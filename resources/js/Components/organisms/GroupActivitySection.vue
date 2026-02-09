<script setup>
import RecentActivityCard from '@/Components/molecules/RecentActivityCard.vue';

defineProps({
    recentActivity: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <RecentActivityCard
        :activities="recentActivity || []"
        variant="simple"
        title="All Group Activity"
    />
</template>
