<script setup>
import { computed } from 'vue';
import {
    Dumbbell,
    Clipboard,
    BookOpen,
    Target,
    ListChecks,
    Utensils,
    Flame,
    Calendar,
} from 'lucide-vue-next';
import DialogModal from '@/Components/molecules/DialogModal.vue';
import Button from '@/Components/atoms/Button.vue';
import Badge from '@/Components/atoms/Badge.vue';
import Progress from '@/Components/atoms/Progress.vue';

const CATEGORY_META = {
    programs: { name: 'Program', icon: Dumbbell },
    assessments: { name: 'Assessment', icon: Clipboard },
    content: { name: 'Content', icon: BookOpen },
    goals: { name: 'Goal', icon: Target },
    habits: { name: 'Habit', icon: ListChecks },
    nutrition: { name: 'Nutrition', icon: Utensils },
};

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    item: {
        type: Object,
        default: null,
    },
    clientId: {
        type: Number,
        default: null,
    },
});

const emit = defineEmits(['close', 'schedule']);

const meta = computed(() =>
    props.item ? CATEGORY_META[props.item.category] ?? { name: props.item.category, icon: BookOpen } : null
);

function formatDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function handleSchedule() {
    emit('schedule', props.item);
}

function handleClose() {
    emit('close');
}
</script>

<template>
    <DialogModal
        :is-open="isOpen"
        :title="item?.name ?? 'Content details'"
        description="View details and schedule this content"
        max-width="lg"
        @close="handleClose"
    >
        <template v-if="item">
            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <div
                        class="size-10 rounded-lg flex items-center justify-center shrink-0 bg-gray-100"
                    >
                        <component
                            :is="meta?.icon ?? BookOpen"
                            class="size-5 text-gray-700"
                        />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900">{{ item.name }}</p>
                        <p v-if="item.description" class="text-sm text-gray-500 mt-0.5">
                            {{ item.description }}
                        </p>
                        <Badge
                            :variant="
                                item.status === 'completed'
                                    ? 'default'
                                    : item.status === 'active'
                                    ? 'outline'
                                    : 'secondary'
                            "
                            :class="[
                                'text-xs mt-2',
                                item.status === 'completed'
                                    ? 'bg-gray-800 text-white'
                                    : item.status === 'active'
                                    ? 'border-gray-800 text-gray-800'
                                    : 'bg-gray-100 text-gray-600',
                            ]"
                        >
                            {{ item.status }}
                        </Badge>
                    </div>
                </div>

                <div v-if="item.progress !== undefined" class="space-y-1">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Progress</span>
                        <span class="font-semibold text-gray-900">{{ item.progress }}%</span>
                    </div>
                    <Progress :value="item.progress" />
                </div>

                <div v-if="item.streak !== undefined" class="flex items-center gap-2 text-sm">
                    <Flame class="size-4 text-orange-500 shrink-0" />
                    <span class="font-medium text-gray-900">{{ item.streak }} day streak</span>
                </div>

                <div class="text-sm text-gray-500 space-y-1">
                    <p v-if="item.assignedDate">
                        Assigned {{ formatDate(item.assignedDate) }}
                    </p>
                    <p v-if="item.dueDate">
                        Due {{ formatDate(item.dueDate) }}
                    </p>
                    <p v-if="item.completedDate" class="text-gray-800 font-medium">
                        Completed {{ formatDate(item.completedDate) }}
                    </p>
                </div>
            </div>
        </template>

        <template #footer>
            <div class="flex justify-end gap-2">
                <Button v-if="clientId != null" @click="handleSchedule">
                    <Calendar class="size-4 mr-2" />
                    Schedule
                </Button>
                <Button type="button" variant="outline" @click="handleClose">
                    Close
                </Button>
            </div>
        </template>
    </DialogModal>
</template>
