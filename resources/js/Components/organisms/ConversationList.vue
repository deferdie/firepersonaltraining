<script setup>
import { Search } from 'lucide-vue-next';
import Input from '@/Components/atoms/Input.vue';
import ConversationItem from '@/Components/organisms/ConversationItem.vue';

const props = defineProps({
    conversations: {
        type: Array,
        default: () => [],
    },
    selectedId: {
        type: [Number, String],
        default: null,
    },
    effectiveSelected: {
        type: Object,
        default: null,
    },
    searchQuery: {
        type: String,
        default: '',
    },
});

const isSelected = (conv) => {
    if (conv.id) return props.selectedId === conv.id;
    return props.effectiveSelected && props.effectiveSelected.type === conv.type && props.effectiveSelected.targetId === conv.targetId;
};

defineEmits(['update:searchQuery', 'select']);
</script>

<template>
    <div class="w-96 border-r border-gray-200 flex flex-col bg-white">
        <div class="p-4 border-b border-gray-200">
            <h1 class="text-xl font-bold mb-4">Messages</h1>
            <div class="relative">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-500" />
                <input
                    :value="searchQuery"
                    type="text"
                    placeholder="Search conversations..."
                    class="flex h-10 w-full rounded-md border border-gray-200 bg-gray-50 pl-9 pr-3 py-2 text-sm placeholder:text-gray-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:ring-offset-0"
                    @input="$emit('update:searchQuery', $event.target.value)"
                />
            </div>
        </div>
        <div class="flex-1 overflow-y-auto">
            <ConversationItem
                v-for="conv in conversations"
                :key="conv.id ?? `${conv.type}-${conv.targetId}`"
                :conversation="conv"
                :is-selected="isSelected(conv)"
                @select="$emit('select', conv)"
            />
        </div>
    </div>
</template>
