<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import TrainerLayout from '@/Layouts/TrainerLayout.vue';
import {
    Dumbbell,
    Video,
    File,
    ClipboardCheck,
    Heart,
    ArrowRight,
    Sparkles,
    FileInput,
    Utensils,
} from 'lucide-vue-next';
import FeatureHighlight from '@/Components/molecules/FeatureHighlight.vue';
import Button from '@/Components/atoms/Button.vue';

const props = defineProps({
    counts: {
        type: Object,
        default: () => ({}),
    },
    stats: {
        type: Object,
        default: () => ({ createdThisMonth: 0 }),
    },
});

const libraryCategories = computed(() => [
    {
        id: 'programs',
        name: 'Programs',
        description: 'Training programs designed to achieve specific goals',
        icon: Dumbbell,
        count: props.counts.programs ?? 0,
        routeName: 'trainer.library.programs.index',
        color: 'text-blue-600',
        bgColor: 'bg-blue-100',
    },
    {
        id: 'exercises',
        name: 'Exercises',
        description: 'Exercise library with detailed instructions',
        icon: Dumbbell,
        count: props.counts.exercises ?? 0,
        routeName: 'trainer.library.exercises.index',
        color: 'text-purple-600',
        bgColor: 'bg-purple-100',
    },
    {
        id: 'forms',
        name: 'Forms',
        description: 'Custom forms like PAR-Q and intake questionnaires',
        icon: FileInput,
        count: props.counts.forms ?? 0,
        routeName: 'trainer.library.forms.index',
        color: 'text-teal-600',
        bgColor: 'bg-teal-100',
    },
    {
        id: 'assessments',
        name: 'Assessments',
        description: 'Forms and assessments to track client progress',
        icon: ClipboardCheck,
        count: props.counts.assessments ?? 0,
        routeName: 'trainer.library.assessments.index',
        color: 'text-green-600',
        bgColor: 'bg-green-100',
    },
    {
        id: 'videos',
        name: 'Videos',
        description: 'Educational and instructional video content',
        icon: Video,
        count: props.counts.videos ?? 0,
        routeName: 'trainer.library.videos.index',
        color: 'text-red-600',
        bgColor: 'bg-red-100',
    },
    {
        id: 'documents',
        name: 'Documents',
        description: 'PDFs, guides, and educational materials',
        icon: File,
        count: props.counts.documents ?? 0,
        routeName: 'trainer.library.documents.index',
        color: 'text-orange-600',
        bgColor: 'bg-orange-100',
    },
    {
        id: 'habits',
        name: 'Habits',
        description: 'Trackable habits for building healthy routines',
        icon: Heart,
        count: props.counts.habits ?? 0,
        routeName: 'trainer.library.habits.index',
        color: 'text-pink-600',
        bgColor: 'bg-pink-100',
    },
    {
        id: 'meal-plans',
        name: 'Meal Plans',
        description: 'Weekly meal plans for nutrition and dietary goals',
        icon: Utensils,
        count: props.counts.meal_plans ?? 0,
        routeName: 'trainer.library.meal-plans.index',
        color: 'text-yellow-600',
        bgColor: 'bg-yellow-100',
    },
]);

function navigateTo(routeName) {
    router.visit(route(routeName));
}
</script>

<template>
    <TrainerLayout title="Library" description="Create and organize all your training content in one place">
        <!-- AI Feature Highlight -->
        <FeatureHighlight title="AI-Powered Content Creation"
            description="Our AI can help you generate personalized programs, exercises, and assessments tailored to your clients' needs. Save time and deliver better results."
            class="mb-8">
            <template #icon>
                <Sparkles class="size-6 text-gray-700" />
            </template>
            <Button size="sm" variant="outline">
                Learn More
            </Button>
        </FeatureHighlight>

        <!-- Library Categories Grid -->
        <div>
            <h2 class="text-xl font-semibold mb-4">Browse by Category</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <button v-for="category in libraryCategories" :key="category.id" type="button"
                    class="group bg-white border border-gray-200 rounded-xl p-6 hover:shadow-lg hover:border-gray-300 transition-all text-left"
                    @click="navigateTo(category.routeName)">
                    <div class="flex items-start justify-between mb-4">
                        <div :class="['size-14 rounded-xl flex items-center justify-center', category.bgColor]">
                            <component :is="category.icon" :class="['size-7', category.color]" />
                        </div>
                        <ArrowRight
                            class="size-5 text-gray-400 group-hover:text-gray-700 group-hover:translate-x-1 transition-all" />
                    </div>

                    <h3 class="font-semibold text-lg mb-2 group-hover:text-gray-900">
                        {{ category.name }}
                    </h3>
                    <p class="text-sm text-gray-500 mb-4 line-clamp-2">
                        {{ category.description }}
                    </p>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <span class="text-2xl font-bold text-gray-900">{{ category.count }}</span>
                        <span class="text-xs text-gray-500">items</span>
                    </div>
                </button>
            </div>
        </div>
    </TrainerLayout>
</template>
