<script setup>
import { ref, watch } from 'vue';
import { ListChecks, Dumbbell } from 'lucide-vue-next';
import Button from '@/Components/atoms/Button.vue';
import ContentSelectionMenu from '@/Components/organisms/ContentSelectionMenu.vue';
import HabitContentStepper from '@/Components/organisms/HabitContentStepper.vue';
import ProgramContentStepper from '@/Components/organisms/ProgramContentStepper.vue';

const props = defineProps({
    assignableType: {
        type: String,
        required: true,
        validator: (v) => ['client', 'group'].includes(v),
    },
    assignableId: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(['assigned']);

const phase = ref('select-type');
const selectedContentType = ref(null);

const contentTypes = [
    { id: 'habits', label: 'Habit', icon: ListChecks },
    { id: 'programs', label: 'Program', icon: Dumbbell },
];

const habitStepperRef = ref(null);
const programStepperRef = ref(null);

function goToContentFlow() {
    if (selectedContentType.value) {
        phase.value = 'content-flow';
    }
}

function onBack() {
    phase.value = 'select-type';
    selectedContentType.value = null;
    habitStepperRef.value?.reset?.();
    programStepperRef.value?.reset?.();
}

function onSubmit() {
    emit('assigned');
}

watch(
    () => props.assignableId,
    () => {
        phase.value = 'select-type';
        selectedContentType.value = null;
    }
);
</script>

<template>
    <div class="space-y-6">
        <!-- Phase 1: Content type selection -->
        <template v-if="phase === 'select-type'">
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-gray-900 text-white">
                    1. Content type
                </div>
            </div>
            <ContentSelectionMenu
                v-model="selectedContentType"
                :content-types="contentTypes"
            />
            <div class="flex justify-end pt-4 border-t border-gray-200">
                <Button
                    type="button"
                    :disabled="!selectedContentType"
                    @click="goToContentFlow"
                >
                    Next
                </Button>
            </div>
        </template>

        <!-- Phase 2: Type-specific stepper -->
        <template v-else>
            <HabitContentStepper
                v-if="selectedContentType === 'habits'"
                ref="habitStepperRef"
                :assignable-type="assignableType"
                :assignable-id="assignableId"
                @back="onBack"
                @submit="onSubmit"
            />
            <ProgramContentStepper
                v-else-if="selectedContentType === 'programs'"
                ref="programStepperRef"
                :assignable-type="assignableType"
                :assignable-id="assignableId"
                @back="onBack"
                @submit="onSubmit"
            />
        </template>
    </div>
</template>
