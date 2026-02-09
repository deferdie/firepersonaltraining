<script setup>
import { computed } from 'vue';

const props = defineProps({
    icon: {
        type: Object,
        required: true,
    },
    title: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
    action: {
        type: Object,
        default: null,
    },
});

const hasAction = computed(() => props.action !== null);
</script>

<template>
    <div class="text-center py-12">
        <component :is="icon" class="size-12 mx-auto text-gray-400 mb-3" />
        <h3 v-if="title" class="text-lg font-semibold text-gray-900 mb-1">
            {{ title }}
        </h3>
        <p class="text-gray-500">
            {{ description }}
        </p>
        <div v-if="hasAction" class="mt-4">
            <slot name="action" />
        </div>
    </div>
</template>
