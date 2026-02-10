<script setup>
const props = defineProps({
    contentTypes: {
        type: Array,
        required: true,
    },
    modelValue: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(['update:modelValue']);

function select(id) {
    emit('update:modelValue', id);
}
</script>

<template>
    <div class="space-y-3">
        <p class="text-sm text-gray-600">Choose what to assign.</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <button
                v-for="ct in contentTypes"
                :key="ct.id"
                type="button"
                :class="[
                    'flex items-center gap-3 p-4 rounded-lg border-2 text-left transition-all',
                    modelValue === ct.id
                        ? 'border-gray-900 bg-gray-50'
                        : 'border-gray-200 hover:border-gray-300',
                ]"
                @click="select(ct.id)"
            >
                <component :is="ct.icon" class="size-5 text-gray-700" />
                <span class="font-medium">{{ ct.label }}</span>
            </button>
        </div>
    </div>
</template>
