<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['active', 'inactive', 'trial', 'default'].includes(value),
    },
    className: {
        type: String,
        default: '',
    },
});

const variantClasses = computed(() => {
    const variants = {
        active: 'bg-green-500 text-white',
        trial: 'bg-blue-500 text-white',
        inactive: 'bg-gray-200 text-gray-700',
        default: 'bg-gray-100 text-gray-700',
    };
    return variants[props.variant];
});
</script>

<template>
    <span
        :class="[
            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
            variantClasses,
            className
        ]"
    >
        <slot />
    </span>
</template>
