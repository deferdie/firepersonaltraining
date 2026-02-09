<script setup>
import Card from '@/Components/molecules/Card.vue';
import CardContent from '@/Components/molecules/CardContent.vue';
import CardHeader from '@/Components/molecules/CardHeader.vue';

defineProps({
    icon: {
        type: Object,
        default: null,
    },
    title: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Card>
        <CardHeader>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <component v-if="icon" :is="icon" class="size-5 text-gray-900" />
                    <h2 class="text-lg font-semibold">{{ title }}</h2>
                </div>
                <slot name="action" />
            </div>
        </CardHeader>
        <CardContent>
            <slot />
        </CardContent>
    </Card>
</template>
