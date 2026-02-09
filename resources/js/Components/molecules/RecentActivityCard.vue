<script setup>
import {
    Dumbbell,
    MessageSquare,
    FileText,
    StickyNote,
    Camera,
    Utensils,
    Target,
} from 'lucide-vue-next';
import Card from '@/Components/molecules/Card.vue';
import CardContent from '@/Components/molecules/CardContent.vue';
import CardHeader from '@/Components/molecules/CardHeader.vue';
import Badge from '@/Components/atoms/Badge.vue';

const props = defineProps({
    activities: {
        type: Array,
        default: () => [],
    },
    /** 'simple' = compact list (e.g. groups), 'detailed' = full cards with type-specific content (e.g. clients) */
    variant: {
        type: String,
        default: 'detailed',
        validator: (v) => ['simple', 'detailed'].includes(v),
    },
    title: {
        type: String,
        default: 'Recent Activity',
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

const actorName = (activity) => activity.member ?? activity.performed_by ?? 'Unknown';
</script>

<template>
    <Card>
        <CardHeader>
            <h2 class="text-lg font-semibold">{{ title }}</h2>
        </CardHeader>
        <CardContent>
            <!-- Simple variant -->
            <div v-if="variant === 'simple'" class="space-y-4">
                <div
                    v-for="(activity, index) in activities"
                    :key="index"
                    class="flex items-start gap-4 pb-4 border-b border-gray-100 last:border-0 last:pb-0"
                >
                    <div
                        :class="[
                            'size-10 rounded-full flex items-center justify-center shrink-0',
                            getActivityColor(activity.type),
                        ]"
                    >
                        <component :is="getActivityIcon(activity.type)" class="size-5" />
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">{{ actorName(activity) }}</p>
                        <p class="text-sm text-gray-500">{{ activity.title }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ activity.date }} at {{ activity.time }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Detailed variant -->
            <div v-else class="space-y-6">
                <div
                    v-for="(activity, index) in activities"
                    :key="index"
                    class="relative"
                >
                    <div class="flex gap-4">
                        <div class="relative shrink-0">
                            <div
                                :class="[
                                    'size-10 rounded-lg flex items-center justify-center shadow-sm',
                                    getActivityColor(activity.type),
                                ]"
                            >
                                <component :is="getActivityIcon(activity.type)" class="size-5" />
                            </div>
                            <div
                                v-if="index !== activities.length - 1"
                                class="absolute top-12 left-1/2 -translate-x-1/2 w-0.5 h-full bg-gray-200"
                            />
                        </div>
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

                            <div v-if="activity.type === 'trainer_note'" class="space-y-2">
                                <div v-if="activity.category" class="mb-2">
                                    <Badge variant="outline" class="text-xs">{{ activity.category }}</Badge>
                                </div>
                                <p class="text-sm text-gray-600 bg-gray-50 rounded-lg p-3">
                                    {{ activity.content }}
                                </p>
                            </div>

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

                            <div v-if="activity.type === 'goal_achievement'" class="bg-indigo-50 border border-indigo-200 rounded-lg p-3">
                                <p class="font-semibold text-sm text-indigo-900 mb-1">{{ activity.goal_name }}</p>
                                <p v-if="activity.goal_description" class="text-sm text-indigo-700">{{ activity.goal_description }}</p>
                            </div>

                            <div v-if="activity.type === 'message' && activity.message" class="space-y-2">
                                <p class="text-sm text-gray-600 bg-gray-50 rounded-lg p-3">
                                    {{ activity.message }}
                                </p>
                            </div>

                            <div v-if="activity.notes && (activity.type === 'workout' || activity.type === 'workout_completion')" class="bg-gray-50 border border-gray-200 rounded-lg p-3 mt-2">
                                <p class="text-sm text-gray-900">
                                    <span class="font-semibold">Coach Note:</span> {{ activity.notes }}
                                </p>
                            </div>

                            <div v-if="activity.performed_by" class="text-xs text-gray-500 mt-2">
                                By {{ activity.performed_by }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="!activities?.length" class="text-center py-12 text-gray-500">
                <FileText class="size-12 mx-auto mb-3 opacity-50" />
                <p>No recent activity</p>
            </div>
        </CardContent>
    </Card>
</template>
