<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { ChevronRight, ChevronLeft } from 'lucide-vue-next';
import Button from '@/Components/atoms/Button.vue';
import HabitContentMenu from '@/Components/organisms/HabitContentMenu.vue';
import HabitCreate from '@/Components/organisms/HabitCreate.vue';
import LibraryContentSelector from '@/Components/molecules/LibraryContentSelector.vue';

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

const emit = defineEmits(['back', 'submit']);

const step = ref(0);
const sourceChoice = ref(null);
const selectedLibraryItem = ref(null);

const form = useForm({
    name: '',
    description: '',
    library_habit_id: null,
});

const assignRoute = computed(() => {
    if (props.assignableType === 'client') {
        return route('trainer.clients.habits.store', props.assignableId);
    }
    return route('trainer.groups.habits.store', props.assignableId);
});

const assignableLabel = computed(() =>
    props.assignableType === 'client' ? 'client' : 'group'
);

const steps = [
    { id: 0, label: 'Source' },
    { id: 1, label: 'Details' },
    { id: 2, label: 'Review' },
];

const reviewName = computed(() => {
    if (form.library_habit_id && selectedLibraryItem.value) {
        return selectedLibraryItem.value.name;
    }
    return form.name;
});

const reviewDescription = computed(() => {
    if (form.library_habit_id && selectedLibraryItem.value) {
        return selectedLibraryItem.value.description ?? '';
    }
    return form.description ?? '';
});

const isFromLibrary = computed(() => !!form.library_habit_id);

const canProceedFromStep0 = computed(() => !!sourceChoice.value);
const canProceedFromStep1 = computed(() => {
    if (sourceChoice.value === 'create') {
        return !!form.name?.trim();
    }
    return !!form.library_habit_id;
});

function nextStep() {
    if (step.value === 0 && sourceChoice.value === 'create') {
        form.library_habit_id = null;
        selectedLibraryItem.value = null;
    }
    if (step.value === 0 && sourceChoice.value === 'library') {
        form.name = '';
        form.description = '';
    }
    if (step.value < 2) step.value++;
}

function prevStep() {
    if (step.value === 0) {
        emit('back');
        return;
    }
    step.value--;
}

function onLibrarySelect(item) {
    selectedLibraryItem.value = item;
    form.library_habit_id = item.id;
    form.name = '';
    form.description = '';
}

function handleSubmit() {
    form.post(assignRoute.value, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            selectedLibraryItem.value = null;
            emit('submit');
        },
    });
}

function reset() {
    step.value = 0;
    sourceChoice.value = null;
    selectedLibraryItem.value = null;
    form.reset();
    form.clearErrors();
}

defineExpose({ reset });
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center gap-2">
            <template v-for="(s, idx) in steps" :key="s.id">
                <div
                    :class="[
                        'flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium',
                        step >= s.id ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-500',
                    ]"
                >
                    {{ s.id + 1 }}. {{ s.label }}
                </div>
                <ChevronRight v-if="idx < steps.length - 1" class="size-4 text-gray-300" />
            </template>
        </div>

        <!-- Step 0: Source -->
        <div v-if="step === 0">
            <HabitContentMenu
                v-model="sourceChoice"
                :assignable-label="assignableLabel"
            />
        </div>

        <!-- Step 1: Create or Library -->
        <div v-if="step === 1" class="space-y-4">
            <HabitCreate
                v-if="sourceChoice === 'create'"
                :model-value="{ name: form.name, description: form.description }"
                :disabled="form.processing"
                :errors="form.errors"
                @update:model-value="(v) => { form.name = v.name; form.description = v.description }"
            />
            <template v-else>
                <p class="text-sm text-gray-600">
                    Select a habit from your library. It will be copied here.
                </p>
                <LibraryContentSelector
                    content-type="habits"
                    :selected-id="form.library_habit_id"
                    @select="onLibrarySelect"
                />
            </template>
        </div>

        <!-- Step 2: Review -->
        <div
            v-if="step === 2"
            class="space-y-4 rounded-lg border border-gray-200 bg-gray-50 p-4"
        >
            <p class="text-sm text-gray-600">Review and assign.</p>
            <dl class="space-y-2 text-sm">
                <div>
                    <dt class="text-gray-500">Type</dt>
                    <dd class="font-medium">Habit</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Source</dt>
                    <dd class="font-medium">{{ isFromLibrary ? 'From library' : 'New habit' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Name</dt>
                    <dd class="font-medium">{{ reviewName || '—' }}</dd>
                </div>
                <div v-if="reviewDescription">
                    <dt class="text-gray-500">Description</dt>
                    <dd class="text-gray-700">{{ reviewDescription }}</dd>
                </div>
            </dl>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
            <Button
                type="button"
                variant="outline"
                :disabled="form.processing"
                @click="prevStep"
            >
                <ChevronLeft class="size-4 mr-1" />
                Back
            </Button>
            <div class="flex gap-2">
                <Button
                    v-if="step < 2"
                    type="button"
                    :disabled="
                        form.processing ||
                        (step === 0 && !canProceedFromStep0) ||
                        (step === 1 && !canProceedFromStep1)
                    "
                    @click="nextStep"
                >
                    Next
                    <ChevronRight class="size-4 ml-1" />
                </Button>
                <template v-else>
                    <Button
                        type="button"
                        :disabled="form.processing"
                        @click="handleSubmit"
                    >
                        {{ form.processing ? 'Assigning...' : 'Assign' }}
                    </Button>
                </template>
            </div>
        </div>
    </div>
</template>
