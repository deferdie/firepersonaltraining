<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import TrainerLayout from '@/Layouts/TrainerLayout.vue';
import {
    File,
    FileText,
    Image,
    Video,
    Music,
    Table,
    Plus,
    Pencil,
    Trash2,
    Download,
} from 'lucide-vue-next';
import SearchInput from '@/Components/molecules/SearchInput.vue';
import EmptyState from '@/Components/molecules/EmptyState.vue';
import DocumentFormModal from '@/Components/organisms/DocumentFormModal.vue';
import DocumentPreviewModal from '@/Components/organisms/DocumentPreviewModal.vue';
import DeleteModal from '@/Components/molecules/DeleteModal.vue';
import Button from '@/Components/atoms/Button.vue';
import Card from '@/Components/molecules/Card.vue';
import CardHeader from '@/Components/molecules/CardHeader.vue';
import CardContent from '@/Components/molecules/CardContent.vue';
import DropdownMenu from '@/Components/molecules/DropdownMenu.vue';
import DropdownMenuItem from '@/Components/molecules/DropdownMenuItem.vue';

const props = defineProps({
    documents: {
        type: Object,
        default: () => ({ data: [] }),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const searchQuery = ref(props.filters?.search || '');
const isDocumentModalOpen = ref(false);
const editingDocument = ref(null);
const documentToDelete = ref(null);
const deleting = ref(false);
const previewDocument = ref(null);

const documentsList = () => props.documents?.data ?? [];

const openCreateModal = () => {
    editingDocument.value = null;
    isDocumentModalOpen.value = true;
};

const openEditModal = (document) => {
    editingDocument.value = document;
    isDocumentModalOpen.value = true;
};

const openPreview = (document) => {
    previewDocument.value = document;
};

const closePreview = () => {
    previewDocument.value = null;
};

const handleCloseModal = () => {
    isDocumentModalOpen.value = false;
    editingDocument.value = null;
};

const openDeleteModal = (document) => {
    documentToDelete.value = document;
};

const handleCloseDeleteModal = () => {
    if (!deleting.value) {
        documentToDelete.value = null;
    }
};

const handleConfirmDelete = () => {
    if (!documentToDelete.value) return;
    const id = documentToDelete.value.id;
    deleting.value = true;
    router.delete(route('trainer.library.documents.destroy', id), {
        preserveScroll: true,
        onSuccess: () => {
            documentToDelete.value = null;
        },
        onFinish: () => {
            deleting.value = false;
        },
    });
};

const handleSearch = () => {
    router.get(route('trainer.library.documents.index'), { search: searchQuery.value }, {
        preserveState: true,
        replace: true,
    });
};

const formatCreatedDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
};

const formatFileSize = (bytes) => {
    if (bytes == null) return '';
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
};

const downloadUrl = (document) => {
    if (!document?.id) return '#';
    return route('trainer.library.documents.download', document.id);
};

const deleteModalDescription = computed(() =>
    documentToDelete.value
        ? `Are you sure you want to delete "${documentToDelete.value.title}"? This cannot be undone.`
        : 'This action cannot be undone.'
);

const imageExtensions = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
const videoExtensions = ['mp4', 'webm', 'mov'];
const audioExtensions = ['wav', 'mp3', 'm4a'];
const documentExtensions = ['pdf', 'doc', 'docx'];
const spreadsheetExtensions = ['xls', 'xlsx'];

function getDocumentIcon(fileType) {
    const ext = (fileType || '').toLowerCase();
    if (imageExtensions.includes(ext)) {
        return { icon: Image, bgColor: 'bg-green-100', textColor: 'text-green-600' };
    }
    if (videoExtensions.includes(ext)) {
        return { icon: Video, bgColor: 'bg-red-100', textColor: 'text-red-600' };
    }
    if (audioExtensions.includes(ext)) {
        return { icon: Music, bgColor: 'bg-purple-100', textColor: 'text-purple-600' };
    }
    if (documentExtensions.includes(ext)) {
        return { icon: FileText, bgColor: 'bg-orange-100', textColor: 'text-orange-600' };
    }
    if (spreadsheetExtensions.includes(ext)) {
        return { icon: Table, bgColor: 'bg-emerald-100', textColor: 'text-emerald-600' };
    }
    return { icon: File, bgColor: 'bg-gray-100', textColor: 'text-gray-600' };
}
</script>

<template>
    <TrainerLayout
        title="Documents"
        description="PDFs, guides, and educational materials"
    >
        <template #action>
            <Button @click="openCreateModal">
                <Plus class="size-4 mr-2" />
                Add Document
            </Button>
        </template>

        <div class="space-y-6">
            <SearchInput
                v-model="searchQuery"
                placeholder="Search documents by title or description..."
                @search="handleSearch"
            />

            <div v-if="documentsList().length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card
                    v-for="doc in documentsList()"
                    :key="doc.id"
                    class="hover:shadow-md transition-all cursor-pointer"
                    @click="openPreview(doc)"
                >
                    <CardHeader class="pb-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                <div
                                    :class="[
                                        'size-12 shrink-0 rounded-xl flex items-center justify-center',
                                        getDocumentIcon(doc.file_type).bgColor,
                                        getDocumentIcon(doc.file_type).textColor,
                                    ]"
                                >
                                    <component :is="getDocumentIcon(doc.file_type).icon" class="size-6" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base font-semibold truncate">{{ doc.title }}</h3>
                                    <p
                                        v-if="doc.description"
                                        class="text-sm text-gray-500 line-clamp-2 mt-0.5"
                                    >
                                        {{ doc.description }}
                                    </p>
                                    <p
                                        v-else
                                        class="text-sm text-gray-400 italic mt-0.5"
                                    >
                                        No description
                                    </p>
                                </div>
                            </div>
                            <DropdownMenu @click.stop>
                                <template #content>
                                    <DropdownMenuItem @click.stop="openEditModal(doc)">
                                        <Pencil class="size-4 mr-2" />
                                        Edit
                                    </DropdownMenuItem>
                                    <a
                                        v-if="doc.file_url"
                                        :href="downloadUrl(doc)"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none transition-colors hover:bg-gray-100 focus:bg-gray-100"
                                        @click.stop
                                    >
                                        <Download class="size-4 mr-2" />
                                        Download
                                    </a>
                                    <DropdownMenuItem
                                        @click.stop="openDeleteModal(doc)"
                                        className="text-red-600 focus:text-red-600"
                                    >
                                        <Trash2 class="size-4 mr-2" />
                                        Delete
                                    </DropdownMenuItem>
                                </template>
                            </DropdownMenu>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center gap-2 flex-wrap text-xs text-gray-500">
                            <span>Created {{ formatCreatedDate(doc.created_at) }}</span>
                            <template v-if="doc.file_type || doc.file_size != null">
                                <span>·</span>
                                <span>{{ doc.file_type?.toUpperCase() || 'File' }}</span>
                                <span v-if="doc.file_size != null">{{ formatFileSize(doc.file_size) }}</span>
                            </template>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <EmptyState
                v-else
                :icon="File"
                :description="searchQuery ? 'No documents found matching your search' : 'No documents yet'"
            />

            <DocumentFormModal
                :is-open="isDocumentModalOpen"
                :document="editingDocument"
                @close="handleCloseModal"
            />

            <DocumentPreviewModal
                :is-open="!!previewDocument"
                :document="previewDocument"
                @close="closePreview"
            />

            <DeleteModal
                :is-open="!!documentToDelete"
                title="Delete document?"
                :description="deleteModalDescription"
                confirm-label="Delete"
                :processing="deleting"
                @close="handleCloseDeleteModal"
                @confirm="handleConfirmDelete"
            />
        </div>
    </TrainerLayout>
</template>
