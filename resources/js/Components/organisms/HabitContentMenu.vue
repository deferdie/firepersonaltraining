<script setup>
const props = defineProps({
    modelValue: {
        type: String,
        default: null,
        validator: (v) => v === null || v === 'create' || v === 'library',
    },
    assignableLabel: {
        type: String,
        default: 'client',
    },
});

const emit = defineEmits(['update:modelValue']);

function select(choice) {
    emit('update:modelValue', choice);
}
</script>

<template>
    <div class="space-y-3">
        <p class="text-sm text-gray-600">
            Create a new habit or pick one from your library.
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <button
                type="button"
                :class="[
                    'flex flex-col gap-1 p-4 rounded-lg border-2 text-left transition-all',
                    modelValue === 'create'
                        ? 'border-gray-900 bg-gray-50'
                        : 'border-gray-200 hover:border-gray-300',
                ]"
                @click="select('create')"
            >
                <span class="font-medium">Create new</span>
                <span class="text-sm text-gray-500">
                    Add a new habit for this {{ assignableLabel }}
                </span>
            </button>
            <button
                type="button"
                :class="[
                    'flex flex-col gap-1 p-4 rounded-lg border-2 text-left transition-all',
                    modelValue === 'library'
                        ? 'border-gray-900 bg-gray-50'
                        : 'border-gray-200 hover:border-gray-300',
                ]"
                @click="select('library')"
            >
                <span class="font-medium">From library</span>
                <span class="text-sm text-gray-500">
                    Copy an existing habit from your library
                </span>
            </button>
        </div>
    </div>
</template>
