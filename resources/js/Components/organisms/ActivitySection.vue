<script setup>
import {
    Dumbbell,
    MessageSquare,
    FileText,
    Sparkles,
    Brain,
    ArrowRight,
    Zap,
    Plus,
    StickyNote,
    Camera,
    Utensils,
    Target,
} from 'lucide-vue-next';
import Card from '@/Components/molecules/Card.vue';
import CardContent from '@/Components/molecules/CardContent.vue';
import CardHeader from '@/Components/molecules/CardHeader.vue';
import Badge from '@/Components/atoms/Badge.vue';
import Button from '@/Components/atoms/Button.vue';
import MiniMessenger from '@/Components/organisms/MiniMessenger.vue';
import FitnessTrackerCard from '@/Components/molecules/FitnessTrackerCard.vue';
import WeeklyActivity from '@/Components/molecules/WeeklyActivity.vue';

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

const getActivityIcon = (type) => {
    switch (type) {
        case 'workout':
        case 'workout_completion':
            return Dumbbell;
        case 'message':
            return MessageSquare;
        case 'trainer_note':
            return StickyNote;
        case 'progress_photo':
            return Camera;
        case 'food_entry':
            return Utensils;
        case 'goal_achievement':
            return Target;
        case 'form':
            return FileText;
        default:
            return FileText;
    }
};

const getActivityColor = (type) => {
    switch (type) {
        case 'workout':
        case 'workout_completion':
            return 'bg-blue-100 text-blue-600';
        case 'message':
            return 'bg-purple-100 text-purple-600';
        case 'trainer_note':
            return 'bg-yellow-100 text-yellow-600';
        case 'progress_photo':
            return 'bg-pink-100 text-pink-600';
        case 'food_entry':
            return 'bg-green-100 text-green-600';
        case 'goal_achievement':
            return 'bg-indigo-100 text-indigo-600';
        default:
            return 'bg-gray-100 text-gray-600';
    }
};

const formatDate = (dateStr) => {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
};
</script>

<template>
    <div class="grid grid-cols-12 gap-6">
        <!-- Left Column - Activity Feed (Larger) -->
        <div class="col-span-8">
            <Card>
                <CardHeader>
                    <h2 class="text-lg font-semibold">Recent Activity</h2>
                </CardHeader>
                <CardContent>
                    <div v-if="recentActivity.length > 0" class="space-y-6">
                        <div
                            v-for="(activity, index) in recentActivity"
                            :key="index"
                            class="relative"
                        >
                            <div class="flex gap-4">
                                <!-- Icon -->
                                <div class="relative shrink-0">
                                    <div
                                        :class="[
                                            'size-10 rounded-lg flex items-center justify-center shadow-sm',
                                            getActivityColor(activity.type),
                                        ]"
                                    >
                                        <component
                                            :is="getActivityIcon(activity.type)"
                                            class="size-5"
                                        />
                                    </div>
                                    <div
                                        v-if="index !== recentActivity.length - 1"
                                        class="absolute top-12 left-1/2 -translate-x-1/2 w-0.5 h-full bg-gray-200"
                                    />
                                </div>

                                <!-- Content -->
                                <div class="flex-1 pb-6">
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <div>
                                            <h3 class="font-semibold">{{ activity.title }}</h3>
                                            <p class="text-sm text-gray-500">
                                                {{ formatDate(activity.date) }} at {{ activity.time }}
                                            </p>
                                        </div>
                                        <Badge v-if="activity.duration" variant="outline" class="shrink-0">
                                            {{ activity.duration }}
                                        </Badge>
                                    </div>

                                    <!-- Workout Details -->
                                    <div
                                        v-if="(activity.type === 'workout' || activity.type === 'workout_completion') && activity.exercises"
                                        class="bg-gray-50 rounded-lg p-4 mb-3 space-y-2"
                                    >
                                        <div
                                            v-for="(exercise, idx) in activity.exercises"
                                            :key="idx"
                                            class="flex items-center justify-between text-sm"
                                        >
                                            <span class="font-medium">{{ exercise.name }}</span>
                                            <div class="flex items-center gap-2">
                                                <span class="text-gray-500">
                                                    <span v-if="exercise.sets">{{ exercise.sets }} × </span>
                                                    <span v-if="exercise.reps">{{ exercise.reps }}</span>
                                                    <span v-if="exercise.weight"> @ {{ exercise.weight }}</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Trainer Note -->
                                    <div v-if="activity.type === 'trainer_note'" class="space-y-2">
                                        <div v-if="activity.category" class="mb-2">
                                            <Badge variant="outline" class="text-xs">{{ activity.category }}</Badge>
                                        </div>
                                        <p class="text-sm text-gray-600 bg-gray-50 rounded-lg p-3">
                                            {{ activity.content }}
                                        </p>
                                    </div>

                                    <!-- Progress Photo -->
                                    <div v-if="activity.type === 'progress_photo'" class="space-y-2">
                                        <div v-if="activity.photo_url" class="bg-gray-50 rounded-lg p-3">
                                            <img
                                                :src="activity.photo_url"
                                                :alt="`Progress photo - ${activity.angle || 'Front'}`"
                                                class="w-full rounded-lg max-h-64 object-cover"
                                            />
                                        </div>
                                        <div v-if="activity.angle || activity.weight" class="flex items-center gap-2 text-sm text-gray-600">
                                            <span v-if="activity.angle">Angle: {{ activity.angle }}</span>
                                            <span v-if="activity.weight">Weight: {{ activity.weight }} lbs</span>
                                        </div>
                                    </div>

                                    <!-- Food Entry -->
                                    <div v-if="activity.type === 'food_entry'" class="bg-gray-50 rounded-lg p-3">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-medium text-sm">{{ activity.food_name }}</p>
                                                <p v-if="activity.meal_type" class="text-xs text-gray-500">{{ activity.meal_type }}</p>
                                            </div>
                                            <div v-if="activity.calories" class="text-right">
                                                <p class="font-semibold text-sm">{{ activity.calories }}</p>
                                                <p class="text-xs text-gray-500">calories</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Goal Achievement -->
                                    <div v-if="activity.type === 'goal_achievement'" class="bg-indigo-50 border border-indigo-200 rounded-lg p-3">
                                        <p class="font-semibold text-sm text-indigo-900 mb-1">{{ activity.goal_name }}</p>
                                        <p v-if="activity.goal_description" class="text-sm text-indigo-700">{{ activity.goal_description }}</p>
                                    </div>

                                    <!-- Message -->
                                    <div v-if="activity.type === 'message' && activity.message" class="space-y-2">
                                        <p class="text-sm text-gray-600 bg-gray-50 rounded-lg p-3">
                                            {{ activity.message }}
                                        </p>
                                    </div>

                                    <!-- Workout Notes -->
                                    <div v-if="activity.notes && (activity.type === 'workout' || activity.type === 'workout_completion')" class="bg-gray-50 border border-gray-200 rounded-lg p-3 mt-2">
                                        <p class="text-sm text-gray-900">
                                            <span class="font-semibold">Coach Note:</span> {{ activity.notes }}
                                        </p>
                                    </div>

                                    <!-- Performed By -->
                                    <div v-if="activity.performed_by" class="text-xs text-gray-500 mt-2">
                                        By {{ activity.performed_by }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-12 text-gray-500">
                        <FileText class="size-12 mx-auto mb-3 opacity-50" />
                        <p>No recent activity</p>
                    </div>
                </CardContent>
            </Card>
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
