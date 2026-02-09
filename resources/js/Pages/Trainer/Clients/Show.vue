<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import TrainerLayout from '@/Layouts/TrainerLayout.vue';
import {
    Send,
    LogIn,
    KeyRound,
    Mail,
    Trash2,
    Flame,
    Dumbbell,
    TrendingUp,
    Clock,
    Sparkles,
    Activity,
    Target,
    CalendarDays,
    Clipboard,
    Utensils,
    DollarSign,
    StickyNote,
    Camera,
} from 'lucide-vue-next';
import Avatar from '@/Components/atoms/Avatar.vue';
import Badge from '@/Components/atoms/Badge.vue';
import Button from '@/Components/atoms/Button.vue';
import Card from '@/Components/molecules/Card.vue';
import CardContent from '@/Components/molecules/CardContent.vue';
import Tabs from '@/Components/molecules/Tabs.vue';
import ActivitySection from '@/Components/organisms/ActivitySection.vue';
import GoalsSection from '@/Components/organisms/GoalsSection.vue';
import ScheduleSection from '@/Components/organisms/ScheduleSection.vue';
import ContentSection from '@/Components/organisms/ContentSection.vue';
import FoodJournalSection from '@/Components/organisms/FoodJournalSection.vue';
import PaymentsSection from '@/Components/organisms/PaymentsSection.vue';
import NotesSection from '@/Components/organisms/NotesSection.vue';
import ProgressPhotosSection from '@/Components/organisms/ProgressPhotosSection.vue';

const props = defineProps({
    client: Object,
    stats: Object,
    recentActivity: Array,
    weeklyActivity: Array,
    foodEntries: Object,
    progressPhotos: Array,
    notes: Array,
    goals: Array,
    payments: Array,
    aiInsights: Array,
    smartActions: Array,
    assignedContent: Array,
    upcomingSessions: Array,
    calendarEvents: Array,
    recentMessages: Array,
});

const activeSection = ref('activity');

const tabs = [
    { id: 'activity', label: 'Activity', icon: Activity },
    { id: 'goals', label: 'Goals', icon: Target },
    { id: 'schedule', label: 'Schedule', icon: CalendarDays },
    { id: 'content', label: 'Content', icon: Clipboard },
    { id: 'food', label: 'Food', icon: Utensils },
    { id: 'payments', label: 'Payments', icon: DollarSign },
    { id: 'notes', label: 'Notes', icon: StickyNote },
    { id: 'photos', label: 'Photos', icon: Camera },
];
</script>

