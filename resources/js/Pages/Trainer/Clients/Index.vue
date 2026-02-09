<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import TrainerLayout from '@/Layouts/TrainerLayout.vue';
import { Users, TrendingUp, Calendar, Plus } from 'lucide-vue-next';
import PageHeader from '@/Components/molecules/PageHeader.vue';
import SearchInput from '@/Components/molecules/SearchInput.vue';
import EmptyState from '@/Components/molecules/EmptyState.vue';
import StatCard from '@/Components/molecules/StatCard.vue';
import ClientCard from '@/Components/organisms/ClientCard.vue';
import AddClientModal from '@/Components/organisms/AddClientModal.vue';
import Button from '@/Components/atoms/Button.vue';

const props = defineProps({
    clients: Object,
    stats: Object,
    filters: Object,
    programs: {
        type: Array,
        default: () => [],
    },
});

const searchQuery = ref(props.filters?.search || '');
const isAddClientModalOpen = ref(false);

const filteredClients = computed(() => {
    if (!searchQuery.value) {
        return props.clients.data;
    }

    const query = searchQuery.value.toLowerCase();
    return props.clients.data.filter(client =>
        client.name.toLowerCase().includes(query) ||
        client.email.toLowerCase().includes(query) ||
        (client.program && client.program.toLowerCase().includes(query))
    );
});

const handleClientClick = (client) => {
    router.visit(route('trainer.clients.show', client.id));
};

const handleSendMessage = (client) => {
    // Handle send message action
    console.log('Send message to', client.name);
};

const handleScheduleSession = (client) => {
    // Handle schedule session action
    console.log('Schedule session for', client.name);
};

const handleSearch = () => {
    router.get(route('trainer.clients.index'), { search: searchQuery.value }, {
        preserveState: true,
        replace: true,
    });
};
</script>

<template>
    <Head title="Clients" />

    <TrainerLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <PageHeader
                    title="Clients"
                    description="Manage and track all your clients"
                />
                <Button @click="isAddClientModalOpen = true">
                    <Plus class="size-4 mr-2" />
                    Add Client
                </Button>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-3">
                <StatCard
                    title="Total Clients"
                    :value="stats.total"
                    :icon="Users"
                />
                <StatCard
                    title="Active Clients"
                    :value="stats.active"
                    :icon="TrendingUp"
                />
                <StatCard
                    title="Trial Members"
                    :value="stats.trial"
                    :icon="Calendar"
                />
            </div>

            <!-- Search -->
            <SearchInput
                v-model="searchQuery"
                placeholder="Search clients by name, email, or program..."
                @search="handleSearch"
            />

            <!-- Clients Grid -->
            <div v-if="filteredClients.length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <ClientCard
                    v-for="client in filteredClients"
                    :key="client.id"
                    :client="client"
                    @click="handleClientClick"
                    @send-message="handleSendMessage"
                    @schedule-session="handleScheduleSession"
                />
            </div>

            <!-- Empty State -->
            <EmptyState
                v-else
                :icon="Users"
                :description="searchQuery ? 'No clients found matching your search' : 'No clients yet'"
            />

            <!-- Add Client Modal -->
            <AddClientModal
                :is-open="isAddClientModalOpen"
                :programs="programs"
                @close="isAddClientModalOpen = false"
            />
        </div>
    </TrainerLayout>
</template>
