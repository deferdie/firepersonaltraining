<script setup>
defineProps({
    className: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div
        :class="[
            'rounded-lg border border-gray-200 bg-white text-gray-950 shadow-sm',
            className
        ]"
    >
        <slot />
    </div>
</template>
