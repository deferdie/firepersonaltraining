<script setup>
import { ref, watch, onUnmounted } from 'vue';
import { Search } from 'lucide-vue-next';
import Input from '@/Components/atoms/Input.vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: 'Search...',
    },
    debounce: {
        type: Number,
        default: 300,
    },
});

const emit = defineEmits(['update:modelValue', 'search']);

const searchQuery = ref(props.modelValue);
let debounceTimer = null;

watch(() => props.modelValue, (newValue) => {
    searchQuery.value = newValue;
});

const handleInput = (value) => {
    searchQuery.value = value;
    emit('update:modelValue', value);
    
    // Debounce search event
    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }
    
    debounceTimer = setTimeout(() => {
        emit('search', value);
    }, props.debounce);
};

onUnmounted(() => {
    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }
});
</script>

<template>
    <div class="relative max-w-md">
        <Input
            :model-value="searchQuery"
            :placeholder="placeholder"
            :has-icon="true"
            @update:model-value="handleInput"
        >
            <template #icon>
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-500 pointer-events-none" />
            </template>
        </Input>
    </div>
</template>
