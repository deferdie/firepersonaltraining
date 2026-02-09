<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        required: true,
    },
    tabs: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['update:modelValue']);

const activeTab = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

const handleTabClick = (tabId) => {
    activeTab.value = tabId;
};
</script>

<template>
    <div class="bg-white border border-gray-200 rounded-lg p-1">
        <div class="flex gap-1 overflow-x-auto scrollbar-hide">
            <button
                v-for="tab in tabs"
                :key="tab.id"
                @click="handleTabClick(tab.id)"
                :class="[
                    'flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-md text-sm font-medium transition-all whitespace-nowrap',
                    activeTab === tab.id
                        ? 'bg-gray-900 text-white'
                        : 'text-gray-600 hover:bg-gray-50'
                ]"
            >
                <component v-if="tab.icon" :is="tab.icon" class="size-4" />
                {{ tab.label }}
            </button>
        </div>
    </div>
</template>

<style scoped>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
