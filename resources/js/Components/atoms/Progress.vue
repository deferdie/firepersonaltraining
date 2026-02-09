<script setup>
import { computed } from 'vue';

const props = defineProps({
    value: {
        type: Number,
        required: true,
        validator: (value) => value >= 0 && value <= 100,
    },
    className: {
        type: String,
        default: '',
    },
});

const width = computed(() => `${Math.min(100, Math.max(0, props.value))}%`);
</script>

<template>
    <div :class="['h-2.5 bg-gray-100 rounded-full overflow-hidden', className]">
        <div
            class="h-full bg-gray-800 rounded-full transition-all"
            :style="{ width: width }"
        />
    </div>
</template>
