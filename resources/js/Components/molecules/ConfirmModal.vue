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
        default: 'Confirm',
    },
    description: {
        type: String,
        default: '',
    },
    confirmLabel: {
        type: String,
        default: 'Confirm',
    },
    cancelLabel: {
        type: String,
        default: 'Cancel',
    },
    confirmVariant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'outline', 'ghost', 'danger'].includes(value),
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
                {{ cancelLabel }}
            </Button>
            <Button
                type="button"
                :variant="confirmVariant"
                :disabled="processing"
                @click="emit('confirm')"
            >
                {{ processing ? 'Processing...' : confirmLabel }}
            </Button>
        </div>
    </DialogModal>
</template>
