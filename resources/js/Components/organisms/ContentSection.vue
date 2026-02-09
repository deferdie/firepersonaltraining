<script setup>
import { ref } from 'vue';
import {
    Dumbbell,
    Clipboard,
    BookOpen,
    Target,
    ListChecks,
    Utensils,
    Plus,
    Flame,
} from 'lucide-vue-next';
import Card from '@/Components/molecules/Card.vue';
import CardContent from '@/Components/molecules/CardContent.vue';
import CardHeader from '@/Components/molecules/CardHeader.vue';
import Badge from '@/Components/atoms/Badge.vue';
import Button from '@/Components/atoms/Button.vue';
import Progress from '@/Components/atoms/Progress.vue';

const props = defineProps({
    assignedContent: {
        type: Array,
        default: () => [],
    },
});

const activeCategory = ref('all');

const categories = [
    {
        id: 'programs',
        name: 'Programs',
        icon: Dumbbell,
        description: 'Training programs designed to achieve specific goals',
    },
    {
        id: 'assessments',
        name: 'Assessments',
        icon: Clipboard,
        description: 'Built-in, or custom assessments to help you better understand your client\'s needs',
    },
    {
        id: 'content',
        name: 'Content',
        icon: BookOpen,
        description: 'Educational materials, videos, and PDFs',
    },
    {
        id: 'goals',
        name: 'Goals',
        icon: Target,
        description: 'Trackable goals and milestones',
    },
    {
        id: 'habits',
        name: 'Habits & Tracking',
        icon: ListChecks,
        description: 'Build long-term results by improving nutrition, training and lifestyle habits',
    },
    {
        id: 'nutrition',
        name: 'Nutrition',
        icon: Utensils,
        description: 'Meal plans, macro targets, and nutrition guidance',
    },
];

const visibleCategories =
    activeCategory.value === 'all'
        ? categories
        : categories.filter((cat) => cat.id === activeCategory.value);

const getCategoryItems = (categoryId) => {
    return props.assignedContent.filter((item) => item.category === categoryId);
};

const formatDate = (dateStr) => {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};
</script>

<template>
    <div class="space-y-6">
        <!-- Sub-navigation -->
        <Card>
            <CardHeader>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold">Assigned Content</h2>
                    <Button size="sm">
                        <Plus class="size-4 mr-1" />
                        Assign
                    </Button>
                </div>

                <!-- Category Filter Tabs -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-hide">
                    <button
                        @click="activeCategory = 'all'"
                        :class="[
                            'flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all whitespace-nowrap',
                            activeCategory === 'all'
                                ? 'bg-gray-900 text-white shadow-sm'
                                : 'bg-gray-50 text-gray-600 hover:bg-gray-100',
                        ]"
                    >
                        All
                        <span
                            :class="[
                                'text-xs px-1.5 py-0.5 rounded',
                                activeCategory === 'all'
                                    ? 'bg-gray-700 text-gray-100'
                                    : 'bg-gray-200 text-gray-600',
                            ]"
                        >
                            {{ assignedContent.length }}
                        </span>
                    </button>

                    <button
                        v-for="category in categories"
                        :key="category.id"
                        @click="activeCategory = category.id"
                        :class="[
                            'flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all whitespace-nowrap',
                            activeCategory === category.id
                                ? 'bg-gray-900 text-white shadow-sm'
                                : 'bg-gray-50 text-gray-600 hover:bg-gray-100',
                        ]"
                    >
                        <component :is="category.icon" class="size-4" />
                        {{ category.name }}
                        <span
                            :class="[
                                'text-xs px-1.5 py-0.5 rounded',
                                activeCategory === category.id
                                    ? 'bg-gray-700 text-gray-100'
                                    : 'bg-gray-200 text-gray-600',
                            ]"
                        >
                            {{ getCategoryItems(category.id).length }}
                        </span>
                    </button>
                </div>
            </CardHeader>
        </Card>

        <!-- Category Sections -->
        <div class="space-y-6">
            <Card
                v-for="category in visibleCategories"
                :key="category.id"
            >
                <!-- Category Header - Only show if viewing all -->
                <div v-if="activeCategory === 'all'" class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div
                                class="size-10 bg-white rounded-lg flex items-center justify-center shrink-0 shadow-sm"
                            >
                                <component :is="category.icon" class="size-5 text-gray-700" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-base mb-1">{{ category.name }}</h3>
                                <p class="text-sm text-gray-500">{{ category.description }}</p>
                            </div>
                        </div>
                        <Button variant="ghost" size="sm" class="shrink-0">
                            <Plus class="size-4 mr-1" />
                            Add
                        </Button>
                    </div>
                </div>

                <!-- Category Items -->
                <CardContent>
                    <div v-if="getCategoryItems(category.id).length > 0" class="space-y-3">
                        <div
                            v-for="item in getCategoryItems(category.id)"
                            :key="item.id"
                            class="flex items-start gap-4 p-4 rounded-lg border border-gray-200 hover:border-gray-300 hover:shadow-sm transition-all cursor-pointer group"
                        >
                            <component
                                :is="category.icon"
                                class="size-5 text-gray-500 mt-0.5 shrink-0 group-hover:text-gray-700 transition-colors"
                            />

                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <div class="flex-1">
                                        <p class="font-semibold text-sm mb-1">{{ item.name }}</p>
                                        <p class="text-xs text-gray-500">{{ item.description }}</p>
                                    </div>
                                    <Badge
                                        :variant="
                                            item.status === 'completed'
                                                ? 'default'
                                                : item.status === 'active'
                                                ? 'outline'
                                                : 'secondary'
                                        "
                                        :class="[
                                            'text-xs shrink-0',
                                            item.status === 'completed'
                                                ? 'bg-gray-800 text-white'
                                                : item.status === 'active'
                                                ? 'border-gray-800 text-gray-800'
                                                : 'bg-gray-100 text-gray-600',
                                        ]"
                                    >
                                        {{ item.status }}
                                    </Badge>
                                </div>

                                <!-- Progress Bar (for items with progress) -->
                                <div v-if="item.progress !== undefined" class="mb-2">
                                    <div class="flex items-center justify-between text-xs mb-1">
                                        <span class="text-gray-500">Progress</span>
                                        <span class="font-semibold text-gray-900">{{ item.progress }}%</span>
                                    </div>
                                    <Progress :value="item.progress" />
                                </div>

                                <!-- Streak Badge (for habits) -->
                                <div v-if="item.streak !== undefined" class="flex items-center gap-1.5 mb-2">
                                    <Flame class="size-3.5 text-orange-500" />
                                    <span class="text-xs font-semibold text-gray-900">{{ item.streak }} day streak</span>
                                </div>

                                <!-- Metadata -->
                                <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                                    <span>Assigned {{ formatDate(item.assignedDate) }}</span>
                                    <span v-if="item.dueDate">• Due {{ formatDate(item.dueDate) }}</span>
                                    <span v-if="item.completedDate" class="text-gray-800 font-medium">
                                        • Completed {{ formatDate(item.completedDate) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-12">
                        <div
                            class="size-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4"
                        >
                            <component :is="category.icon" class="size-8 text-gray-400" />
                        </div>
                        <p class="text-base font-semibold text-gray-900 mb-1">
                            No {{ category.name.toLowerCase() }} yet
                        </p>
                        <p class="text-sm text-gray-500 mb-4">{{ category.description }}</p>
                        <Button size="sm">
                            <Plus class="size-4 mr-1" />
                            Add {{ category.name === 'Habits & Tracking' ? 'Habit' : category.name.slice(0, -1) }}
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

<style scoped>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
