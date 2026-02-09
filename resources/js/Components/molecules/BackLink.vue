<script setup>
import { Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

defineProps({
    href: {
        type: [String, Object],
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition-colors"
    >
        <ArrowLeft class="size-4 mr-2 shrink-0" />
        {{ label }}
    </Link>
</template>
