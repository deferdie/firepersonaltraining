<script setup>
import { ChevronRight, Dumbbell } from 'lucide-vue-next';

const props = defineProps({
    workouts: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg">Coming Up</h3>
        </div>

        <div v-if="workouts?.length" class="space-y-3">
            <div
                v-for="workout in workouts"
                :key="workout.id"
                class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl hover:bg-gray-100 transition-colors cursor-pointer"
            >
                <div
                    class="size-12 bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl flex items-center justify-center text-white"
                >
                    <Dumbbell class="size-6" />
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold mb-1">{{ workout.name }}</h4>
                    <p class="text-sm text-gray-500">
                        {{ workout.date }} • {{ workout.time }}
                    </p>
                </div>
                <ChevronRight class="size-5 text-gray-400" />
            </div>
        </div>
        <p v-else class="text-sm text-gray-500 py-4">No upcoming workouts</p>
    </div>
</template>

