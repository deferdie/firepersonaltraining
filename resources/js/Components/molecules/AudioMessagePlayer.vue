<script setup>
import { ref } from 'vue';
import { Play, Pause } from 'lucide-vue-next';
import Button from '@/Components/atoms/Button.vue';

defineProps({
    duration: {
        type: String,
        default: '0:00',
    },
    isTrainer: {
        type: Boolean,
        default: false,
    },
});

const isPlaying = ref(false);
const progress = ref(0);
let intervalId = null;

const togglePlay = () => {
    isPlaying.value = !isPlaying.value;
    if (isPlaying.value) {
        let currentProgress = 0;
        intervalId = setInterval(() => {
            currentProgress += 5;
            progress.value = currentProgress;
            if (currentProgress >= 100) {
                clearInterval(intervalId);
                isPlaying.value = false;
                progress.value = 0;
            }
        }, 100);
    } else if (intervalId) {
        clearInterval(intervalId);
    }
};
</script>

<template>
    <div class="flex items-center gap-3 w-full">
        <Button
            variant="ghost"
            size="icon"
            :class="[
                'shrink-0 size-8',
                isTrainer ? 'hover:bg-gray-600 text-white' : 'hover:bg-gray-100'
            ]"
            @click="togglePlay"
        >
            <Pause v-if="isPlaying" class="size-4" />
            <Play v-else class="size-4" />
        </Button>
        <div class="flex-1 relative">
            <div
                :class="[
                    'h-1 rounded-full',
                    isTrainer ? 'bg-gray-600' : 'bg-gray-200'
                ]"
            >
                <div
                    :class="[
                        'h-full rounded-full transition-all',
                        isTrainer ? 'bg-white' : 'bg-gray-800'
                    ]"
                    :style="{ width: `${progress}%` }"
                />
            </div>
        </div>
        <span class="text-xs shrink-0">{{ duration }}</span>
    </div>
</template>
