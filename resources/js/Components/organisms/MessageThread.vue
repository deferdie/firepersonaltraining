<script setup>
import { ref, watch, nextTick } from 'vue';
import MessageBubble from '@/Components/organisms/MessageBubble.vue';

const props = defineProps({
    messages: {
        type: Array,
        default: () => [],
    },
});

const threadRef = ref(null);

const scrollToBottom = () => {
    nextTick(() => {
        if (threadRef.value) {
            threadRef.value.scrollTop = threadRef.value.scrollHeight;
        }
    });
};

watch(
    () => props.messages.length,
    () => scrollToBottom(),
    { immediate: true }
);
</script>

<template>
    <div
        ref="threadRef"
        class="flex-1 overflow-y-auto p-6 space-y-4"
    >
        <MessageBubble
            v-for="msg in messages"
            :key="msg.id"
            :message="msg"
        />
    </div>
</template>
