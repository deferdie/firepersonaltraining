<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import TrainerLayout from '@/Layouts/TrainerLayout.vue';
import { Heart, Plus, Pencil, Trash2 } from 'lucide-vue-next';
import SearchInput from '@/Components/molecules/SearchInput.vue';
import EmptyState from '@/Components/molecules/EmptyState.vue';
import HabitFormModal from '@/Components/organisms/HabitFormModal.vue';
import DeleteModal from '@/Components/molecules/DeleteModal.vue';
import Button from '@/Components/atoms/Button.vue';
import Card from '@/Components/molecules/Card.vue';
import CardHeader from '@/Components/molecules/CardHeader.vue';
import CardContent from '@/Components/molecules/CardContent.vue';
import DropdownMenu from '@/Components/molecules/DropdownMenu.vue';
import DropdownMenuItem from '@/Components/molecules/DropdownMenuItem.vue';

const props = defineProps({
    habits: {
        type: Object,
        default: () => ({ data: [] }),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const searchQuery = ref(props.filters?.search || '');
const isHabitModalOpen = ref(false);
const editingHabit = ref(null);
const habitToDelete = ref(null);
const deleting = ref(false);

const habitsList = () => props.habits?.data ?? [];

const openCreateModal = () => {
    editingHabit.value = null;
    isHabitModalOpen.value = true;
};

const openEditModal = (habit) => {
    editingHabit.value = habit;
    isHabitModalOpen.value = true;
};

const handleCloseModal = () => {
    isHabitModalOpen.value = false;
    editingHabit.value = null;
};

const openDeleteModal = (habit) => {
    habitToDelete.value = habit;
};

const handleCloseDeleteModal = () => {
    if (!deleting.value) {
        habitToDelete.value = null;
    }
};

const handleConfirmDelete = () => {
    if (!habitToDelete.value) return;
    const id = habitToDelete.value.id;
    deleting.value = true;
    router.delete(route('trainer.library.habits.destroy', id), {
        preserveScroll: true,
        onSuccess: () => {
            habitToDelete.value = null;
        },
        onFinish: () => {
            deleting.value = false;
        },
    });
};

const handleSearch = () => {
    router.get(route('trainer.library.habits.index'), { search: searchQuery.value }, {
        preserveState: true,
        replace: true,
    });
};

const formatCreatedDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
};

const deleteModalDescription = computed(() =>
    habitToDelete.value
        ? `Are you sure you want to delete "${habitToDelete.value.name}"? This cannot be undone.`
        : 'This action cannot be undone.'
);
</script>

<template>
    <TrainerLayout
        title="Habits"
        description="Trackable habits for building healthy routines"
    >
        <template #action>
            <Button @click="openCreateModal">
                <Plus class="size-4 mr-2" />
                Add Habit
            </Button>
        </template>

        <div class="space-y-6">
            <SearchInput
                v-model="searchQuery"
                placeholder="Search habits by name or description..."
                @search="handleSearch"
            />

            <div v-if="habitsList().length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card
                    v-for="habit in habitsList()"
                    :key="habit.id"
                    class="hover:shadow-md transition-all cursor-pointer"
                    @click="openEditModal(habit)"
                >
                    <CardHeader class="pb-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                <div
                                    class="size-12 shrink-0 rounded-xl flex items-center justify-center bg-pink-100 text-pink-600"
                                >
                                    <Heart class="size-6" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base font-semibold truncate">{{ habit.name }}</h3>
                                    <p
                                        v-if="habit.description"
                                        class="text-sm text-gray-500 line-clamp-2 mt-0.5"
                                    >
                                        {{ habit.description }}
                                    </p>
                                    <p
                                        v-else
                                        class="text-sm text-gray-400 italic mt-0.5"
                                    >
                                        No description
                                    </p>
                                </div>
                            </div>
                            <DropdownMenu @click.stop>
                                <template #content>
                                    <DropdownMenuItem @click.stop="openEditModal(habit)">
                                        <Pencil class="size-4 mr-2" />
                                        Edit
                                    </DropdownMenuItem>
                                    <DropdownMenuItem
                                        @click.stop="openDeleteModal(habit)"
                                        className="text-red-600 focus:text-red-600"
                                    >
                                        <Trash2 class="size-4 mr-2" />
                                        Delete
                                    </DropdownMenuItem>
                                </template>
                            </DropdownMenu>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <span class="text-xs text-gray-500">
                            Created {{ formatCreatedDate(habit.created_at) }}
                        </span>
                    </CardContent>
                </Card>
            </div>

            <EmptyState
                v-else
                :icon="Heart"
                :description="searchQuery ? 'No habits found matching your search' : 'No habits yet'"
            />

            <HabitFormModal
                :is-open="isHabitModalOpen"
                :habit="editingHabit"
                @close="handleCloseModal"
            />

            <DeleteModal
                :is-open="!!habitToDelete"
                title="Delete habit?"
                :description="deleteModalDescription"
                confirm-label="Delete"
                :processing="deleting"
                @close="handleCloseDeleteModal"
                @confirm="handleConfirmDelete"
            />
        </div>
    </TrainerLayout>
</template>
