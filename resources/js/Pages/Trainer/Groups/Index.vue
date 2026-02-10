<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import TrainerLayout from '@/Layouts/TrainerLayout.vue';
import { UsersRound, Users, Calendar, Plus } from 'lucide-vue-next';
import SearchInput from '@/Components/molecules/SearchInput.vue';
import EmptyState from '@/Components/molecules/EmptyState.vue';
import StatCard from '@/Components/molecules/StatCard.vue';
import GroupCard from '@/Components/organisms/GroupCard.vue';
import AddGroupModal from '@/Components/organisms/AddGroupModal.vue';
import Button from '@/Components/atoms/Button.vue';

const props = defineProps({
    groups: Object,
    stats: Object,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || '');
const isAddGroupModalOpen = ref(false);

const filteredGroups = computed(() => {
    if (!searchQuery.value) {
        return props.groups.data;
    }

    const query = searchQuery.value.toLowerCase();
    return props.groups.data.filter(group =>
        group.name.toLowerCase().includes(query) ||
        (group.description && group.description.toLowerCase().includes(query))
    );
});

const handleGroupClick = (group) => {
    router.visit(route('trainer.groups.show', group.id));
};

const handleEdit = (group) => {
    // Placeholder for edit modal
    console.log('Edit group', group);
};

const handleManageMembers = (group) => {
    // Placeholder for manage members
    console.log('Manage members', group);
};

const handleSearch = () => {
    router.get(route('trainer.groups.index'), { search: searchQuery.value }, {
        preserveState: true,
        replace: true,
    });
};
</script>

<template>
    <TrainerLayout
        title="Groups"
        description="Organize and manage clients in groups"
    >
        <template #action>
            <Button @click="isAddGroupModalOpen = true">
                <Plus class="size-4 mr-2" />
                Add Group
            </Button>
        </template>

        <div class="space-y-6">
            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-3">
                <StatCard
                    title="Total Groups"
                    :value="stats.total"
                    :icon="UsersRound"
                />
                <StatCard
                    title="Active Groups"
                    :value="stats.active"
                    :icon="Users"
                />
                <StatCard
                    title="Total Members"
                    :value="stats.total_members"
                    :icon="Calendar"
                />
            </div>

            <!-- Search -->
            <SearchInput
                v-model="searchQuery"
                placeholder="Search groups by name or description..."
                @search="handleSearch"
            />

            <!-- Groups Grid -->
            <div v-if="filteredGroups.length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <GroupCard
                    v-for="group in filteredGroups"
                    :key="group.id"
                    :group="group"
                    @click="handleGroupClick"
                    @edit="handleEdit"
                    @manage-members="handleManageMembers"
                />
            </div>

            <!-- Empty State -->
            <EmptyState
                v-else
                :icon="UsersRound"
                :description="searchQuery ? 'No groups found matching your search' : 'No groups yet'"
            />

            <!-- Add Group Modal -->
            <AddGroupModal
                :is-open="isAddGroupModalOpen"
                @close="isAddGroupModalOpen = false"
            />
        </div>
    </TrainerLayout>
</template>
