<script setup>
import { inject, computed } from 'vue';
import { ChevronDown } from 'lucide-vue-next';
import Button from '@/Components/atoms/Button.vue';

const select = inject('select');

const displayValue = computed(() => {
    if (select.selectedValue === null || select.selectedValue === undefined || select.selectedValue === '') {
        return select.placeholder;
    }
    return select.selectedLabel || select.selectedValue;
});

const handleClick = (event) => {
    event.stopPropagation();
    if (!select.disabled) {
        select.open = !select.open;
    }
};
</script>

<template>
    <Button
        variant="outline"
        class="w-full justify-between"
        :disabled="select.disabled"
        @click.stop="handleClick"
    >
        <span :class="{ 'text-gray-500': !select.selectedValue }">
            {{ displayValue }}
        </span>
        <ChevronDown class="size-4 opacity-50" />
    </Button>
</template>
