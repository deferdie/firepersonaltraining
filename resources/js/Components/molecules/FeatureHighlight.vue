<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-200 rounded-xl p-6">
        <div class="flex items-start gap-4">
            <div
                v-if="$slots.icon"
                class="size-12 bg-white rounded-lg flex items-center justify-center shrink-0 shadow-sm"
            >
                <slot name="icon" />
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-lg mb-2">{{ title }}</h3>
                <p v-if="description" class="text-sm text-gray-500 mb-4">
                    {{ description }}
                </p>
                <div v-if="$slots.default" class="flex flex-wrap gap-2">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
