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
            'relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none transition-colors hover:bg-gray-100 focus:bg-gray-100 data-[disabled]:pointer-events-none data-[disabled]:opacity-50',
            className
        ]"
    >
        <slot />
    </div>
</template>
