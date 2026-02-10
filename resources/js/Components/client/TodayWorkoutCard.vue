<script setup>
import { Clock, Dumbbell, Play } from 'lucide-vue-next';
import Button from '@/Components/atoms/Button.vue';
import Badge from '@/Components/atoms/Badge.vue';

const props = defineProps({
    workout: {
        type: Object,
        required: true,
    },
    badgeLabel: {
        type: String,
        default: "Today's Workout",
    },
});
</script>

<template>
    <div class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-3xl p-6 text-white shadow-xl">
        <div class="flex items-start justify-between mb-4">
            <Badge class="bg-white/20 text-white border-white/30 backdrop-blur-sm">
                {{ badgeLabel }}
            </Badge>
            <Clock class="size-5 opacity-80" />
        </div>

        <h2 class="text-2xl font-bold mb-2">{{ workout.name }}</h2>
        <p class="opacity-90 mb-6">{{ workout.description }}</p>

        <div class="flex items-center gap-6 mb-6 text-sm">
            <div class="flex items-center gap-2">
                <Clock class="size-4" />
                <span>{{ workout.duration }}</span>
            </div>
            <div class="flex items-center gap-2">
                <Dumbbell class="size-4" />
                <span>{{ workout.exercises }} exercises</span>
            </div>
        </div>

        <Button
            class="w-full bg-white text-blue-600 hover:bg-gray-100 font-semibold py-6 rounded-2xl shadow-lg"
            size="lg"
        >
            <Play class="size-5 mr-2 fill-current" />
            Start Workout
        </Button>
    </div>
</template>

