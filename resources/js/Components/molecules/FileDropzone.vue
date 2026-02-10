<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    id: {
        type: String,
        default: '',
    },
    modelValue: {
        type: File,
        default: null,
    },
    accept: {
        type: String,
        default: '',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    required: {
        type: Boolean,
        default: false,
    },
    placeholder: {
        type: String,
        default: 'Drag and drop a file here, or click to browse.',
    },
    hint: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);

const inputRef = ref(null);
const isDragOver = ref(false);

const hasFile = computed(() => props.modelValue != null);

const fileLabel = computed(() => {
    if (!props.modelValue) return '';
    const name = props.modelValue.name;
    const size = props.modelValue.size;
    const sizeStr = size < 1024
        ? `${size} B`
        : size < 1024 * 1024
            ? `${(size / 1024).toFixed(1)} KB`
            : `${(size / (1024 * 1024)).toFixed(1)} MB`;
    return `${name} · ${sizeStr}`;
});

const setFile = (file) => {
    emit('update:modelValue', file ?? null);
    if (inputRef.value) {
        inputRef.value.value = '';
    }
};

const handleDrop = (e) => {
    e.preventDefault();
    isDragOver.value = false;
    if (props.disabled) return;
    const file = e.dataTransfer?.files?.[0];
    if (file) setFile(file);
};

const handleDragOver = (e) => {
    e.preventDefault();
    if (props.disabled) return;
    isDragOver.value = true;
};

const handleDragLeave = () => {
    isDragOver.value = false;
};

const handleClick = () => {
    if (props.disabled) return;
    inputRef.value?.click();
};

const handleInputChange = (e) => {
    const file = e.target?.files?.[0];
    setFile(file ?? null);
};

const clearFile = (e) => {
    e.stopPropagation();
    if (props.disabled) return;
    setFile(null);
};
</script>

<template>
    <div
        :id="id || undefined"
        role="button"
        tabindex="0"
        :aria-label="placeholder"
        :aria-required="required"
        :class="[
            'flex min-h-[120px] w-full cursor-pointer flex-col items-center justify-center rounded-md border border-dashed px-4 py-6 text-center text-sm transition-colors',
            'border-gray-300 bg-white ring-offset-white',
            'hover:border-gray-400 hover:bg-gray-50/50',
            'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2',
            isDragOver && 'border-gray-500 bg-gray-50',
            disabled && 'cursor-not-allowed opacity-50 pointer-events-none',
        ]"
        @click="handleClick"
        @drop="handleDrop"
        @dragover="handleDragOver"
        @dragleave="handleDragLeave"
        @keydown.enter.prevent="handleClick"
        @keydown.space.prevent="handleClick"
    >
        <input
            ref="inputRef"
            type="file"
            :accept="accept"
            class="sr-only"
            :disabled="disabled"
            aria-hidden="true"
            @change="handleInputChange"
        />

        <template v-if="hasFile">
            <p class="font-medium text-gray-900 truncate max-w-full" :title="modelValue?.name">
                {{ fileLabel }}
            </p>
            <button
                type="button"
                class="mt-2 text-sm text-gray-500 underline hover:text-gray-700 focus:outline-none focus:underline"
                @click="clearFile"
            >
                Remove
            </button>
        </template>

        <template v-else>
            <p class="text-gray-500">
                {{ placeholder }}
            </p>
            <p v-if="hint" class="mt-1 text-xs text-gray-400">
                {{ hint }}
            </p>
        </template>
    </div>
</template>
