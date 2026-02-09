<script setup>
import { Pin } from 'lucide-vue-next';

defineProps({
    conversation: {
        type: Object,
        required: true,
    },
    isSelected: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['select']);
</script>

<template>
    <button
        type="button"
        class="w-full flex items-center gap-3 p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors text-left"
        :class="{ 'bg-gray-100': isSelected }"
        @click="$emit('select', conversation)"
    >
        <div class="relative shrink-0">
            <slot name="avatar">
                <div
                    :class="[
                        'size-12 rounded-full flex items-center justify-center text-white text-sm font-medium',
                        conversation.color
                    ]"
                >
                    {{ conversation.initials }}
                </div>
            </slot>
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-2 mb-1">
                <div class="flex items-center gap-2">
                    <p class="font-semibold text-sm truncate">{{ conversation.name }}</p>
                    <Pin v-if="conversation.pinned" class="size-3 text-gray-500 shrink-0" />
                </div>
                <span v-if="conversation.timestamp" class="text-xs text-gray-500 shrink-0">{{ conversation.timestamp }}</span>
            </div>
            <div class="flex items-center justify-between gap-2">
                <p class="text-sm text-gray-500 truncate">{{ conversation.lastMessage || 'No messages yet' }}</p>
                <span
                    v-if="conversation.unread > 0"
                    class="shrink-0 flex items-center justify-center size-5 rounded-full bg-gray-800 text-white text-xs font-medium"
                >
                    {{ conversation.unread }}
                </span>
            </div>
        </div>
    </button>
</template>
