<script setup>
defineProps({
    className: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <p
        :class="[
            'text-sm text-gray-500',
            className
        ]"
    >
        <slot />
    </p>
</template>
