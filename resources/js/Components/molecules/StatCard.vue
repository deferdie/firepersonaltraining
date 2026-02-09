<script setup>
import Card from '@/Components/molecules/Card.vue';
import CardHeader from '@/Components/molecules/CardHeader.vue';
import CardContent from '@/Components/molecules/CardContent.vue';
import CardTitle from '@/Components/molecules/CardTitle.vue';

defineProps({
    title: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        required: true,
    },
    icon: {
        type: Object,
        required: true,
    },
    className: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <Card :class="className">
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">{{ title }}</CardTitle>
            <component :is="icon" class="size-4 text-gray-500" />
        </CardHeader>
        <CardContent>
            <div class="text-2xl font-bold">{{ value }}</div>
        </CardContent>
    </Card>
</template>
