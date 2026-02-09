<script setup>
import { inject, computed } from 'vue';

const props = defineProps({
    value: {
        type: [String, Number, null],
        default: null,
    },
    label: {
        type: String,
        default: '',
    },
});

const select = inject('select');

const isSelected = computed(() => {
    // Handle null/undefined comparison
    if (select.selectedValue === null || select.selectedValue === undefined) {
        return props.value === null || props.value === undefined;
    }
    return select.selectedValue === props.value;
});

const handleClick = (event) => {
    event.stopPropagation();
    const label = props.label || props.value;
    select.selectValue(props.value, label);
};
</script>

<template>
    <div
        :class="[
            'relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none transition-colors hover:bg-gray-100 focus:bg-gray-100',
            isSelected && 'bg-gray-100'
        ]"
        @click.stop="handleClick"
    >
        <slot />
    </div>
</template>
