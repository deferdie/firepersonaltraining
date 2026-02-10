<script setup>
import { computed } from 'vue';
import DialogModal from '@/Components/molecules/DialogModal.vue';
import Button from '@/Components/atoms/Button.vue';
import { FileText } from 'lucide-vue-next';

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

const imageExtensions = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
const videoExtensions = ['mp4', 'webm', 'mov'];
const audioExtensions = ['wav', 'mp3', 'm4a'];
const documentExtensions = ['pdf'];
const officeExtensions = ['doc', 'docx', 'xls', 'xlsx'];

const fileType = computed(() => (props.document?.file_type || '').toLowerCase());
const hasPreviewUrl = computed(() => !!props.document?.preview_url);

const isImage = computed(() => imageExtensions.includes(fileType.value));
const isVideo = computed(() => videoExtensions.includes(fileType.value));
const isAudio = computed(() => audioExtensions.includes(fileType.value));
const isPdf = computed(() => documentExtensions.includes(fileType.value));
const isOffice = computed(() => officeExtensions.includes(fileType.value));

const googleViewerUrl = computed(() => {
    if (!isOffice.value || !hasPreviewUrl.value) return null;
    return `https://docs.google.com/gview?url=${encodeURIComponent(props.document.preview_url)}&embedded=true`;
});

const downloadUrl = computed(() => {
    if (!props.document?.id) return '#';
    return route('trainer.library.documents.download', props.document.id);
});

const modalTitle = computed(() => props.document?.title || 'Preview document');
const modalDescription = computed(() => {
    if (!props.document?.file_type) return '';
    return props.document.file_type.toUpperCase();
});

const handleClose = () => {
    emit('close');
};
</script>

<template>
    <DialogModal
        :is-open="isOpen"
        :title="modalTitle"
        :description="modalDescription"
        max-width="3xl"
        @close="handleClose"
    >
        <!-- Body -->
        <div class="space-y-4">
            <!-- No preview URL at all -->
            <div
                v-if="!hasPreviewUrl"
                class="flex flex-col items-center justify-center py-12 text-center text-gray-500"
            >
                <FileText class="size-8 mb-3 text-gray-400" />
                <p class="text-sm mb-1">
                    Preview is not available for this file.
                </p>
                <p class="text-xs">
                    Use the download button below to open it in a native application.
                </p>
            </div>

            <!-- Image preview -->
            <div v-else-if="isImage" class="flex items-center justify-center">
                <img
                    :src="document.preview_url"
                    :alt="document.title"
                    class="max-h-[70vh] w-full object-contain rounded-lg bg-gray-50"
                />
            </div>

            <!-- Video preview -->
            <div v-else-if="isVideo" class="flex items-center justify-center">
                <video
                    :src="document.preview_url"
                    controls
                    class="w-full max-h-[70vh] rounded-lg bg-black"
                />
            </div>

            <!-- Audio preview -->
            <div v-else-if="isAudio" class="flex flex-col items-stretch">
                <p class="text-sm text-gray-500 mb-2">
                    Audio preview
                </p>
                <audio
                    :src="document.preview_url"
                    controls
                    class="w-full"
                />
            </div>

            <!-- PDF preview -->
            <div v-else-if="isPdf" class="flex flex-col gap-3">
                <iframe
                    :src="document.preview_url"
                    class="w-full h-[70vh] rounded-lg bg-gray-100"
                />
                <p class="text-xs text-gray-400">
                    If the PDF does not render, try downloading and opening it in your PDF viewer.
                </p>
            </div>

            <!-- Office via Google Docs Viewer -->
            <div v-else-if="isOffice && googleViewerUrl" class="flex flex-col gap-3">
                <iframe
                    :src="googleViewerUrl"
                    class="w-full h-[70vh] rounded-lg bg-gray-100"
                />
                <p class="text-xs text-gray-400">
                    This preview uses Google Docs Viewer and may not work in local development or for non-public URLs.
                </p>
            </div>

            <!-- Fallback for unknown types -->
            <div
                v-else
                class="flex flex-col items-center justify-center py-12 text-center text-gray-500"
            >
                <FileText class="size-8 mb-3 text-gray-400" />
                <p class="text-sm mb-1">
                    Preview is not available for this file type.
                </p>
                <p class="text-xs">
                    Use the download button below to open it in a native application.
                </p>
            </div>
        </div>

        <template #footer>
            <div class="flex items-center justify-end gap-3">
                <Button
                    type="button"
                    variant="outline"
                    @click="handleClose"
                >
                    Close
                </Button>
                <a
                    :href="downloadUrl"
                    class="inline-flex items-center justify-center rounded-md border border-transparent bg-gray-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Download
                </a>
            </div>
        </template>
    </DialogModal>
</template>

