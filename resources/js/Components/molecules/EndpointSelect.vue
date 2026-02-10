<script setup>
import { ref, watch, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    endpoint: {
        type: String,
        required: true,
    },
    endpointParams: {
        type: Object,
        default: () => ({}),
    },
    queryParam: {
        type: String,
        default: null,
    },
    queryValue: {
        type: [String, Number],
        default: null,
    },
    valueKey: {
        type: String,
        default: 'id',
    },
    labelKey: {
        type: String,
        default: 'name',
    },
    placeholder: {
        type: String,
        default: 'Select...',
    },
    modelValue: {
        type: [String, Number, null],
        default: null,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue', 'select']);

const items = ref([]);
const loading = ref(false);
const error = ref(null);

const options = computed(() => items.value);

async function fetchOptions() {
    if (!props.endpoint) {
        items.value = [];
        return;
    }
    if (props.queryParam != null && (props.queryValue == null || props.queryValue === '')) {
        items.value = [];
        return;
    }

    const params = { ...props.endpointParams };
    if (props.queryParam != null && props.queryValue != null) {
        params[props.queryParam] = props.queryValue;
    }

    loading.value = true;
    error.value = null;
    try {
        const url = route(props.endpoint, props.endpointParams);
        const axiosParams = props.queryParam != null && props.queryValue != null
            ? { [props.queryParam]: props.queryValue }
            : {};
        const { data } = await axios.get(url, { params: axiosParams });
        items.value = data.items ?? data.habits ?? data.programs ?? [];
    } catch (e) {
        error.value = 'Could not load options';
        items.value = [];
    } finally {
        loading.value = false;
    }
}

watch(
    () => [props.endpoint, props.queryValue, JSON.stringify(props.endpointParams)],
    () => {
        fetchOptions();
    },
    { immediate: true }
);

function onSelect(e) {
    const value = e.target.value ? (isNaN(Number(e.target.value)) ? e.target.value : Number(e.target.value)) : null;
    emit('update:modelValue', value);
    const item = options.value.find((o) => o[props.valueKey] == value);
    if (item) {
        emit('select', item);
    }
}
</script>

<template>
    <select
        :value="modelValue"
        :disabled="disabled || loading"
        class="flex h-10 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-950 disabled:opacity-50"
        @change="onSelect"
    >
        <option :value="null">{{ placeholder }}</option>
        <option
            v-for="item in options"
            :key="item[valueKey]"
            :value="item[valueKey]"
        >
            {{ item[labelKey] }}
        </option>
    </select>
    <p v-if="loading" class="text-xs text-gray-500 mt-1">Loading...</p>
    <p v-else-if="error" class="text-xs text-red-600 mt-1">{{ error }}</p>
</template>
