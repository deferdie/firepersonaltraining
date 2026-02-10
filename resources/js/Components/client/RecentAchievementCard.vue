<script setup>
import { Trophy } from 'lucide-vue-next';

const props = defineProps({
    achievement: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="bg-gradient-to-br from-yellow-400 to-orange-500 rounded-3xl p-6 text-white shadow-xl">
        <div class="flex items-center gap-2 mb-3">
            <Trophy class="size-5" />
            <span class="font-semibold">Latest Achievement</span>
        </div>

        <div class="flex items-center gap-4">
            <div
                class="size-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center text-4xl"
            >
                {{ achievement.icon ?? '🔥' }}
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-xl mb-1">{{ achievement.title }}</h3>
                <p class="text-sm opacity-90">{{ achievement.description }}</p>
            </div>
        </div>
    </div>
</template>

