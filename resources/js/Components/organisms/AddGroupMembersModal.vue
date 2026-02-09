<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import DialogModal from '@/Components/molecules/DialogModal.vue';
import Button from '@/Components/atoms/Button.vue';
import Input from '@/Components/atoms/Input.vue';
import Avatar from '@/Components/atoms/Avatar.vue';
import InputError from '@/Components/InputError.vue';
import { Search } from 'lucide-vue-next';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    groupId: {
        type: [Number, String],
        required: true,
    },
    availableClients: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close']);

const searchQuery = ref('');
const selectedIds = ref(new Set());

const form = useForm({
    client_ids: [],
});

const filteredClients = computed(() => {
    if (!searchQuery.value) {
        return props.availableClients;
    }
    const query = searchQuery.value.toLowerCase();
    return props.availableClients.filter((c) => c.name.toLowerCase().includes(query));
});

const selectedCount = computed(() => selectedIds.value.size);

const canSubmit = computed(() => selectedCount.value > 0 && !form.processing);

const isSelected = (id) => selectedIds.value.has(id);

const toggleClient = (id) => {
    const next = new Set(selectedIds.value);
    if (next.has(id)) {
        next.delete(id);
    } else {
        next.add(id);
    }
    selectedIds.value = next;
};

const handleSubmit = () => {
    form.client_ids = Array.from(selectedIds.value);
    form.post(route('trainer.groups.members.store', props.groupId), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            selectedIds.value = new Set();
            emit('close');
        },
    });
};

const handleClose = () => {
    if (!form.processing) {
        form.reset();
        form.clearErrors();
        selectedIds.value = new Set();
        searchQuery.value = '';
        emit('close');
    }
};

watch(
    () => props.isOpen,
    (open) => {
        if (!open) {
            selectedIds.value = new Set();
            searchQuery.value = '';
        }
    }
);
</script>

<template>
    <DialogModal
        :is-open="isOpen"
        title="Add Members"
        description="Select clients to add to this group"
        max-width="2xl"
        @close="handleClose"
    >
        <form @submit.prevent="handleSubmit" class="space-y-4">
            <div v-if="availableClients.length > 0" class="space-y-4">
                <div class="relative max-w-md">
                    <Input
                        v-model="searchQuery"
                        placeholder="Search clients by name..."
                        :disabled="form.processing"
                        :has-icon="true"
                    >
                        <template #icon>
                            <Search
                                class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-500 pointer-events-none"
                            />
                        </template>
                    </Input>
                </div>

                <p class="text-sm text-gray-500">
                    {{ selectedCount }} {{ selectedCount === 1 ? 'member' : 'members' }} selected
                </p>

                <div
                    class="max-h-64 overflow-y-auto rounded-lg border border-gray-200 divide-y divide-gray-100"
                >
                    <div
                        v-for="client in filteredClients"
                        :key="client.id"
                        class="flex items-center gap-3 p-3 hover:bg-gray-50 cursor-pointer transition-colors"
                        @click="toggleClient(client.id)"
                    >
                        <input
                            type="checkbox"
                            :checked="isSelected(client.id)"
                            class="rounded border-gray-300 text-gray-900 focus:ring-gray-500 shrink-0"
                            @click.stop
                            @change="toggleClient(client.id)"
                        />
                        <Avatar
                            :initials="client.initials"
                            :color="client.color"
                            size="sm"
                        />
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm truncate">{{ client.name }}</p>
                            <p class="text-xs text-gray-500 capitalize">
                                {{ client.status }}
                            </p>
                        </div>
                    </div>
                    <div
                        v-if="filteredClients.length === 0"
                        class="p-6 text-center text-gray-500 text-sm"
                    >
                        {{ searchQuery ? 'No clients match your search' : 'No clients available' }}
                    </div>
                </div>

                <InputError :message="form.errors.client_ids" />
            </div>

            <div v-else class="py-8 text-center text-gray-500">
                <p class="font-medium mb-1">No clients available</p>
                <p class="text-sm">
                    All your clients are already in this group, or you don't have any clients yet.
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <Button
                    type="button"
                    variant="outline"
                    :disabled="form.processing"
                    @click="handleClose"
                >
                    Cancel
                </Button>
                <Button
                    v-if="availableClients.length > 0"
                    type="submit"
                    :disabled="!canSubmit"
                >
                    {{ form.processing ? 'Adding...' : `Add ${selectedCount} member${selectedCount !== 1 ? 's' : ''}` }}
                </Button>
            </div>
        </form>
    </DialogModal>
</template>
