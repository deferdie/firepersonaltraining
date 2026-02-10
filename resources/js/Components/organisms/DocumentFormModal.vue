<script setup>
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import DialogModal from '@/Components/molecules/DialogModal.vue';
import Label from '@/Components/atoms/Label.vue';
import Input from '@/Components/atoms/Input.vue';
import Textarea from '@/Components/atoms/Textarea.vue';
import Button from '@/Components/atoms/Button.vue';
import FileDropzone from '@/Components/molecules/FileDropzone.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    document: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    title: '',
    description: '',
    category: '',
    file: null,
});

watch(
    () => [props.isOpen, props.document],
    () => {
        if (props.isOpen) {
            if (props.document) {
                form.title = props.document.title;
                form.description = props.document.description ?? '';
                form.category = props.document.category ?? '';
                form.file = null;
            } else {
                form.reset();
                form.file = null;
            }
        }
    },
    { immediate: true }
);

const handleSubmit = () => {
    if (props.document) {
        const url = route('trainer.library.documents.update', props.document.id);
        if (form.file) {
            form.transform((data) => ({ ...data, _method: 'PATCH' })).post(url, {
                preserveScroll: true,
                forceFormData: true,
                onSuccess: () => {
                    form.reset();
                    form.file = null;
                    emit('close');
                },
            });
        } else {
            form.patch(url, {
                preserveScroll: true,
                onSuccess: () => {
                    form.reset();
                    emit('close');
                },
            });
        }
    } else {
        form.post(route('trainer.library.documents.store'), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                form.file = null;
                emit('close');
            },
        });
    }
};

const handleClose = () => {
    if (!form.processing) {
        form.reset();
        form.clearErrors();
        form.file = null;
        emit('close');
    }
};

const isEdit = () => !!props.document;
const modalTitle = isEdit() ? 'Edit document' : 'Add document';
const modalDescription = isEdit()
    ? 'Update the details or replace the file.'
    : 'Upload a document (PDF, images, videos, audio, Word, or Excel).';
const submitLabel = () =>
    form.processing
        ? (isEdit() ? 'Saving...' : 'Uploading...')
        : (isEdit() ? 'Save changes' : 'Upload document');

const acceptedTypes = [
    'image/jpeg', 'image/png', 'image/gif', 'image/webp',
    'application/pdf',
    'video/mp4', 'video/webm', 'video/quicktime',
    'audio/wav', 'audio/mpeg', 'audio/mp3', 'audio/x-m4a', 'audio/mp4',
    'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
].join(',');

const currentFileLabel = () => {
    if (!props.document?.file_type || !props.document?.file_url) return null;
    const ext = props.document.file_type.toUpperCase();
    const size = props.document.file_size != null
        ? (props.document.file_size < 1024
            ? props.document.file_size + ' B'
            : props.document.file_size < 1024 * 1024
                ? (props.document.file_size / 1024).toFixed(1) + ' KB'
                : (props.document.file_size / (1024 * 1024)).toFixed(1) + ' MB')
        : '';
    return `${ext}${size ? ` · ${size}` : ''}`;
};
</script>

<template>
    <DialogModal
        :is-open="isOpen"
        :title="modalTitle"
        :description="modalDescription"
        max-width="2xl"
        @close="handleClose"
    >
        <form id="document-form" @submit.prevent="handleSubmit" class="space-y-6">
            <div class="space-y-4">
                <div class="space-y-2">
                    <Label for="document-title" :required="true">Title</Label>
                    <Input
                        id="document-title"
                        v-model="form.title"
                        placeholder="e.g. Client intake form"
                        :disabled="form.processing"
                    />
                    <InputError :message="form.errors.title" />
                </div>

                <div class="space-y-2">
                    <Label for="document-description">Description</Label>
                    <Textarea
                        id="document-description"
                        v-model="form.description"
                        placeholder="Optional description..."
                        rows="3"
                        :disabled="form.processing"
                    />
                    <InputError :message="form.errors.description" />
                </div>

                <div class="space-y-2">
                    <Label for="document-category">Category</Label>
                    <Input
                        id="document-category"
                        v-model="form.category"
                        placeholder="Optional category..."
                        :disabled="form.processing"
                    />
                    <InputError :message="form.errors.category" />
                </div>

                <div class="space-y-2">
                    <Label for="document-file" :required="!isEdit()">
                        {{ isEdit() ? 'Replace file (optional)' : 'File' }}
                    </Label>
                    <p v-if="isEdit() && currentFileLabel()" class="text-sm text-gray-500 mb-1">
                        Current: {{ currentFileLabel() }}
                    </p>
                    <FileDropzone
                        id="document-file"
                        v-model="form.file"
                        :accept="acceptedTypes"
                        :disabled="form.processing"
                        :required="!isEdit()"
                        :placeholder="isEdit() ? 'Drop a file to replace, or click to browse.' : 'Drag and drop a file here, or click to browse.'"
                    />
                    <InputError :message="form.errors.file" />
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
                    form="document-form"
                    :disabled="form.processing || (!isEdit() && !form.file)"
                >
                    {{ submitLabel() }}
                </Button>
            </div>
        </template>
    </DialogModal>
</template>
