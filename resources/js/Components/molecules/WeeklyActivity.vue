<script setup>
import { CheckCircle2, AlertCircle, Clock } from 'lucide-vue-next';

const props = defineProps({
    weeklyActivity: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div class="space-y-2">
        <div
            v-for="(day, index) in weeklyActivity"
            :key="index"
            class="flex items-center gap-3"
        >
            <div class="w-8 text-center">
                <p class="text-xs font-medium text-gray-500">{{ day.day }}</p>
            </div>
            <div class="flex-1">
                <div
                    :class="[
                        'h-10 rounded-lg flex items-center px-3 gap-2 transition-all',
                        day.completed
                            ? 'bg-gray-800 text-white'
                            : day.missed
                            ? 'bg-gray-300 text-gray-600'
                            : day.upcoming
                            ? 'bg-gray-100 border-2 border-gray-400'
                            : 'bg-gray-50 border border-gray-200',
                    ]"
                >
                    <CheckCircle2 v-if="day.completed" class="size-4" />
                    <AlertCircle v-else-if="day.missed" class="size-4" />
                    <Clock v-else-if="day.upcoming" class="size-4" />
                    <span class="text-xs font-medium">
                        {{
                            day.completed
                                ? 'Completed'
                                : day.missed
                                ? 'Missed'
                                : day.upcoming
                                ? 'Today'
                                : day.date
                        }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
