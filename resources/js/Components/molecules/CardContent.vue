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
            'p-6 pt-0',
            className
        ]"
    >
        <slot />
    </div>
</template>
