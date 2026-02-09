<script setup>
import { ArrowRight, Zap } from 'lucide-vue-next';
import Card from '@/Components/molecules/Card.vue';
import CardContent from '@/Components/molecules/CardContent.vue';
import CardHeader from '@/Components/molecules/CardHeader.vue';
import MiniMessenger from '@/Components/organisms/MiniMessenger.vue';
import FitnessTrackerCard from '@/Components/molecules/FitnessTrackerCard.vue';
import WeeklyActivity from '@/Components/molecules/WeeklyActivity.vue';
import RecentActivityCard from '@/Components/molecules/RecentActivityCard.vue';

const props = defineProps({
    client: {
        type: Object,
        required: true,
    },
    recentActivity: {
        type: Array,
        default: () => [],
    },
    weeklyActivity: {
        type: Array,
        default: () => [],
    },
    smartActions: {
        type: Array,
        default: () => [],
    },
    recentMessages: {
        type: Array,
        default: () => [],
    },
});

</script>

<template>
    <div class="grid grid-cols-12 gap-6">
        <!-- Left Column - Activity Feed (Larger) -->
        <div class="col-span-8">
            <RecentActivityCard :activities="recentActivity" variant="detailed" />
        </div>

        <!-- Right Column - This Week & Quick Actions -->
        <div class="col-span-4 space-y-6">
            <!-- Fitness Tracker Stats -->
            <FitnessTrackerCard
                :stats="{
                    steps: { current: 8247, target: 10000 },
                    calories: { current: 412, target: 600 },
                    exercise: { current: 28, target: 30 },
                    heartRate: { current: 68, status: 'Resting' },
                    synced: true,
                }"
            />

            <!-- Mini Messenger -->
            <MiniMessenger
                :messages="recentMessages"
                :client-name="client.name"
                :client-initials="client.initials"
                :client-color="client.color"
            />

            <!-- This Week -->
            <Card>
                <CardHeader class="pb-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold">This Week</h3>
                        <span class="text-xs text-gray-500">
                            {{
                                weeklyActivity.filter((d) => d.completed).length
                            }}/{{ weeklyActivity.filter((d) => d.completed || d.missed).length }}
                        </span>
                    </div>
                </CardHeader>
                <CardContent>
                    <WeeklyActivity :weekly-activity="weeklyActivity" />
                </CardContent>
            </Card>

            <!-- Quick Actions -->
            <Card v-if="smartActions && smartActions.length > 0">
                <CardHeader class="pb-3">
                    <h3 class="text-base font-semibold">Quick Actions</h3>
                </CardHeader>
                <CardContent>
                    <div class="space-y-2">
                        <button
                            v-for="(action, index) in smartActions"
                            :key="index"
                            class="w-full flex items-center gap-3 p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors text-left group"
                        >
                            <div
                                class="size-8 bg-white rounded-lg flex items-center justify-center shadow-sm group-hover:shadow transition-shadow"
                            >
                                <component :is="action.icon" class="size-4 text-gray-700" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate">{{ action.label }}</p>
                                <div v-if="action.automate" class="flex items-center gap-1 mt-0.5">
                                    <Zap class="size-3 text-gray-600" />
                                    <span class="text-xs text-gray-500">AI-powered</span>
                                </div>
                            </div>
                            <ArrowRight class="size-4 text-gray-400 group-hover:text-gray-700 transition-colors" />
                        </button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
