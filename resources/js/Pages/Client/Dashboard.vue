<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    Calendar,
    CheckCircle2,
    Play,
    Clock,
    Dumbbell,
    ChevronRight,
    Trophy,
    Zap,
    Heart,
    BarChart3,
} from 'lucide-vue-next';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import Button from '@/Components/atoms/Button.vue';
import Badge from '@/Components/atoms/Badge.vue';
import Progress from '@/Components/atoms/Progress.vue';
import Avatar from '@/Components/atoms/Avatar.vue';

const props = defineProps({
    client: Object,
    stats: Object,
    todayWorkout: Object,
    upcomingWorkouts: Array,
    programProgress: Object,
    recentAchievements: Array,
    conversations: {
        type: Array,
        default: () => [],
    },
    messages: Array,
    unreadMessagesCount: Number,
    conversationId: Number,
    selectedConversationId: Number,
    initialTab: {
        type: String,
        default: 'home',
    },
});

const page = usePage();
const activeTab = ref(props.initialTab || 'home');
const localUnreadCount = ref(props.unreadMessagesCount ?? 0);
watch(() => props.unreadMessagesCount, (v) => { localUnreadCount.value = v ?? 0; }, { immediate: true });

const layoutStats = computed(() => ({
    streak: props.stats?.streak ?? 0,
    workoutsCount: props.stats?.workoutsCount ?? 0,
    goalPercent: props.stats?.goalPercent ?? 0,
}));

const profileName = computed(() => props.client?.name ?? page.props.auth?.user?.name ?? 'User');
const profileInitials = computed(() => {
    const name = profileName.value;
    return name.split(' ').map((n) => n[0]).join('').toUpperCase().slice(0, 2);
});

const handleLogout = () => {
    router.post(route('client.logout'));
};
</script>

