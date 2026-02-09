<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import TrainerLayout from '@/Layouts/TrainerLayout.vue';
import { MessageSquare } from 'lucide-vue-next';
import ConversationList from '@/Components/organisms/ConversationList.vue';
import ChatHeader from '@/Components/organisms/ChatHeader.vue';
import MessageThread from '@/Components/organisms/MessageThread.vue';
import MessageComposer from '@/Components/organisms/MessageComposer.vue';

const props = defineProps({
    conversations: {
        type: Array,
        default: () => [],
    },
    selectedConversation: {
        type: Object,
        default: null,
    },
    messages: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    openGroupId: {
        type: Number,
        default: null,
    },
});

const searchQuery = ref(props.filters?.search || '');
const localMessages = ref([...props.messages]);

watch(
    () => props.messages,
    (newMessages) => {
        localMessages.value = [...(newMessages || [])];
    },
    { immediate: true }
);

watch(
    () => props.selectedConversation?.id,
    () => {
        localMessages.value = [...(props.messages || [])];
    }
);

const composingTo = ref(null);
const effectiveSelected = computed(() => composingTo.value || props.selectedConversation);
const selectedId = computed(() => {
    const c = effectiveSelected.value;
    return c?.id ?? (c ? `${c.type}:${c.targetId}` : null);
});

const handleSelectConversation = (conv) => {
    if (conv.id) {
        composingTo.value = null;
        router.visit(route('trainer.messages.show', conv.id), {
            preserveState: false,
        });
    } else {
        composingTo.value = conv;
        localMessages.value = [];
    }
};

const openGroupIfRequested = () => {
    if (!props.openGroupId || !props.conversations?.length) return;
    const groupConv = props.conversations.find(
        (c) => c.type === 'group' && Number(c.targetId) === Number(props.openGroupId)
    );
    if (groupConv) handleSelectConversation(groupConv);
};

const handleSearch = () => {
    router.get(route('trainer.messages.index'), { search: searchQuery.value }, {
        preserveState: true,
        replace: true,
    });
};

watch(searchQuery, () => {
    const timer = setTimeout(handleSearch, 300);
    return () => clearTimeout(timer);
});

const handleSend = (payload) => {
    const conv = effectiveSelected.value;
    if (!conv) return;
    router.post(route('trainer.messages.store'), {
        conversation_id: conv.id || undefined,
        type: conv.type,
        target_id: conv.targetId,
        ...payload,
    }, {
        preserveScroll: true,
        preserveState: false,
    });
};

let echoChannel = null;

const subscribeToConversation = (conversationId) => {
    if (!conversationId || !window.Echo) return;
    try {
        echoChannel = window.Echo.private(`conversation.${conversationId}`);
        echoChannel.listen('.message.sent', (e) => {
            if (e.message && e.conversation_id === conversationId) {
                localMessages.value = [...localMessages.value, e.message];
            }
        });
    } catch (err) {
        // Echo unavailable or not configured - graceful degradation
    }
};

const unsubscribeFromConversation = () => {
    if (echoChannel && window.Echo) {
        try {
            window.Echo.leave(`conversation.${props.selectedConversation?.id}`);
        } catch (err) {}
        echoChannel = null;
    }
};

watch(
    () => props.selectedConversation?.id,
    (newId, oldId) => {
        if (oldId) unsubscribeFromConversation();
        if (newId) subscribeToConversation(newId);
    },
    { immediate: true }
);

onMounted(() => {
    openGroupIfRequested();
});

onUnmounted(() => {
    unsubscribeFromConversation();
});
</script>

<template>
    <TrainerLayout title="Messages">
        <div class="flex h-[calc(100vh-8rem)] bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <ConversationList
                :conversations="conversations"
                :selected-id="selectedId"
                :effective-selected="effectiveSelected"
                v-model:search-query="searchQuery"
                @select="handleSelectConversation"
            />

            <div v-if="effectiveSelected" class="flex-1 flex flex-col bg-gray-50">
                <ChatHeader :conversation="effectiveSelected" />
                <MessageThread :messages="localMessages" />
                <MessageComposer @send="handleSend" />
            </div>

            <div
                v-else
                class="flex-1 flex flex-col items-center justify-center bg-gray-50 text-gray-500"
            >
                <MessageSquare class="size-16 mb-4 opacity-30" />
                <p class="text-lg font-medium">Select a conversation</p>
                <p class="text-sm mt-1">Choose a client or group from the list to start messaging</p>
            </div>
        </div>
    </TrainerLayout>
</template>
