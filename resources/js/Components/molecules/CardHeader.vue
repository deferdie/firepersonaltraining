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
            'flex flex-col space-y-1.5 p-6',
            className
        ]"
    >
        <slot />
    </div>
</template>
