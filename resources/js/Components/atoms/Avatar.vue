<script setup>
import { computed } from 'vue';

const props = defineProps({
    initials: {
        type: String,
        required: true,
    },
    color: {
        type: String,
        default: 'bg-gray-500',
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value),
    },
});

const sizeClasses = computed(() => {
    const sizes = {
        sm: 'size-8 text-xs',
        md: 'size-10 text-sm',
        lg: 'size-12 text-base',
        xl: 'size-16 text-lg',
    };
    return sizes[props.size];
});
</script>

<template>
    <div
        :class="[
            'rounded-full flex items-center justify-center font-medium text-white',
            color,
            sizeClasses
        ]"
    >
        {{ initials }}
    </div>
</template>
