<script setup>
import Progress from '@/Components/atoms/Progress.vue';

const props = defineProps({
    programProgress: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg">{{ programProgress.name }}</h3>
            <span class="text-sm text-gray-500">{{ programProgress.weekLabel }}</span>
        </div>

        <Progress :value="programProgress.percent" class="h-3 mb-4" />

        <div class="grid grid-cols-3 gap-3">
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-3 border border-green-100">
                <p class="text-2xl font-bold text-green-700 mb-1">
                    {{ programProgress.completedWorkouts }}/{{ programProgress.totalWorkouts }}
                </p>
                <p class="text-xs text-green-600">Completed</p>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-3 border border-blue-100">
                <p class="text-2xl font-bold text-blue-700 mb-1">
                    {{ programProgress.trainingHours }}h
                </p>
                <p class="text-xs text-blue-600">Training</p>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-3 border border-purple-100">
                <p class="text-2xl font-bold text-purple-700 mb-1">
                    {{ programProgress.weightChange ?? '-' }}
                </p>
                <p class="text-xs text-purple-600">Progress</p>
            </div>
        </div>
    </div>
</template>