<template>
    <Head :title="`${client.name} - Client Profile`" />

    <TrainerLayout>
        <div class="max-w-7xl mx-auto pb-8">
            <!-- Compact Client Info -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <Avatar
                            :initials="client.initials"
                            :color="client.color"
                            size="xl"
                        />
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <h1 class="text-2xl font-bold">{{ client.name }}</h1>
                                <Badge class="bg-gray-800 text-white text-xs">
                                    {{ client.status.charAt(0).toUpperCase() + client.status.slice(1) }}
                                </Badge>
                            </div>
                            <p class="text-sm text-gray-500">
                                {{ client.email }} • Last active {{ client.lastActive }}
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <Link
                            v-if="!client.has_completed_signup"
                            :href="route('trainer.clients.send-signup-invitation', client.id)"
                            method="post"
                            as="button"
                            class="inline-flex items-center justify-center rounded-md border border-transparent bg-gray-900 px-3 py-1.5 text-sm font-medium text-white shadow hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                        >
                            <Mail class="size-4 mr-2" />
                            Request signup
                        </Link>
                        <Button size="sm">
                            <Send class="size-4 mr-2" />
                            Message
                        </Button>
                        <Button
                            v-if="client.has_completed_signup"
                            variant="outline"
                            size="sm"
                        >
                            <LogIn class="size-4 mr-2" />
                            Login to Client Dashboard
                        </Button>
                        <Button variant="outline" size="sm">
                            <KeyRound class="size-4 mr-2" />
                            Reset Password
                        </Button>
                        <Button
                            variant="outline"
                            size="sm"
                            class="text-red-600 hover:text-red-700 hover:border-red-600"
                        >
                            <Trash2 class="size-4 mr-2" />
                            Delete
                        </Button>
                    </div>
                </div>

                <!-- Key Metrics Bar - Compact -->
                <div class="grid grid-cols-4 gap-3 mb-6">
                    <Card>
                        <CardContent class="p-3">
                            <div class="flex items-center gap-2 mb-1">
                                <Flame class="size-4 text-orange-500" />
                                <span class="text-xs text-gray-500">Streak</span>
                            </div>
                            <p class="text-2xl font-bold">{{ stats.streak }}</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent class="p-3">
                            <div class="flex items-center gap-2 mb-1">
                                <Dumbbell class="size-4 text-blue-600" />
                                <span class="text-xs text-gray-500">Completed</span>
                            </div>
                            <p class="text-2xl font-bold">
                                {{ stats.workoutsCompleted }}/{{ stats.workoutsTotal }}
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent class="p-3">
                            <div class="flex items-center gap-2 mb-1">
                                <TrendingUp class="size-4 text-green-600" />
                                <span class="text-xs text-gray-500">Adherence</span>
                            </div>
                            <p class="text-2xl font-bold">{{ stats.adherence }}%</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent class="p-3">
                            <div class="flex items-center gap-2 mb-1">
                                <Clock class="size-4 text-purple-600" />
                                <span class="text-xs text-gray-500">Avg Session</span>
                            </div>
                            <p class="text-2xl font-bold">{{ stats.avgDuration }}m</p>
                        </CardContent>
                    </Card>
                </div>

                <!-- Top AI Insight - Single Most Important -->
                <div
                    v-if="aiInsights && aiInsights.length > 0"
                    class="bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-200 rounded-lg p-4 mb-6"
                >
                    <div class="flex items-center gap-3">
                        <div class="size-10 bg-white rounded-lg flex items-center justify-center shrink-0">
                            <Sparkles class="size-5 text-gray-700" />
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-sm mb-1">{{ aiInsights[0].title }}</p>
                            <p class="text-sm text-gray-600">{{ aiInsights[0].message }}</p>
                        </div>
                        <Button size="sm" variant="outline">
                            {{ aiInsights[0].action }}
                        </Button>
                    </div>
                </div>

                <!-- Subnavigation -->
                <Tabs v-model="activeSection" :tabs="tabs" />
            </div>

            <!-- Content Area - Based on Active Section -->
            <div class="mt-6">
                <ActivitySection
                    v-if="activeSection === 'activity'"
                    :client="client"
                    :recent-activity="recentActivity"
                    :weekly-activity="weeklyActivity"
                    :smart-actions="smartActions"
                    :recent-messages="recentMessages"
                />
                <GoalsSection
                    v-if="activeSection === 'goals'"
                    :goals="goals"
                />
                <ScheduleSection
                    v-if="activeSection === 'schedule'"
                    :upcoming-sessions="upcomingSessions"
                    :calendar-events="calendarEvents"
                />
                <ContentSection
                    v-if="activeSection === 'content'"
                    :assigned-content="assignedContent"
                />
                <FoodJournalSection
                    v-if="activeSection === 'food'"
                    :food-entries="foodEntries"
                />
                <PaymentsSection
                    v-if="activeSection === 'payments'"
                    :payments="payments"
                />
                <NotesSection
                    v-if="activeSection === 'notes'"
                    :notes="notes"
                    :client-id="client.id"
                />
                <ProgressPhotosSection
                    v-if="activeSection === 'photos'"
                    :progress-photos="progressPhotos"
                />
            </div>
        </div>
    </TrainerLayout>
</template>
