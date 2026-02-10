<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
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
import StatCard from '@/Components/molecules/StatCard.vue';
import Tabs from '@/Components/molecules/Tabs.vue';
import ActivitySection from '@/Components/organisms/ActivitySection.vue';
import GoalsSection from '@/Components/organisms/GoalsSection.vue';
import ScheduleSection from '@/Components/organisms/ScheduleSection.vue';
import ContentSection from '@/Components/organisms/ContentSection.vue';
import FoodJournalSection from '@/Components/organisms/FoodJournalSection.vue';
import PaymentsSection from '@/Components/organisms/PaymentsSection.vue';
import NotesSection from '@/Components/organisms/NotesSection.vue';
import ProgressPhotosSection from '@/Components/organisms/ProgressPhotosSection.vue';
import ConfirmModal from '@/Components/molecules/ConfirmModal.vue';

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
const showResetPasswordModal = ref(false);
const resetPasswordProcessing = ref(false);
const showDeleteModal = ref(false);
const deleteProcessing = ref(false);

const openResetPasswordModal = () => {
    showResetPasswordModal.value = true;
};

const closeResetPasswordModal = () => {
    if (!resetPasswordProcessing.value) {
        showResetPasswordModal.value = false;
    }
};

const confirmResetPassword = () => {
    resetPasswordProcessing.value = true;
    router.post(route('trainer.clients.reset-password', props.client.id), {}, {
        preserveScroll: true,
        onFinish: () => {
            resetPasswordProcessing.value = false;
            showResetPasswordModal.value = false;
        },
    });
};

const openDeleteModal = () => {
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    if (!deleteProcessing.value) {
        showDeleteModal.value = false;
    }
};

const confirmDelete = () => {
    deleteProcessing.value = true;
    router.delete(route('trainer.clients.destroy', props.client.id), {
        onFinish: () => {
            deleteProcessing.value = false;
            showDeleteModal.value = false;
        },
    });
};

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
    <TrainerLayout :title="`${client.name} - Client Profile`" :show-header="false">
        <div>
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
                        <Link
                            v-if="client.has_completed_signup"
                            :href="route('trainer.clients.impersonate', client.id)"
                            method="post"
                            as="button"
                            class="inline-flex items-center justify-center rounded-md font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 hover:border-gray-400 focus:ring-gray-500 px-3 py-1.5 text-sm gap-2 h-9"
                        >
                            <LogIn class="size-4 mr-2" />
                            Login to Client Dashboard
                        </Link>
                        <Button
                            v-if="client.has_completed_signup"
                            variant="outline"
                            size="sm"
                            @click="openResetPasswordModal"
                        >
                            <KeyRound class="size-4 mr-2" />
                            Reset Password
                        </Button>
                        <Button
                            variant="outline"
                            size="sm"
                            class="text-red-600 hover:text-red-700 hover:border-red-600"
                            @click="openDeleteModal"
                        >
                            <Trash2 class="size-4 mr-2" />
                            Delete
                        </Button>
                    </div>
                </div>

                <!-- Key Metrics Bar - Compact -->
                <div class="grid grid-cols-4 gap-3 mb-6">
                    <StatCard
                        title="Streak"
                        :value="stats.streak"
                        :icon="Flame"
                    />
                    <StatCard
                        title="Completed"
                        :value="`${stats.workoutsCompleted}/${stats.workoutsTotal}`"
                        :icon="Dumbbell"
                    />
                    <StatCard
                        title="Adherence"
                        :value="`${stats.adherence}%`"
                        :icon="TrendingUp"
                    />
                    <StatCard
                        title="Avg Session"
                        :value="`${stats.avgDuration}m`"
                        :icon="Clock"
                    />
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

            <ConfirmModal
                :is-open="showResetPasswordModal"
                title="Reset password"
                description="A new password will be generated and sent to this client by email. They can use it to sign in to their client dashboard."
                confirm-label="Send new password"
                :processing="resetPasswordProcessing"
                @close="closeResetPasswordModal"
                @confirm="confirmResetPassword"
            />
            <ConfirmModal
                :is-open="showDeleteModal"
                title="Delete client"
                :description="`Remove ${client.name} and all associated data (programs, workouts, food entries, notes, photos, schedules, and habits)? This cannot be undone.`"
                confirm-label="Delete"
                confirm-variant="danger"
                :processing="deleteProcessing"
                @close="closeDeleteModal"
                @confirm="confirmDelete"
            />

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
                    :client-id="client.id"
                    :upcoming-sessions="upcomingSessions"
                    :calendar-events="calendarEvents"
                />
                <ContentSection
                    v-if="activeSection === 'content'"
                    :assigned-content="assignedContent"
                    assignable-type="client"
                    :assignable-id="client.id"
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
        
    </TrainerLayout>
</template>
