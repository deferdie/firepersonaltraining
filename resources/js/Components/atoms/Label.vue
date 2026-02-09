<script setup>
defineProps({
    for: {
        type: String,
        default: '',
    },
    required: {
        type: Boolean,
        default: false,
    },
    className: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <label
        :for="for"
        :class="[
            'block text-sm font-medium text-gray-700',
            className
        ]"
    >
        <slot />
        <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>
</template>
