<script setup>
import { Users } from 'lucide-vue-next';
import Card from '@/Components/molecules/Card.vue';
import CardHeader from '@/Components/molecules/CardHeader.vue';
import CardContent from '@/Components/molecules/CardContent.vue';
import DropdownMenu from '@/Components/molecules/DropdownMenu.vue';
import DropdownMenuItem from '@/Components/molecules/DropdownMenuItem.vue';

const props = defineProps({
    group: {
        type: Object,
        required: true,
    },
    clickable: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['click', 'edit', 'manageMembers']);

const formatCreatedDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
};

const handleCardClick = () => {
    if (props.clickable) {
        emit('click', props.group);
    }
};

const handleEdit = () => {
    emit('edit', props.group);
};

const handleManageMembers = () => {
    emit('manageMembers', props.group);
};
</script>

<template>
    <Card
        :class="[
            'hover:shadow-md transition-all hover:border-gray-900',
            clickable && 'cursor-pointer'
        ]"
        @click="handleCardClick"
    >
        <CardHeader class="pb-3">
            <div class="flex items-start justify-between gap-2">
                <div class="flex items-start gap-3 flex-1 min-w-0">
                    <div
                        class="flex size-12 shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-600"
                    >
                        <Users class="size-6" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold truncate">{{ group.name }}</h3>
                        <p
                            v-if="group.description"
                            class="text-sm text-gray-500 line-clamp-2 mt-0.5"
                        >
                            {{ group.description }}
                        </p>
                        <p
                            v-else
                            class="text-sm text-gray-400 italic mt-0.5"
                        >
                            No description
                        </p>
                    </div>
                </div>
                <DropdownMenu @click.stop>
                    <template #content>
                        <DropdownMenuItem @click.stop="handleEdit">
                            Edit
                        </DropdownMenuItem>
                        <DropdownMenuItem @click.stop="handleManageMembers">
                            Manage Members
                        </DropdownMenuItem>
                    </template>
                </DropdownMenu>
            </div>
        </CardHeader>
        <CardContent class="space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <Users class="size-4" />
                    <span>{{ group.members_count ?? 0 }} {{ (group.members_count ?? 0) === 1 ? 'member' : 'members' }}</span>
                </div>
                <span class="text-xs text-gray-500">
                    Created {{ formatCreatedDate(group.created_at) }}
                </span>
            </div>
        </CardContent>
    </Card>
</template>
