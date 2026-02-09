<script setup>
import { Activity, Flame, Clock, ExternalLink } from 'lucide-vue-next';
import Card from '@/Components/molecules/Card.vue';
import CardContent from '@/Components/molecules/CardContent.vue';
import Badge from '@/Components/atoms/Badge.vue';
import Button from '@/Components/atoms/Button.vue';
import Progress from '@/Components/atoms/Progress.vue';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            steps: { current: 0, target: 10000 },
            calories: { current: 0, target: 600 },
            exercise: { current: 0, target: 30 },
            heartRate: { current: 0, status: 'Resting' },
            synced: true,
        }),
    },
});
</script>

<template>
    <Card>
        <CardContent class="p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <Activity class="size-5 text-gray-900" />
                    <h3 class="text-base font-semibold">Fitness Tracker</h3>
                </div>
                <Badge
                    v-if="stats.synced"
                    variant="secondary"
                    class="bg-green-100 text-green-700 text-xs"
                >
                    Synced
                </Badge>
            </div>

            <!-- Today's Stats -->
            <div class="space-y-4">
                <!-- Steps -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div
                                class="size-8 rounded-lg bg-blue-100 flex items-center justify-center"
                            >
                                <Activity class="size-4 text-blue-600" />
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Steps</p>
                                <p class="text-sm font-bold">
                                    {{ stats.steps?.current?.toLocaleString() || 0 }}
                                </p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">
                            / {{ stats.steps?.target?.toLocaleString() || 10000 }}
                        </span>
                    </div>
                    <Progress
                        :value="
                            stats.steps?.target
                                ? (stats.steps.current / stats.steps.target) * 100
                                : 0
                        "
                        class="h-1.5"
                    />
                </div>

                <!-- Active Calories -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div
                                class="size-8 rounded-lg bg-orange-100 flex items-center justify-center"
                            >
                                <Flame class="size-4 text-orange-600" />
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Active Calories</p>
                                <p class="text-sm font-bold">
                                    {{ stats.calories?.current || 0 }} kcal
                                </p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">
                            / {{ stats.calories?.target || 600 }}
                        </span>
                    </div>
                    <Progress
                        :value="
                            stats.calories?.target
                                ? (stats.calories.current / stats.calories.target) * 100
                                : 0
                        "
                        class="h-1.5"
                    />
                </div>

                <!-- Exercise Minutes -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div
                                class="size-8 rounded-lg bg-green-100 flex items-center justify-center"
                            >
                                <Clock class="size-4 text-green-600" />
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Exercise</p>
                                <p class="text-sm font-bold">
                                    {{ stats.exercise?.current || 0 }} min
                                </p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">
                            / {{ stats.exercise?.target || 30 }}
                        </span>
                    </div>
                    <Progress
                        :value="
                            stats.exercise?.target
                                ? (stats.exercise.current / stats.exercise.target) * 100
                                : 0
                        "
                        class="h-1.5"
                    />
                </div>

                <!-- Heart Rate -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-2">
                            <div
                                class="size-8 rounded-lg bg-red-100 flex items-center justify-center"
                            >
                                <Activity class="size-4 text-red-600" />
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Avg Heart Rate</p>
                                <p class="text-sm font-bold">
                                    {{ stats.heartRate?.current || 0 }} bpm
                                </p>
                            </div>
                        </div>
                        <Badge v-if="stats.heartRate?.status" variant="outline" class="text-xs">
                            {{ stats.heartRate.status }}
                        </Badge>
                    </div>
                </div>
            </div>

            <!-- View Details Link -->
            <Button variant="ghost" size="sm" class="w-full mt-4 text-xs">
                <ExternalLink class="size-3 mr-1" />
                View Full Health Data
            </Button>
        </CardContent>
    </Card>
</template>
