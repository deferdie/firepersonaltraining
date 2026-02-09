<script setup>
import { CalendarDays, Clock } from 'lucide-vue-next';
import Card from '@/Components/molecules/Card.vue';
import CardContent from '@/Components/molecules/CardContent.vue';
import CardHeader from '@/Components/molecules/CardHeader.vue';
import Badge from '@/Components/atoms/Badge.vue';

const props = defineProps({
    upcomingSessions: {
        type: Array,
        default: () => [],
    },
    calendarEvents: {
        type: Array,
        default: () => [],
    },
});

const formatDate = (dateStr) => {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
};
</script>

<template>
    <div class="space-y-6">
        <!-- Upcoming Sessions -->
        <Card>
            <CardHeader>
                <div class="flex items-center gap-2">
                    <CalendarDays class="size-5 text-gray-900" />
                    <h2 class="text-lg font-semibold">Upcoming Sessions</h2>
                </div>
            </CardHeader>
            <CardContent>
                <div v-if="upcomingSessions.length > 0" class="space-y-3">
                    <div
                        v-for="(session, index) in upcomingSessions"
                        :key="index"
                        class="flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:shadow-sm transition-shadow"
                    >
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                <Clock class="size-5 text-gray-600" />
                            </div>
                            <div>
                                <p class="font-semibold">{{ session.type }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ formatDate(session.date) }} at {{ session.time }}
                                </p>
                            </div>
                        </div>
                        <Badge
                            :variant="session.status === 'confirmed' ? 'default' : 'secondary'"
                            :class="[
                                session.status === 'confirmed'
                                    ? 'bg-gray-800 text-white'
                                    : 'bg-gray-100 text-gray-600',
                            ]"
                        >
                            {{ session.status }}
                        </Badge>
                    </div>
                </div>
                <div v-else class="text-center py-12 text-gray-500">
                    <CalendarDays class="size-12 mx-auto mb-3 opacity-50" />
                    <p>No upcoming sessions scheduled</p>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
