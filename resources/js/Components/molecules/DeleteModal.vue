<script setup>
import DialogModal from '@/Components/molecules/DialogModal.vue';
import Button from '@/Components/atoms/Button.vue';

defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Delete?',
    },
    description: {
        type: String,
        default: 'This action cannot be undone.',
    },
    confirmLabel: {
        type: String,
        default: 'Delete',
    },
    processing: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close', 'confirm']);
</script>

<template>
    <DialogModal
        :is-open="isOpen"
        :title="title"
        :description="description"
        max-width="sm"
        @close="emit('close')"
    >
        <div class="flex items-center justify-end gap-3 pt-2">
            <Button
                type="button"
                variant="outline"
                :disabled="processing"
                @click="emit('close')"
            >
                Cancel
            </Button>
            <Button
                type="button"
                variant="danger"
                :disabled="processing"
                @click="emit('confirm')"
            >
                {{ processing ? 'Deleting...' : confirmLabel }}
            </Button>
        </div>
    </DialogModal>
</template>