<template>
    <Head title="Dashboard" />

    <ClientLayout
        :stats="layoutStats"
        :unread-messages-count="localUnreadCount"
        v-model:active-tab="activeTab"
    >
        <!-- Home View -->
        <div v-if="activeTab === 'home'" class="space-y-6">
            <!-- Today's Workout - Hero Card -->
            <div
                v-if="todayWorkout"
                class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-3xl p-6 text-white shadow-xl"
            >
                <div class="flex items-start justify-between mb-4">
                    <Badge class="bg-white/20 text-white border-white/30 backdrop-blur-sm">
                        Today's Workout
                    </Badge>
                    <Clock class="size-5 opacity-80" />
                </div>

                <h2 class="text-2xl font-bold mb-2">{{ todayWorkout.name }}</h2>
                <p class="opacity-90 mb-6">{{ todayWorkout.description }}</p>

                <div class="flex items-center gap-6 mb-6 text-sm">
                    <div class="flex items-center gap-2">
                        <Clock class="size-4" />
                        <span>{{ todayWorkout.duration }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <Dumbbell class="size-4" />
                        <span>{{ todayWorkout.exercises }} exercises</span>
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

            <!-- Program Progress -->
            <div
                v-if="programProgress"
                class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100"
            >
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-lg">{{ programProgress.name }}</h3>
                    <span class="text-sm text-gray-500">{{ programProgress.weekLabel }}</span>
                </div>

                <Progress :value="programProgress.percent" class="h-3 mb-4" />

                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-3 border border-green-100">
                        <p class="text-2xl font-bold text-green-700 mb-1">{{ programProgress.completedWorkouts }}/{{ programProgress.totalWorkouts }}</p>
                        <p class="text-xs text-green-600">Completed</p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-3 border border-blue-100">
                        <p class="text-2xl font-bold text-blue-700 mb-1">{{ programProgress.trainingHours }}h</p>
                        <p class="text-xs text-blue-600">Training</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-3 border border-purple-100">
                        <p class="text-2xl font-bold text-purple-700 mb-1">{{ programProgress.weightChange ?? '-' }}</p>
                        <p class="text-xs text-purple-600">Progress</p>
                    </div>
                </div>
            </div>

            <!-- Recent Achievement -->
            <div
                v-if="recentAchievements?.length"
                class="bg-gradient-to-br from-yellow-400 to-orange-500 rounded-3xl p-6 text-white shadow-xl"
            >
                <div class="flex items-center gap-2 mb-3">
                    <Trophy class="size-5" />
                    <span class="font-semibold">Latest Achievement</span>
                </div>

                <div class="flex items-center gap-4">
                    <div class="size-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center text-4xl">
                        {{ recentAchievements[0].icon ?? '🔥' }}
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-xl mb-1">{{ recentAchievements[0].title }}</h3>
                        <p class="text-sm opacity-90">{{ recentAchievements[0].description }}</p>
                    </div>
                </div>
            </div>

            <!-- Upcoming Workouts -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-lg">Coming Up</h3>
                </div>

                <div v-if="upcomingWorkouts?.length" class="space-y-3">
                    <div
                        v-for="workout in upcomingWorkouts"
                        :key="workout.id"
                        class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl hover:bg-gray-100 transition-colors cursor-pointer"
                    >
                        <div class="size-12 bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl flex items-center justify-center text-white">
                            <Dumbbell class="size-6" />
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold mb-1">{{ workout.name }}</h4>
                            <p class="text-sm text-gray-500">{{ workout.date }} • {{ workout.time }}</p>
                        </div>
                        <ChevronRight class="size-5 text-gray-400" />
                    </div>
                </div>
                <p v-else class="text-sm text-gray-500 py-4">No upcoming workouts</p>
            </div>
        </div>

        <!-- Workouts View -->
        <div v-if="activeTab === 'workouts'" class="space-y-6">
            <h2 class="text-2xl font-bold px-2">Your Workouts</h2>

            <!-- Today's Workout -->
            <div
                v-if="todayWorkout"
                class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-3xl p-6 text-white shadow-xl"
            >
                <Badge class="bg-white/20 text-white border-white/30 backdrop-blur-sm mb-4">
                    Today
                </Badge>

                <h3 class="text-2xl font-bold mb-2">{{ todayWorkout.name }}</h3>
                <p class="opacity-90 mb-6">{{ todayWorkout.description }}</p>

                <div class="flex items-center gap-6 mb-6 text-sm">
                    <div class="flex items-center gap-2">
                        <Clock class="size-4" />
                        <span>{{ todayWorkout.duration }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <Dumbbell class="size-4" />
                        <span>{{ todayWorkout.exercises }} exercises</span>
                    </div>
                    <div v-if="todayWorkout.scheduledTime" class="flex items-center gap-2">
                        <Calendar class="size-4" />
                        <span>{{ todayWorkout.scheduledTime }}</span>
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

            <!-- Upcoming -->
            <div class="space-y-3">
                <h3 class="font-bold text-lg px-2">This Week</h3>
                <div
                    v-for="workout in upcomingWorkouts"
                    :key="workout.id"
                    class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow cursor-pointer"
                >
                    <div class="flex items-start gap-4">
                        <div class="size-14 bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl flex items-center justify-center text-white shrink-0">
                            <Dumbbell class="size-7" />
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold mb-1">{{ workout.name }}</h4>
                            <p class="text-sm text-gray-500 mb-3">{{ workout.date }} • {{ workout.time }}</p>
                            <Badge v-if="workout.type" variant="default" class="text-xs">
                                {{ workout.type }}
                            </Badge>
                        </div>
                        <ChevronRight class="size-5 text-gray-400" />
                    </div>
                </div>
                <div v-if="!upcomingWorkouts?.length" class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <p class="text-sm text-gray-500">No workouts scheduled this week</p>
                </div>
            </div>
        </div>

        <!-- Progress View -->
        <div v-if="activeTab === 'progress'" class="space-y-6">
            <h2 class="text-2xl font-bold px-2">Your Progress</h2>

            <!-- Weekly Stats -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <h3 class="font-bold text-lg mb-4">This Week</h3>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-4 border border-blue-100">
                        <Dumbbell class="size-6 text-blue-600 mb-2" />
                        <p class="text-3xl font-bold text-blue-900 mb-1">{{ stats?.weekWorkouts ?? 0 }}</p>
                        <p class="text-sm text-blue-700">Workouts</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-4 border border-green-100">
                        <Clock class="size-6 text-green-600 mb-2" />
                        <p class="text-3xl font-bold text-green-900 mb-1">{{ stats?.weekTrainingHours ?? '0' }}h</p>
                        <p class="text-sm text-green-700">Training Time</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-4 border border-purple-100">
                        <Heart class="size-6 text-purple-600 mb-2" />
                        <p class="text-3xl font-bold text-purple-900 mb-1">{{ stats?.avgHeartRate ?? '-' }}</p>
                        <p class="text-sm text-purple-700">Avg Heart Rate</p>
                    </div>
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl p-4 border border-orange-100">
                        <Zap class="size-6 text-orange-600 mb-2" />
                        <p class="text-3xl font-bold text-orange-900 mb-1">{{ stats?.caloriesBurned ?? '-' }}</p>
                        <p class="text-sm text-orange-700">Calories Burned</p>
                    </div>
                </div>
            </div>

            <!-- Goals -->
            <div v-if="stats?.goals?.length" class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <h3 class="font-bold text-lg mb-4">Active Goals</h3>

                <div class="space-y-4">
                    <div v-for="goal in stats.goals" :key="goal.id">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold">{{ goal.name }}</span>
                            <span class="text-sm font-bold text-gray-900">{{ goal.percent }}%</span>
                        </div>
                        <Progress :value="goal.percent" class="h-2 mb-2" />
                        <p class="text-sm text-gray-500">{{ goal.detail }}</p>
                    </div>
                </div>
            </div>

            <!-- Achievements -->
            <div v-if="recentAchievements?.length" class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <h3 class="font-bold text-lg mb-4">Recent Achievements</h3>

                <div class="space-y-3">
                    <div
                        v-for="achievement in recentAchievements"
                        :key="achievement.id"
                        :class="['flex items-center gap-4 p-4 rounded-2xl text-white', `bg-gradient-to-r ${achievement.color ?? 'from-orange-400 to-red-500'}`]"
                    >
                        <div class="size-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-2xl">
                            {{ achievement.icon ?? '🔥' }}
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold mb-1">{{ achievement.title }}</h4>
                            <p class="text-sm opacity-90">{{ achievement.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile View -->
        <div v-if="activeTab === 'profile'" class="space-y-6">
            <h2 class="text-2xl font-bold px-2">Profile</h2>

            <!-- Profile Header -->
            <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-3xl p-6 text-white shadow-xl">
                <div class="flex flex-col items-center text-center">
                    <Avatar
                        :initials="profileInitials"
                        color="bg-gradient-to-br from-blue-500 to-purple-600"
                        size="xl"
                    />
                    <h3 class="text-2xl font-bold mt-4 mb-1">{{ profileName }}</h3>
                    <p class="opacity-80 mb-4">Member since {{ client?.memberSince ?? '2024' }}</p>
                    <Link :href="route('client.profile.edit')">
                        <Button variant="outline" class="bg-white/10 border-white/30 text-white hover:bg-white/20 rounded-2xl">
                            Edit Profile
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Stats Summary -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <h3 class="font-bold text-lg mb-4">Your Stats</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <span class="text-gray-600">Total Workouts</span>
                        <span class="font-bold text-xl">{{ stats?.workoutsCount ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <span class="text-gray-600">Training Hours</span>
                        <span class="font-bold text-xl">{{ stats?.trainingHours ?? '0' }}h</span>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <span class="text-gray-600">Current Streak</span>
                        <span class="font-bold text-xl">{{ stats?.streak ?? 0 }} days</span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <span class="text-gray-600">Achievements</span>
                        <span class="font-bold text-xl">{{ recentAchievements?.length ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <h3 class="font-bold text-lg mb-4">Settings</h3>
                <div class="space-y-2">
                    <button class="w-full flex items-center justify-between p-4 bg-gray-50 rounded-2xl hover:bg-gray-100 transition-colors">
                        <span class="font-medium">Notifications</span>
                        <ChevronRight class="size-5 text-gray-400" />
                    </button>
                    <button class="w-full flex items-center justify-between p-4 bg-gray-50 rounded-2xl hover:bg-gray-100 transition-colors">
                        <span class="font-medium">Privacy</span>
                        <ChevronRight class="size-5 text-gray-400" />
                    </button>
                    <button class="w-full flex items-center justify-between p-4 bg-gray-50 rounded-2xl hover:bg-gray-100 transition-colors">
                        <span class="font-medium">Help & Support</span>
                        <ChevronRight class="size-5 text-gray-400" />
                    </button>
                    <button
                        class="w-full flex items-center justify-between p-4 bg-gray-50 rounded-2xl hover:bg-gray-100 transition-colors text-red-600"
                        @click="handleLogout"
                    >
                        <span class="font-medium">Logout</span>
                        <ChevronRight class="size-5" />
                    </button>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>
