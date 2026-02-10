<script setup>
import { ChevronLeft } from 'lucide-vue-next';
import Button from '@/Components/atoms/Button.vue';

defineProps({
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

const emit = defineEmits(['back', 'submit']);

function reset() {}

defineExpose({ reset });
</script>

<template>
    <div class="space-y-6">
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-8 text-center">
            <p class="font-medium text-gray-900 mb-1">Programs</p>
            <p class="text-sm text-gray-500 mb-4">Assign programs from your library or create new ones. Coming soon.</p>
            <Button type="button" variant="outline" @click="emit('back')">
                <ChevronLeft class="size-4 mr-1" />
                Back to content type
            </Button>
        </div>
    </div>
</template>
