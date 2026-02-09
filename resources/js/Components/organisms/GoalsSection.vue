<script setup>
import { Target, Clock, TrendingUp, Plus } from 'lucide-vue-next';
import Card from '@/Components/molecules/Card.vue';
import CardContent from '@/Components/molecules/CardContent.vue';
import CardHeader from '@/Components/molecules/CardHeader.vue';
import Button from '@/Components/atoms/Button.vue';
import Progress from '@/Components/atoms/Progress.vue';

const props = defineProps({
    goals: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <div class="space-y-6">
        <!-- Goals with Progress -->
        <Card>
            <CardHeader>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <Target class="size-5 text-gray-900" />
                        <h2 class="text-lg font-semibold">Goals & Progress</h2>
                    </div>
                    <Button variant="ghost" size="sm">
                        <Plus class="size-4 mr-1" />
                        Add
                    </Button>
                </div>
            </CardHeader>
            <CardContent>
                <div v-if="goals.length > 0" class="space-y-5">
                    <div
                        v-for="(goal, index) in goals"
                        :key="index"
                        class="space-y-3 p-4 rounded-lg border border-gray-200 bg-white hover:shadow-sm transition-shadow"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <p class="font-semibold mb-1">{{ goal.title }}</p>
                                <p class="text-sm text-gray-500 mb-2">
                                    {{ goal.current }} → {{ goal.target }}
                                </p>
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <div class="flex items-center gap-1">
                                        <Clock class="size-3" />
                                        Est. {{ goal.estimatedCompletion }}
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <TrendingUp
                                            :class="[
                                                'size-3',
                                                goal.trend === 'up' ? 'text-gray-700' : 'text-gray-400',
                                            ]"
                                        />
                                        {{ goal.trend === 'up' ? 'Improving' : 'Steady' }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-bold text-gray-900">{{ goal.progress }}%</span>
                            </div>
                        </div>
                        <Progress :value="goal.progress" />
                    </div>
                </div>
                <div v-else class="text-center py-12 text-gray-500">
                    <Target class="size-12 mx-auto mb-3 opacity-50" />
                    <p>No goals set yet</p>
                    <Button size="sm" class="mt-4">
                        <Plus class="size-4 mr-1" />
                        Add First Goal
                    </Button>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
