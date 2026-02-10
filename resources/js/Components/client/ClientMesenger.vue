<script setup>
import { ref, computed, watch, onUnmounted } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';
import Avatar from '@/Components/atoms/Avatar.vue';
import MessageBubble from '@/Components/organisms/MessageBubble.vue';
import MessageComposer from '@/Components/organisms/MessageComposer.vue';

const props = defineProps({
    conversations: {
        type: Array,
        default: () => [],
    },
    messages: {
        type: Array,
        default: () => [],
    },
    conversationId: {
        type: Number,
        default: null,
    },
    selectedConversationId: {
        type: Number,
        default: null,
    },
    messagesIndexRoute: {
        type: String,
        default: 'client.messages.index',
    },
    storeRoute: {
        type: String,
        default: 'client.messages.store',
    },
    markReadRoute: {
        type: String,
        default: 'client.conversations.mark-read',
    },
});

const emit = defineEmits(['unread-decremented']);

const selectedConv = computed(() =>
    props.conversations?.find((c) => c.id === props.selectedConversationId) ?? props.conversations?.[0]
);

const handleSelectConversation = (conv) => {
    router.visit(route(props.messagesIndexRoute, { conversation: conv.id }), {
        preserveState: false,
    });
};

const handleSendMessage = (payload) => {
    if (!props.conversationId) return;
    const optimistic = {
        id: 'temp-' + Date.now(),
        sender: 'client',
        type: payload.payload_type || 'text',
        message: payload.body || '',
        content: payload.payload,
        timestamp: new Date().toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' }),
        read: false,
    };
    localMessages.value = [...localMessages.value, optimistic];
    router.post(route(props.storeRoute), {
        conversation_id: props.conversationId,
        ...payload,
    }, {
        preserveScroll: true,
        preserveState: false,
    });
};

const localMessages = ref([...(props.messages || [])]);
watch(() => props.messages, (m) => { localMessages.value = [...(m || [])]; }, { immediate: true });

let echoChannel = null;
const subscribeToConversation = (id) => {
    if (!id || !window.Echo) return;
    try {
        echoChannel = window.Echo.private(`conversation.${id}`);
        echoChannel.listen('.message.sent', (e) => {
            if (e.message && e.conversation_id === id) {
                localMessages.value = [...localMessages.value, e.message];
            }
        });
    } catch (err) {}
};
const unsubscribeFromConversation = () => {
    if (echoChannel && window.Echo && props.conversationId) {
        try { window.Echo.leave(`conversation.${props.conversationId}`); } catch (err) {}
        echoChannel = null;
    }
};
watch(() => props.conversationId, () => {
    if (props.conversationId) {
        subscribeToConversation(props.conversationId);
        axios.post(route(props.markReadRoute, { conversation: props.conversationId })).then(() => {
            const convUnread = selectedConv.value?.unreadCount ?? 0;
            emit('unread-decremented', convUnread);
        }).catch(() => {});
    } else {
        unsubscribeFromConversation();
    }
}, { immediate: true });
onUnmounted(unsubscribeFromConversation);
</script>

<template>
    <div class="space-y-4">
        <h2 class="text-2xl font-bold px-2">Messages</h2>

        <!-- Conversation switcher -->
        <div v-if="conversations?.length > 1" class="flex gap-2 overflow-x-auto pb-2">
            <button
                v-for="conv in conversations"
                :key="conv.id"
                type="button"
                :class="[
                    'shrink-0 flex items-center gap-2 px-4 py-2 rounded-2xl border transition-colors',
                    selectedConversationId === conv.id
                        ? 'bg-gray-900 text-white border-gray-900'
                        : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'
                ]"
                @click="handleSelectConversation(conv)"
            >
                <div
                    :class="[
                        'size-8 rounded-full flex items-center justify-center text-xs font-medium text-white',
                        conv.color || 'bg-gray-600'
                    ]"
                >
                    {{ conv.initials }}
                </div>
                <span class="font-medium truncate max-w-[120px]">{{ conv.name }}</span>
                <span
                    v-if="conv.unreadCount > 0"
                    class="size-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center font-bold"
                >
                    {{ conv.unreadCount > 9 ? '9+' : conv.unreadCount }}
                </span>
            </button>
        </div>

        <!-- Selected conversation header -->
        <div v-if="selectedConv" class="bg-white rounded-3xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <Avatar
                    :initials="selectedConv.initials ?? '?'"
                    :color="selectedConv.color || 'bg-gradient-to-br from-gray-700 to-gray-900'"
                    size="lg"
                />
                <div class="flex-1">
                    <h3 class="font-bold text-lg">{{ selectedConv.name }}</h3>
                    <p class="text-sm text-gray-500">
                        {{ selectedConv.type === 'group' ? 'Group chat' : 'Usually replies within an hour' }}
                    </p>
                </div>
                <div v-if="selectedConv.type === 'client'" class="size-3 bg-green-500 rounded-full"></div>
            </div>
        </div>

        <!-- Messages -->
        <div class="space-y-4 bg-white rounded-3xl p-5 shadow-sm border border-gray-100 min-h-[300px] overflow-y-auto max-h-[400px]">
            <template v-if="localMessages?.length">
                <MessageBubble
                    v-for="msg in localMessages"
                    :key="msg.id"
                    :message="msg"
                    viewer-role="client"
                />
            </template>
            <p v-else class="text-sm text-gray-500 py-8 text-center">No messages yet. Say hello to your trainer!</p>
        </div>

        <!-- Message Input -->
        <div v-if="conversationId" class="bg-white rounded-3xl p-4 shadow-sm border border-gray-100">
            <MessageComposer
                :disabled="!conversationId"
                @send="handleSendMessage"
            />
        </div>
    </div>
</template>
