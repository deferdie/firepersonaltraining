<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import TodayWorkoutCard from '@/Components/client/TodayWorkoutCard.vue';
import ProgramProgressCard from '@/Components/client/ProgramProgressCard.vue';
import RecentAchievementCard from '@/Components/client/RecentAchievementCard.vue';
import UpcomingWorkoutsList from '@/Components/client/UpcomingWorkoutsList.vue';

const props = defineProps({
    client: Object,
    stats: Object,
    todayWorkout: Object,
    upcomingWorkouts: {
        type: Array,
        default: () => [],
    },
    programProgress: Object,
    recentAchievements: {
        type: Array,
        default: () => [],
    },
    layoutStats: {
        type: Object,
        default: () => ({
            streak: 0,
            workoutsCount: 0,
            goalPercent: 0,
        }),
    },
});

const mainAchievement = computed(() =>
    (props.recentAchievements && props.recentAchievements.length > 0)
        ? props.recentAchievements[0]
        : null
);
</script>

<template>
    <Head title="Home" />

    <ClientLayout :stats="layoutStats">
        <div class="space-y-6">
            <TodayWorkoutCard
                v-if="todayWorkout"
                :workout="todayWorkout"
            />

            <ProgramProgressCard
                v-if="programProgress"
                :program-progress="programProgress"
            />

            <RecentAchievementCard
                v-if="mainAchievement"
                :achievement="mainAchievement"
            />

            <UpcomingWorkoutsList :workouts="upcomingWorkouts" />
        </div>
    </ClientLayout>
</template>

