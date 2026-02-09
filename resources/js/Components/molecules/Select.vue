<script setup>
import { ref, provide, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number, null],
        default: null,
    },
    placeholder: {
        type: String,
        default: 'Select...',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue']);

const open = ref(false);
const selectedValue = ref(props.modelValue);
const selectedLabel = ref(null);

const selectValue = (value, label) => {
    selectedValue.value = value;
    selectedLabel.value = label;
    emit('update:modelValue', value);
    open.value = false;
};

const close = () => {
    open.value = false;
};

provide('select', {
    open,
    selectedValue,
    selectedLabel,
    placeholder: props.placeholder,
    disabled: props.disabled,
    selectValue,
    close,
});

watch(() => props.modelValue, (newValue) => {
    selectedValue.value = newValue;
    if (!newValue) {
        selectedLabel.value = null;
    }
});
</script>

<template>
    <div class="relative select-container">
        <slot />
    </div>
</template>
