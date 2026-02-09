<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'outline', 'ghost'].includes(value),
    },
    size: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'icon', 'sm', 'lg'].includes(value),
    },
    type: {
        type: String,
        default: 'button',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const classes = computed(() => {
    const baseClasses = 'inline-flex items-center justify-center rounded-md font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';
    
    // Variant styles
    const variantClasses = {
        default: 'bg-gray-900 text-white hover:bg-gray-800 focus:ring-gray-500',
        outline: 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 hover:border-gray-400 focus:ring-gray-500',
        ghost: 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:ring-gray-500',
    };
    
    // Size styles
    const sizeClasses = {
        default: 'px-4 py-2 text-sm gap-3 h-11',
        sm: 'px-3 py-1.5 text-xs gap-2 h-9',
        lg: 'px-6 py-3 text-base gap-3 h-12',
        icon: 'p-2 h-10 w-10',
    };
    
    return `${baseClasses} ${variantClasses[props.variant]} ${sizeClasses[props.size]}`;
});
</script>

<template>
    <button
        :type="type"
        :class="classes"
        :disabled="disabled"
    >
        <slot />
    </button>
</template>
