<script setup>
import { ref } from 'vue';
import { StickyNote, Plus } from 'lucide-vue-next';
import SectionCard from '@/Components/molecules/SectionCard.vue';
import Button from '@/Components/atoms/Button.vue';
import Badge from '@/Components/atoms/Badge.vue';
import AddNoteModal from '@/Components/organisms/AddNoteModal.vue';

const props = defineProps({
    notes: {
        type: Array,
        default: () => [],
    },
    clientId: {
        type: Number,
        required: true,
    },
});

const isModalOpen = ref(false);

const formatDate = (dateStr) => {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const openModal = () => {
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};
</script>

<template>
    <div class="space-y-6">
        <!-- Notes Overview -->
        <SectionCard :icon="StickyNote" title="Notes Overview">
            <template #action>
                <Button variant="ghost" size="sm" @click="openModal">
                    <Plus class="size-4 mr-1" />
                    Add
                </Button>
            </template>
            <div v-if="notes.length > 0" class="space-y-5">
                <div
                    v-for="note in notes"
                    :key="note.id"
                    class="space-y-3 p-4 rounded-lg border border-gray-200 bg-white hover:shadow-sm transition-shadow"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <Badge variant="outline" class="text-xs">{{ note.category }}</Badge>
                                <span class="text-xs text-gray-500">{{ formatDate(note.date) }}</span>
                            </div>
                            <div class="flex items-center gap-4 text-xs text-gray-500 mb-2">
                                <div class="flex items-center gap-1">
                                    <StickyNote class="size-3" />
                                    Author: {{ note.author }}
                                </div>
                            </div>
                            <p class="text-sm text-gray-900 mt-2">{{ note.content }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="text-center py-12 text-gray-500">
                <StickyNote class="size-12 mx-auto mb-3 opacity-50" />
                <p>No notes yet</p>
                <Button size="sm" class="mt-4" @click="openModal">
                    <Plus class="size-4 mr-1" />
                    Add First Note
                </Button>
            </div>
        </SectionCard>

        <!-- Add Note Modal -->
        <AddNoteModal
            :is-open="isModalOpen"
            :client-id="clientId"
            @close="closeModal"
        />
    </div>
</template>
