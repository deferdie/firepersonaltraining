<script setup>
import { useForm } from '@inertiajs/vue3';
import DialogModal from '@/Components/molecules/DialogModal.vue';
import Label from '@/Components/atoms/Label.vue';
import Input from '@/Components/atoms/Input.vue';
import Textarea from '@/Components/atoms/Textarea.vue';
import Button from '@/Components/atoms/Button.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    name: '',
    description: '',
});

const handleSubmit = () => {
    form.post(route('trainer.groups.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emit('close');
        },
    });
};

const handleClose = () => {
    if (!form.processing) {
        form.reset();
        form.clearErrors();
        emit('close');
    }
};
</script>

<template>
    <DialogModal
        :is-open="isOpen"
        title="Add New Group"
        description="Create a group to organize and manage clients together"
        max-width="2xl"
        @close="handleClose"
    >
        <form @submit.prevent="handleSubmit" class="space-y-6">
            <div class="space-y-4">
                <div class="space-y-2">
                    <Label for="name" :required="true">Group Name</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        placeholder="e.g. Morning Bootcamp"
                        :disabled="form.processing"
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="space-y-2">
                    <Label for="description">Description</Label>
                    <Textarea
                        id="description"
                        v-model="form.description"
                        placeholder="Optional description for this group..."
                        rows="3"
                        :disabled="form.processing"
                    />
                    <InputError :message="form.errors.description" />
                </div>
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
                    type="submit"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Creating...' : 'Create Group' }}
                </Button>
            </div>
        </form>
    </DialogModal>
</template>
