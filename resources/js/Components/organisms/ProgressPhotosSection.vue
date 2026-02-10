<script setup>
import { ref } from 'vue';
import { Camera, Plus, FileText, Trash2, TrendingUp, Calendar } from 'lucide-vue-next';
import Card from '@/Components/molecules/Card.vue';
import CardContent from '@/Components/molecules/CardContent.vue';
import CardHeader from '@/Components/molecules/CardHeader.vue';
import Badge from '@/Components/atoms/Badge.vue';
import Button from '@/Components/atoms/Button.vue';
import DropdownMenu from '@/Components/molecules/DropdownMenu.vue';
import DropdownMenuItem from '@/Components/molecules/DropdownMenuItem.vue';

const props = defineProps({
    progressPhotos: {
        type: Array,
        default: () => [],
    },
});

const selectedPhoto = ref(null);

const formatDate = (dateStr) => {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
};

const openLightbox = (photoUrl) => {
    selectedPhoto.value = photoUrl;
};

const closeLightbox = () => {
    selectedPhoto.value = null;
};
</script>

<template>
    <div class="space-y-6">
        <!-- Progress Photos Header -->
        <Card>
            <CardHeader>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <Camera class="size-5 text-gray-900" />
                        <h2 class="text-lg font-semibold">Progress Photos</h2>
                    </div>
                    <Button>
                        <Plus class="size-4 mr-2" />
                        Upload Photos
                    </Button>
                </div>
            </CardHeader>
            <CardContent>
                <div class="flex items-center gap-4 text-sm text-gray-500">
                    <div class="flex items-center gap-2">
                        <Calendar class="size-4" />
                        <span>{{ progressPhotos.length }} check-ins</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <TrendingUp class="size-4" />
                        <span>Progress tracking</span>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Progress Photos Timeline -->
        <div v-if="progressPhotos.length > 0" class="space-y-6">
            <Card v-for="entry in progressPhotos" :key="entry.date">
                <!-- Entry Header -->
                <div class="p-6 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="font-semibold text-lg">{{ formatDate(entry.date) }}</h3>
                                <Badge v-if="entry.weight" variant="outline">
                                    {{ entry.weight }} lbs
                                </Badge>
                            </div>
                            <p v-if="entry.notes" class="text-sm text-gray-500">{{ entry.notes }}</p>
                        </div>
                        <DropdownMenu>
                            <template #content>
                                <DropdownMenuItem>
                                    <Plus class="size-4 mr-2" />
                                    Add Photos
                                </DropdownMenuItem>
                                <DropdownMenuItem>
                                    <FileText class="size-4 mr-2" />
                                    Edit Notes
                                </DropdownMenuItem>
                                <DropdownMenuItem class="text-red-600">
                                    <Trash2 class="size-4 mr-2" />
                                    Delete Entry
                                </DropdownMenuItem>
                            </template>
                        </DropdownMenu>
                    </div>
                </div>

                <!-- Photo Grid -->
                <CardContent>
                    <div class="grid grid-cols-3 gap-4">
                        <div
                            v-for="photo in entry.photos"
                            :key="photo.id"
                            class="space-y-2"
                        >
                            <div
                                class="aspect-[3/4] bg-gray-100 rounded-lg overflow-hidden cursor-pointer hover:ring-2 hover:ring-blue-500 transition-all"
                                @click="openLightbox(photo.url)"
                            >
                                <img
                                    v-if="photo.url"
                                    :src="photo.url"
                                    :alt="`${photo.angle} view`"
                                    class="w-full h-full object-cover"
                                />
                                <div
                                    v-else
                                    class="w-full h-full flex items-center justify-center text-gray-400"
                                >
                                    <Camera class="size-8" />
                                </div>
                            </div>
                            <p class="text-sm text-center font-medium text-gray-500">{{ photo.angle }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Empty State -->
        <Card v-else>
            <CardContent class="p-12 text-center">
                <div class="max-w-sm mx-auto">
                    <div
                        class="size-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4"
                    >
                        <Camera class="size-8 text-gray-400" />
                    </div>
                    <h3 class="font-semibold text-lg mb-2">No Progress Photos Yet</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Start tracking visual progress by uploading before photos and regular check-in photos.
                    </p>
                    <Button>
                        <Plus class="size-4 mr-2" />
                        Upload First Photos
                    </Button>
                </div>
            </CardContent>
        </Card>

        <!-- Photo Lightbox Modal -->
        <div
            v-if="selectedPhoto"
            class="fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4"
            @click="closeLightbox"
        >
            <div class="relative max-w-4xl max-h-[90vh]">
                <button
                    @click="closeLightbox"
                    class="absolute -top-12 right-0 text-white hover:text-gray-300"
                >
                    <span class="text-2xl">×</span>
                </button>
                <img
                    :src="selectedPhoto"
                    alt="Progress photo"
                    class="max-w-full max-h-[90vh] object-contain rounded-lg"
                    @click.stop
                />
            </div>
        </div>
    </div>
</template>
