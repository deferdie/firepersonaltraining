<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import SearchInput from '@/Components/molecules/SearchInput.vue';

const ROUTE_BY_TYPE = {
    habits: 'trainer.library.habits.list',
    programs: 'trainer.library.programs.list',
};

const EMPTY_MESSAGE_BY_TYPE = {
    habits: 'No habits found. Create some in your library first.',
    programs: 'No programs found. Create some in your library first.',
};

const SEARCH_PLACEHOLDER_BY_TYPE = {
    habits: 'Search habits...',
    programs: 'Search programs...',
};

const props = defineProps({
    contentType: {
        type: String,
        required: true,
        validator: (v) => ['habits', 'programs'].includes(v),
    },
    selectedId: {
        type: [Number, String],
        default: null,
    },
});

const emit = defineEmits(['select']);

const items = ref([]);
const searchQuery = ref('');
const loading = ref(false);

function fetchList(search) {
    const routeName = ROUTE_BY_TYPE[props.contentType];
    if (!routeName) {
        items.value = [];
        return;
    }
    let url;
    try {
        url = route(routeName);
    } catch {
        items.value = [];
        return;
    }
    if (!url) {
        items.value = [];
        return;
    }
    const q = search !== undefined ? search : searchQuery.value;
    loading.value = true;
    axios
        .get(url, {
            params: { search: q || undefined },
        })
        .then(({ data }) => {
            const key = props.contentType === 'habits' ? 'habits' : 'programs';
            items.value = data[key] ?? data.items ?? [];
        })
        .finally(() => {
            loading.value = false;
        });
}

watch(
    () => [props.contentType],
    () => {
        fetchList('');
    },
    { immediate: true }
);

function onSearch(q) {
    fetchList(q);
}

function selectItem(item) {
    emit('select', item);
}

const searchPlaceholder = () => SEARCH_PLACEHOLDER_BY_TYPE[props.contentType] ?? 'Search...';
const emptyMessage = () => EMPTY_MESSAGE_BY_TYPE[props.contentType] ?? 'No items found.';
</script>

<template>
    <div class="space-y-4">
        <SearchInput
            v-model="searchQuery"
            :placeholder="searchPlaceholder()"
            @search="onSearch"
        />
        <div class="max-h-60 overflow-y-auto space-y-2 border border-gray-200 rounded-lg p-2">
            <div v-if="loading" class="py-8 text-center text-sm text-gray-500">
                Loading...
            </div>
            <template v-else-if="items.length === 0">
                <div class="py-8 text-center text-sm text-gray-500">
                    {{ emptyMessage() }}
                </div>
            </template>
            <button
                v-else
                v-for="item in items"
                :key="item.id"
                type="button"
                :class="[
                    'w-full flex flex-col gap-0.5 p-3 rounded-lg border text-left transition-all',
                    selectedId === item.id ? 'border-gray-900 bg-gray-50' : 'border-gray-200 hover:border-gray-300',
                ]"
                @click="selectItem(item)"
            >
                <span class="font-medium">{{ item.name }}</span>
                <span v-if="item.description" class="text-sm text-gray-500 line-clamp-2">
                    {{ item.description }}
                </span>
            </button>
        </div>
    </div>
</template>
