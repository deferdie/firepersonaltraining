<script setup>
import { computed } from 'vue';
import { Utensils, Zap, Plus, CheckCircle2, ExternalLink } from 'lucide-vue-next';
import Card from '@/Components/molecules/Card.vue';
import CardContent from '@/Components/molecules/CardContent.vue';
import CardHeader from '@/Components/molecules/CardHeader.vue';
import Badge from '@/Components/atoms/Badge.vue';
import Button from '@/Components/atoms/Button.vue';

const props = defineProps({
    foodEntries: {
        type: Object,
        default: () => ({}),
    },
});

const formatDate = (dateStr) => {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', {
        weekday: 'long',
        month: 'long',
        day: 'numeric',
        year: 'numeric',
    });
};

const sortedDates = computed(() => {
    return Object.keys(props.foodEntries).sort((a, b) => {
        return new Date(b).getTime() - new Date(a).getTime();
    });
});
</script>

<template>
    <div class="space-y-6">
        <!-- Integrations Card -->
        <Card>
            <CardHeader>
                <div class="flex items-center gap-2">
                    <Zap class="size-5 text-gray-900" />
                    <h2 class="text-lg font-semibold">Food Integrations</h2>
                </div>
            </CardHeader>
            <CardContent>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- MyFitnessPal Integration -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold text-sm"
                                >
                                    MFP
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">MyFitnessPal</h3>
                                    <p class="text-xs text-gray-500">Connected</p>
                                </div>
                            </div>
                            <Badge variant="secondary" class="bg-green-100 text-green-700">
                                <CheckCircle2 class="size-3 mr-1" />
                                Active
                            </Badge>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">
                            Auto-syncs meals, nutrition data, and calorie tracking from MyFitnessPal.
                        </p>
                        <div class="flex gap-2">
                            <Button variant="outline" size="sm" class="flex-1">
                                <ExternalLink class="size-3 mr-1" />
                                View in App
                            </Button>
                            <Button variant="ghost" size="sm">Settings</Button>
                        </div>
                    </div>

                    <!-- Cronometer Integration -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-lg bg-orange-500 flex items-center justify-center text-white font-bold text-sm"
                                >
                                    CR
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Cronometer</h3>
                                    <p class="text-xs text-gray-500">Available</p>
                                </div>
                            </div>
                            <Badge variant="outline">Not Connected</Badge>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">
                            Track micronutrients, vitamins, and detailed nutrition analysis.
                        </p>
                        <Button variant="outline" size="sm" class="w-full">
                            <Plus class="size-3 mr-1" />
                            Connect Integration
                        </Button>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Food Journal Overview -->
        <Card>
            <CardHeader>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <Utensils class="size-5 text-gray-900" />
                        <h2 class="text-lg font-semibold">Food Journal</h2>
                    </div>
                    <Button variant="ghost" size="sm">
                        <Plus class="size-4 mr-1" />
                        Add Entry
                    </Button>
                </div>
            </CardHeader>
            <CardContent>
                <div v-if="sortedDates.length > 0" class="space-y-8">
                    <div v-for="date in sortedDates" :key="date" class="space-y-4">
                        <!-- Date Header -->
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-1 h-8 bg-gray-900 rounded-full" />
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ formatDate(date) }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ foodEntries[date].length }}
                                    {{ foodEntries[date].length === 1 ? 'entry' : 'entries' }}
                                </p>
                            </div>
                        </div>

                        <!-- Entries for this date -->
                        <div class="ml-4 pl-4 border-l-2 border-gray-200 space-y-4">
                            <div
                                v-for="entry in foodEntries[date]"
                                :key="entry.id"
                                class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow"
                            >
                                <!-- Entry Image Placeholder -->
                                <div class="relative h-48 bg-gray-100">
                                    <div
                                        class="absolute inset-0 flex items-center justify-center text-gray-400"
                                    >
                                        <Utensils class="size-12" />
                                    </div>
                                    <div class="absolute top-3 left-3 bg-white px-3 py-1.5 rounded-full shadow-sm">
                                        <span class="text-xs font-semibold text-gray-900">{{ entry.meal }}</span>
                                    </div>
                                    <div class="absolute top-3 right-3 bg-white px-3 py-1.5 rounded-full shadow-sm">
                                        <span class="text-xs font-semibold text-gray-900">{{ entry.time }}</span>
                                    </div>
                                </div>

                                <!-- Entry Details -->
                                <div class="p-4 space-y-3">
                                    <p class="text-sm text-gray-700 leading-relaxed">{{ entry.description }}</p>

                                    <!-- Nutrition Info -->
                                    <div class="grid grid-cols-4 gap-3 pt-3 border-t border-gray-100">
                                        <div class="text-center">
                                            <p class="text-xs text-gray-500 mb-1">Calories</p>
                                            <p class="text-sm font-bold text-gray-900">
                                                {{ entry.calories || '-' }}
                                            </p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-xs text-gray-500 mb-1">Protein</p>
                                            <p class="text-sm font-bold text-gray-900">
                                                {{ entry.protein ? entry.protein + 'g' : '-' }}
                                            </p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-xs text-gray-500 mb-1">Carbs</p>
                                            <p class="text-sm font-bold text-gray-900">
                                                {{ entry.carbs ? entry.carbs + 'g' : '-' }}
                                            </p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-xs text-gray-500 mb-1">Fats</p>
                                            <p class="text-sm font-bold text-gray-900">
                                                {{ entry.fats ? entry.fats + 'g' : '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-12 text-gray-500">
                    <Utensils class="size-12 mx-auto mb-3 opacity-50" />
                    <p>No food entries yet</p>
                    <Button size="sm" class="mt-4">
                        <Plus class="size-4 mr-1" />
                        Add First Entry
                    </Button>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
