<script setup>
import { Phone, Video, Search, MoreVertical } from 'lucide-vue-next';
import Button from '@/Components/atoms/Button.vue';

defineProps({
    conversation: {
        type: Object,
        required: true,
    },
    online: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6">
        <div class="flex items-center gap-3">
            <div
                :class="[
                    'size-10 rounded-full flex items-center justify-center text-white text-sm font-medium shrink-0',
                    conversation.color
                ]"
            >
                {{ conversation.initials }}
            </div>
            <div>
                <p class="font-semibold text-sm">{{ conversation.name }}</p>
                <p class="text-xs text-gray-500">
                    {{ online ? 'Online' : 'Last seen recently' }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <Button variant="ghost" size="icon">
                <Phone class="size-4" />
            </Button>
            <Button variant="ghost" size="icon">
                <Video class="size-4" />
            </Button>
            <Button variant="ghost" size="icon">
                <Search class="size-4" />
            </Button>
            <Button variant="ghost" size="icon">
                <MoreVertical class="size-4" />
            </Button>
        </div>
    </div>
</template>
