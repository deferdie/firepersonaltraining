<script setup>
import { useForm } from '@inertiajs/vue3';
import DialogModal from '@/Components/molecules/DialogModal.vue';
import Label from '@/Components/atoms/Label.vue';
import Textarea from '@/Components/atoms/Textarea.vue';
import Button from '@/Components/atoms/Button.vue';
import Select from '@/Components/molecules/Select.vue';
import SelectTrigger from '@/Components/molecules/SelectTrigger.vue';
import SelectContent from '@/Components/molecules/SelectContent.vue';
import SelectItem from '@/Components/molecules/SelectItem.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    clientId: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    category: null,
    content: '',
});

const handleSubmit = () => {
    form.post(route('trainer.clients.notes.store', props.clientId), {
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
        title="Add Note"
        description="Record important information about this client"
        max-width="2xl"
        @close="handleClose"
    >
        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Category -->
            <div class="space-y-2">
                <Label for="category">Category</Label>
                <Select v-model="form.category" placeholder="Select a category (optional)" :disabled="form.processing">
                    <SelectTrigger id="category" />
                    <SelectContent>
                        <SelectItem :value="null" label="General">
                            General
                        </SelectItem>
                        <SelectItem value="Progress" label="Progress">
                            Progress
                        </SelectItem>
                        <SelectItem value="Concerns" label="Concerns">
                            Concerns
                        </SelectItem>
                        <SelectItem value="Goals" label="Goals">
                            Goals
                        </SelectItem>
                        <SelectItem value="Other" label="Other">
                            Other
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="form.errors.category" />
            </div>

            <!-- Content -->
            <div class="space-y-2">
                <Label for="content" :required="true">Note Content</Label>
                <Textarea
                    id="content"
                    v-model="form.content"
                    :rows="6"
                    placeholder="Enter your note here..."
                    :disabled="form.processing"
                    :required="true"
                />
                <InputError :message="form.errors.content" />
            </div>

            <!-- Actions -->
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
                    {{ form.processing ? 'Adding...' : 'Add Note' }}
                </Button>
            </div>
        </form>
    </DialogModal>
</template>
