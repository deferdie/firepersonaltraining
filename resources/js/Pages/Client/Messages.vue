<script setup>
import { ref, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import ClientMesenger from '@/Components/client/ClientMesenger.vue';

const props = defineProps({
    client: Object,
    stats: Object,
    layoutStats: {
        type: Object,
        default: () => ({
            streak: 0,
            workoutsCount: 0,
            goalPercent: 0,
        }),
    },
    conversations: {
        type: Array,
        default: () => [],
    },
    messages: {
        type: Array,
        default: () => [],
    },
    conversationId: Number,
    selectedConversationId: Number,
    unreadMessagesCount: {
        type: Number,
        default: 0,
    },
});

const localUnreadCount = ref(props.unreadMessagesCount ?? 0);
watch(() => props.unreadMessagesCount, (v) => { localUnreadCount.value = v ?? 0; }, { immediate: true });

const onUnreadDecremented = (delta) => {
    localUnreadCount.value = Math.max(0, (localUnreadCount.value ?? 0) - (delta ?? 0));
};
</script>

<template>
    <Head title="Messages" />

    <ClientLayout
        :stats="layoutStats"
        :unread-messages-count="localUnreadCount"
        active-tab="chat"
    >
        <ClientMesenger
            :conversations="conversations"
            :messages="messages"
            :conversation-id="conversationId"
            :selected-conversation-id="selectedConversationId"
            @unread-decremented="onUnreadDecremented"
        />
    </ClientLayout>
</template>
