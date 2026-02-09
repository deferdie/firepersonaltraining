<script setup>
import { ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import TrainerLayout from '@/Layouts/TrainerLayout.vue';
import {
    MessageSquare,
    Settings,
    MoreVertical,
    UsersRound,
    Users,
    Calendar,
    Activity,
    Dumbbell,
    BarChart3,
    Sparkles,
    Clipboard,
} from 'lucide-vue-next';
import Avatar from '@/Components/atoms/Avatar.vue';
import Badge from '@/Components/atoms/Badge.vue';
import Button from '@/Components/atoms/Button.vue';
import Tabs from '@/Components/molecules/Tabs.vue';
import AddGroupMembersModal from '@/Components/organisms/AddGroupMembersModal.vue';
import GroupOverviewSection from '@/Components/organisms/GroupOverviewSection.vue';
import GroupMembersSection from '@/Components/organisms/GroupMembersSection.vue';
import GroupContentSection from '@/Components/organisms/GroupContentSection.vue';
import GroupActivitySection from '@/Components/organisms/GroupActivitySection.vue';

const props = defineProps({
    group: Object,
    stats: Object,
    members: Array,
    availableClients: {
        type: Array,
        default: () => [],
    },
    recentActivity: Array,
    assignedContent: Array,
    aiInsights: Array,
});

const page = usePage();
const activeSection = ref('overview');
const isAddMembersModalOpen = ref(false);

const tabs = [
    { id: 'overview', label: 'Overview', icon: Activity },
    { id: 'members', label: 'Members', icon: Users },
    { id: 'content', label: 'Assigned Content', icon: Clipboard },
    { id: 'activity', label: 'Activity Feed', icon: BarChart3 },
];

const formatDate = (dateStr) => {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
};

const handleMemberClick = (member) => {
    router.visit(route('trainer.clients.show', member.id));
};

const handleRemoveMember = (member, e) => {
    e.stopPropagation();
    if (confirm(`Remove ${member.name} from this group?`)) {
        router.delete(route('trainer.groups.members.destroy', { group: props.group.id, client: member.id }));
    }
};

const handleMessageGroup = () => {
    router.visit(route('trainer.messages.index', { group: props.group.id }));
};

const handleInsightAction = (insight) => {
    if (insight?.action === 'Send group message') handleMessageGroup();
};
</script>

<template>
    <Head :title="`${group.name} - Group`" />

    <TrainerLayout>
        <div class="max-w-7xl mx-auto pb-8">
            <!-- Header -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-start gap-4">
                        <div
                            :class="[
                                'size-16 rounded-xl flex items-center justify-center shrink-0',
                                group.color || 'bg-blue-600',
                            ]"
                        >
                            <UsersRound class="size-8 text-white" />
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold mb-2">{{ group.name }}</h1>
                            <p
                                v-if="group.description"
                                class="text-gray-500 mb-3"
                            >
                                {{ group.description }}
                            </p>
                            <p
                                v-else
                                class="text-gray-400 italic mb-3"
                            >
                                No description
                            </p>
                            <div class="flex items-center gap-4 text-sm flex-wrap">
                                <div class="flex items-center gap-2">
                                    <Users class="size-4 text-gray-500" />
                                    <span class="font-medium">{{ group.members_count }} members</span>
                                </div>
                                <span class="text-gray-400">•</span>
                                <div class="flex items-center gap-2">
                                    <Calendar class="size-4 text-gray-500" />
                                    <span>Created {{ formatDate(group.created_at) }}</span>
                                </div>
                                <span class="text-gray-400">•</span>
                                <Badge variant="outline" class="text-green-600 border-green-600">
                                    Active
                                </Badge>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <Button variant="outline" size="sm" @click="handleMessageGroup">
                            <MessageSquare class="size-4 mr-2" />
                            Message Group
                        </Button>
                        <Button variant="outline" size="icon">
                            <Settings class="size-4" />
                        </Button>
                        <Button variant="outline" size="icon">
                            <MoreVertical class="size-4" />
                        </Button>
                    </div>
                </div>

                <!-- Stats Bar -->
                <div class="grid grid-cols-4 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-1">
                            <Activity class="size-4 text-gray-500" />
                            <p class="text-sm text-gray-500">Avg. Adherence</p>
                        </div>
                        <p class="text-2xl font-bold">{{ stats?.avg_adherence ?? 0 }}%</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-1">
                            <Users class="size-4 text-gray-500" />
                            <p class="text-sm text-gray-500">Active Members</p>
                        </div>
                        <p class="text-2xl font-bold">
                            {{ stats?.active_members ?? 0 }}/{{ group.members_count ?? 0 }}
                        </p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-1">
                            <Dumbbell class="size-4 text-gray-500" />
                            <p class="text-sm text-gray-500">Total Workouts</p>
                        </div>
                        <p class="text-2xl font-bold">{{ stats?.total_workouts ?? 0 }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-1">
                            <BarChart3 class="size-4 text-gray-500" />
                            <p class="text-sm text-gray-500">Avg. Per Week</p>
                        </div>
                        <p class="text-2xl font-bold">{{ stats?.avg_workouts_per_week ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Top AI Insight -->
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
                    <Button size="sm" variant="outline" @click="handleInsightAction(aiInsights[0])">
                        {{ aiInsights[0].action }}
                    </Button>
                </div>
            </div>

            <!-- Tabs -->
            <Tabs v-model="activeSection" :tabs="tabs" />
            <div class="mt-6">
                <GroupOverviewSection
                    v-if="activeSection === 'overview'"
                    :ai-insights="aiInsights"
                    :recent-activity="recentActivity"
                    @add-members="isAddMembersModalOpen = true"
                    @message-group="handleMessageGroup"
                    @insight-action="handleInsightAction"
                />
                <GroupMembersSection
                    v-if="activeSection === 'members'"
                    :members="members"
                    @add-members="isAddMembersModalOpen = true"
                    @member-click="handleMemberClick"
                    @remove-member="handleRemoveMember"
                />
                <GroupContentSection
                    v-if="activeSection === 'content'"
                    :assigned-content="assignedContent"
                />
                <GroupActivitySection
                    v-if="activeSection === 'activity'"
                    :recent-activity="recentActivity"
                />
            </div>

            <!-- Add Members Modal -->
            <AddGroupMembersModal
                :is-open="isAddMembersModalOpen"
                :group-id="group.id"
                :available-clients="availableClients"
                @close="isAddMembersModalOpen = false"
            />
        </div>
    </TrainerLayout>
</template>
