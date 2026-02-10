<script setup>
import { computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import {
    Home,
    Dumbbell,
    BarChart3,
    MessageSquare,
    User,
    Flame,
    Target,
    LogOut,
} from 'lucide-vue-next';
import Avatar from '@/Components/atoms/Avatar.vue';

const page = usePage();

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            streak: 0,
            workoutsCount: 0,
            goalPercent: 0,
        }),
    },
    unreadMessagesCount: {
        type: Number,
        default: 0,
    },
    activeTab: {
        type: String,
        default: 'home',
        validator: (v) => ['home', 'workouts', 'progress', 'chat', 'profile'].includes(v),
    },
});

const emit = defineEmits(['update:activeTab']);

const user = computed(() => page.props.auth?.user ?? null);
const impersonation = computed(() => page.props.impersonation ?? null);

const userName = computed(() => user.value?.name ?? 'There');

const returnToTrainer = () => {
    router.post(route('client.stop-impersonation'));
};

const initials = computed(() => {
    const name = userName.value;
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
});

const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Good morning';
    if (hour < 17) return 'Good afternoon';
    return 'Good evening';
});

const navItems = [
    { id: 'home', label: 'Home', icon: Home },
    { id: 'workouts', label: 'Workouts', icon: Dumbbell },
    { id: 'progress', label: 'Progress', icon: BarChart3 },
    { id: 'chat', label: 'Chat', icon: MessageSquare },
    { id: 'profile', label: 'Profile', icon: User },
];

const tabRoutes = {
    home: 'client.home',
    workouts: 'client.workouts',
    progress: 'client.progress',
    chat: 'client.messages.index',
    profile: 'client.profile.index',
};

const selectTab = (tabId) => {
    const routeName = tabRoutes[tabId];
    if (routeName && tabId !== props.activeTab) {
        router.visit(route(routeName), { preserveState: false });
    }
    emit('update:activeTab', tabId);
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
        <!-- Impersonation banner -->
        <div
            v-if="impersonation?.active"
            class="bg-amber-500 text-amber-950 px-4 py-2 flex items-center justify-between gap-3"
        >
            <span class="text-sm font-medium">
                Viewing as client. Logged in as {{ impersonation.trainer_name }}.
            </span>
            <button
                type="button"
                class="inline-flex items-center gap-1.5 rounded-md bg-amber-900/20 px-3 py-1.5 text-sm font-medium hover:bg-amber-900/30 focus:outline-none focus:ring-2 focus:ring-amber-700 focus:ring-offset-1"
                @click="returnToTrainer"
            >
                <LogOut class="size-4" />
                Return to trainer dashboard
            </button>
        </div>

        <!-- Mobile App Header -->
        <div class="bg-gradient-to-r from-gray-800 to-gray-900 text-white px-6 pt-8 pb-6 rounded-b-3xl shadow-lg">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-sm opacity-80">{{ greeting }}</p>
                    <h1 class="text-2xl font-bold">{{ userName }}</h1>
                </div>
                <Avatar
                    :initials="initials"
                    color="bg-gradient-to-br from-blue-500 to-purple-600"
                    size="xl"
                />
            </div>

            <!-- Stats Row -->
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-3 text-center">
                    <Flame class="size-5 mx-auto mb-1 text-orange-400" />
                    <p class="text-2xl font-bold">{{ stats.streak }}</p>
                    <p class="text-xs opacity-80">Day Streak</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-3 text-center">
                    <Dumbbell class="size-5 mx-auto mb-1 text-blue-400" />
                    <p class="text-2xl font-bold">{{ stats.workoutsCount }}</p>
                    <p class="text-xs opacity-80">Workouts</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-3 text-center">
                    <Target class="size-5 mx-auto mb-1 text-green-400" />
                    <p class="text-2xl font-bold">{{ stats.goalPercent }}%</p>
                    <p class="text-xs opacity-80">Goal</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="px-4 py-6 pb-24">
            <slot />
        </main>

        <!-- Bottom Navigation - Mobile App Style -->
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-2xl rounded-t-3xl z-50">
            <div class="grid grid-cols-5 gap-1 px-2 py-3 max-w-2xl mx-auto">
                <button
                    v-for="item in navItems"
                    :key="item.id"
                    type="button"
                    @click="selectTab(item.id)"
                    :class="[
                        'flex flex-col items-center gap-1 py-2 px-3 rounded-xl transition-all relative',
                        activeTab === item.id
                            ? 'bg-gray-900 text-white shadow-lg'
                            : 'text-gray-500 hover:text-gray-900',
                    ]"
                >
                    <span v-if="item.id === 'chat' && unreadMessagesCount > 0" class="absolute -top-1 -right-1 size-5 bg-red-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                        {{ unreadMessagesCount > 9 ? '9+' : unreadMessagesCount }}
                    </span>
                    <component :is="item.icon" class="size-5" />
                    <span class="text-xs font-medium">{{ item.label }}</span>
                </button>
            </div>
        </div>
    </div>
</template>
