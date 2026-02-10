<script setup>
import { watch } from 'vue';
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
    habit: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    name: '',
    description: '',
});

watch(
    () => [props.isOpen, props.habit],
    () => {
        if (props.isOpen) {
            if (props.habit) {
                form.name = props.habit.name;
                form.description = props.habit.description ?? '';
            } else {
                form.reset();
            }
        }
    },
    { immediate: true }
);

const handleSubmit = () => {
    if (props.habit) {
        form.patch(route('trainer.library.habits.update', props.habit.id), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                emit('close');
            },
        });
    } else {
        form.post(route('trainer.library.habits.store'), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                emit('close');
            },
        });
    }
};

const handleClose = () => {
    if (!form.processing) {
        form.reset();
        form.clearErrors();
        emit('close');
    }
};

const isEdit = () => !!props.habit;
const modalTitle = isEdit() ? 'Edit habit' : 'Add habit';
const modalDescription = isEdit()
    ? 'Update the details of this habit.'
    : 'Create a trackable habit for building healthy routines.';
const submitLabel = () =>
    form.processing
        ? (isEdit() ? 'Saving...' : 'Creating...')
        : (isEdit() ? 'Save changes' : 'Create habit');
</script>

<template>
    <DialogModal
        :is-open="isOpen"
        :title="modalTitle"
        :description="modalDescription"
        max-width="2xl"
        @close="handleClose"
    >
        <form id="habit-form" @submit.prevent="handleSubmit" class="space-y-6">
            <div class="space-y-4">
                <div class="space-y-2">
                    <Label for="habit-name" :required="true">Name</Label>
                    <Input
                        id="habit-name"
                        v-model="form.name"
                        placeholder="e.g. Drink 8 glasses of water"
                        :disabled="form.processing"
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="space-y-2">
                    <Label for="habit-description">Description</Label>
                    <Textarea
                        id="habit-description"
                        v-model="form.description"
                        placeholder="Optional description..."
                        rows="3"
                        :disabled="form.processing"
                    />
                    <InputError :message="form.errors.description" />
                </div>
            </div>
        </form>
        <template #footer>
            <div class="flex items-center justify-end gap-3">
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
                    form="habit-form"
                    :disabled="form.processing"
                >
                    {{ submitLabel() }}
                </Button>
            </div>
        </template>
    </DialogModal>
</template>
