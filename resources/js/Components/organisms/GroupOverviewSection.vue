<script setup>
import {
    Sparkles,
    Plus,
    Dumbbell,
    FileText,
    Video,
    MessageSquare,
    TrendingUp,
    Bell,
    Brain,
} from 'lucide-vue-next';
import Card from '@/Components/molecules/Card.vue';
import CardContent from '@/Components/molecules/CardContent.vue';
import CardHeader from '@/Components/molecules/CardHeader.vue';
import CardTitle from '@/Components/molecules/CardTitle.vue';
import Button from '@/Components/atoms/Button.vue';
import RecentActivityCard from '@/Components/molecules/RecentActivityCard.vue';

defineProps({
    aiInsights: {
        type: Array,
        default: () => [],
    },
    recentActivity: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['add-members', 'message-group', 'insight-action']);

const iconMap = { TrendingUp, Bell, Brain };

const getInsightBorderClass = (type) => {
    switch (type) {
        case 'success': return 'bg-green-50 border-green-500';
        case 'alert': return 'bg-orange-50 border-orange-500';
        case 'suggestion': return 'bg-blue-50 border-blue-500';
        default: return 'bg-gray-50 border-gray-300';
    }
};

const getInsightIconClass = (type) => {
    switch (type) {
        case 'success': return 'text-green-600';
        case 'alert': return 'text-orange-600';
        case 'suggestion': return 'text-blue-600';
        default: return 'text-gray-600';
    }
};
</script>

<template>
    <div class="space-y-6">
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-8 space-y-6">
                <!-- AI Insights -->
                <Card v-if="aiInsights && aiInsights.length > 1">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Sparkles class="size-5 text-purple-600" />
                            AI Insights
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div
                            v-for="(insight, index) in aiInsights"
                            :key="index"
                            :class="['p-4 rounded-lg border-l-4', getInsightBorderClass(insight.type)]"
                        >
                            <div class="flex items-start gap-3">
                                <component
                                    :is="iconMap[insight.icon] || Sparkles"
                                    :class="['size-5 shrink-0 mt-0.5', getInsightIconClass(insight.type)]"
                                />
                                <div class="flex-1">
                                    <h4 class="font-semibold mb-1">{{ insight.title }}</h4>
                                    <p class="text-sm text-gray-600 mb-3">{{ insight.message }}</p>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        @click="emit('insight-action', insight)"
                                    >
                                        {{ insight.action }}
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent Activity -->
                <RecentActivityCard :activities="recentActivity || []" variant="simple" />
            </div>

            <!-- Quick Actions -->
            <div class="col-span-4">
                <Card>
                    <CardHeader>
                        <h3 class="text-base font-semibold">Quick Actions</h3>
                    </CardHeader>
                    <CardContent class="space-y-2">
                        <Button
                            variant="outline"
                            class="w-full justify-start"
                            @click="emit('add-members')"
                        >
                            <Plus class="size-4 mr-2" />
                            Add Members
                        </Button>
                        <Button variant="outline" class="w-full justify-start">
                            <Dumbbell class="size-4 mr-2" />
                            Assign Program
                        </Button>
                        <Button variant="outline" class="w-full justify-start">
                            <FileText class="size-4 mr-2" />
                            Assign Document
                        </Button>
                        <Button variant="outline" class="w-full justify-start">
                            <Video class="size-4 mr-2" />
                            Assign Video
                        </Button>
                        <Button
                            variant="outline"
                            class="w-full justify-start"
                            @click="emit('message-group')"
                        >
                            <MessageSquare class="size-4 mr-2" />
                            Send Group Message
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
