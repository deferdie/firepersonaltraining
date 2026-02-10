<script setup>
import { ref, watch } from 'vue';
import DialogModal from '@/Components/molecules/DialogModal.vue';
import AssignContentStepper from '@/Components/organisms/AssignContentStepper.vue';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    assignableType: {
        type: String,
        required: true,
        validator: (v) => ['client', 'group'].includes(v),
    },
    assignableId: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(['close', 'assigned']);

const stepperKey = ref(0);

watch(
    () => props.isOpen,
    (open) => {
        if (open) {
            stepperKey.value += 1;
        }
    }
);

function handleClose() {
    emit('close');
}

function handleAssigned() {
    emit('assigned');
    emit('close');
}
</script>

<template>
    <DialogModal
        :is-open="isOpen"
        title="Assign content"
        :description="`Add content to this ${assignableType === 'client' ? 'client' : 'group'}`"
        max-width="2xl"
        @close="handleClose"
    >
        <AssignContentStepper
            :key="stepperKey"
            :assignable-type="assignableType"
            :assignable-id="assignableId"
            @assigned="handleAssigned"
        />
    </DialogModal>
</template>
