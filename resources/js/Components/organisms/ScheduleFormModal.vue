<script setup>
import { ref, computed, watch } from 'vue';
import DialogModal from '@/Components/molecules/DialogModal.vue';
import Button from '@/Components/atoms/Button.vue';
import Input from '@/Components/atoms/Input.vue';
import Label from '@/Components/atoms/Label.vue';
import EndpointSelect from '@/Components/molecules/EndpointSelect.vue';
import axios from 'axios';

const SCHEDULABLE_TYPES = [
    { id: 'habit', label: 'Habit' },
];

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    clientId: {
        type: Number,
        required: true,
    },
    initialDate: {
        type: String,
        default: null,
    },
    editingEvent: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close', 'saved']);

const form = ref({
    schedulable_type: null,
    schedulable_id: null,
    title: '',
    notes: '',
    starts_at: '',
    ends_at: '',
    recurrence_mode: 'one_off',
    recurrence_interval: 1,
    recurrence_weekdays: [],
    recurrence_ends_at: '',
});
const errors = ref({});
const saving = ref(false);

const isEditing = computed(() => !!props.editingEvent);

const recurrenceModes = [
    { id: 'one_off', label: 'One-off' },
    { id: 'daily', label: 'Daily' },
    { id: 'weekly', label: 'Weekly' },
    { id: 'monthly', label: 'Monthly' },
    { id: 'weekdays', label: 'On selected days' },
];

const weekdays = [
    { value: 0, label: 'Sun' },
    { value: 1, label: 'Mon' },
    { value: 2, label: 'Tue' },
    { value: 3, label: 'Wed' },
    { value: 4, label: 'Thu' },
    { value: 5, label: 'Fri' },
    { value: 6, label: 'Sat' },
];

function resetForm() {
    form.value = {
        schedulable_type: null,
        schedulable_id: null,
        title: '',
        notes: '',
        starts_at: '',
        ends_at: '',
        recurrence_mode: 'one_off',
        recurrence_interval: 1,
        recurrence_weekdays: [],
        recurrence_ends_at: '',
    };
    errors.value = {};
}

function toggleWeekday(v) {
    const idx = form.value.recurrence_weekdays.indexOf(v);
    if (idx === -1) {
        form.value.recurrence_weekdays.push(v);
    } else {
        form.value.recurrence_weekdays.splice(idx, 1);
    }
}

function onContentSelect(item) {
    const typeLabel = SCHEDULABLE_TYPES.find((t) => t.id === form.value.schedulable_type)?.label ?? form.value.schedulable_type;
    form.value.title = `${typeLabel}: ${item.name}`;
}

watch(
    () => props.isOpen,
    (open) => {
        if (open) {
            resetForm();
            if (props.initialDate) {
                form.value.starts_at = props.initialDate;
                form.value.ends_at = '';
            }
            if (props.editingEvent) {
                form.value.title = props.editingEvent.title;
                form.value.notes = props.editingEvent.extendedProps?.notes ?? props.editingEvent.notes ?? '';
                form.value.starts_at = props.editingEvent.start?.slice(0, 16) ?? '';
                form.value.ends_at = props.editingEvent.end?.slice(0, 16) ?? '';
            }
        }
    }
);

async function submit() {
    errors.value = {};
    if (!isEditing.value) {
        if (!form.value.schedulable_type) {
            errors.value.schedulable_type = 'Select a content type';
            return;
        }
        if (!form.value.schedulable_id) {
            errors.value.schedulable_id = 'Select content to schedule';
            return;
        }
    }
    if (!form.value.title) {
        errors.value.title = 'Title is required';
        return;
    }
    if (!form.value.starts_at) {
        errors.value.starts_at = 'Start date/time is required';
        return;
    }

    saving.value = true;
    try {
        if (isEditing.value) {
            await axios.patch(
                route('trainer.clients.schedules.update', {
                    client: props.clientId,
                    schedule: props.editingEvent.scheduleId,
                }),
                {
                    title: form.value.title,
                    notes: form.value.notes || null,
                    starts_at: form.value.starts_at,
                    ends_at: form.value.ends_at || null,
                }
            );
        } else {
            await axios.post(
                route('trainer.clients.schedules.store', { client: props.clientId }),
                {
                    schedulable_type: form.value.schedulable_type,
                    schedulable_id: form.value.schedulable_id,
                    title: form.value.title,
                    notes: form.value.notes || null,
                    starts_at: form.value.starts_at,
                    ends_at: form.value.ends_at || null,
                    recurrence_mode: form.value.recurrence_mode,
                    recurrence_interval: form.value.recurrence_interval,
                    recurrence_weekdays: form.value.recurrence_weekdays,
                    recurrence_ends_at: form.value.recurrence_ends_at || null,
                }
            );
        }
        emit('saved');
        emit('close');
    } catch (e) {
        if (e.response?.data?.errors) {
            errors.value = e.response.data.errors;
        } else {
            errors.value = { form: 'Something went wrong. Please try again.' };
        }
    } finally {
        saving.value = false;
    }
}

async function deleteSchedule() {
    if (!props.editingEvent?.scheduleId) return;
    saving.value = true;
    try {
        await axios.delete(
            route('trainer.clients.schedules.destroy', {
                client: props.clientId,
                schedule: props.editingEvent.scheduleId,
            })
        );
        emit('saved');
        emit('close');
    } catch (e) {
        errors.value = { form: 'Could not delete. Please try again.' };
    } finally {
        saving.value = false;
    }
}

function close() {
    emit('close');
}
</script>

<template>
    <DialogModal
        :is-open="isOpen"
        :title="isEditing ? 'Edit schedule' : 'Schedule content'"
        description="Set when this content should appear on the calendar"
        max-width="2xl"
        @close="close"
    >
        <form @submit.prevent="submit" class="space-y-4">
            <p v-if="errors.form" class="text-sm text-red-600">{{ errors.form }}</p>

            <div v-if="!isEditing" class="space-y-4">
                <div class="space-y-2">
                    <Label for="schedulable_type" required>Content type</Label>
                    <select
                        id="schedulable_type"
                        v-model="form.schedulable_type"
                        class="flex h-10 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-950"
                    >
                        <option :value="null">Select content you want to schedule</option>
                        <option
                            v-for="t in SCHEDULABLE_TYPES"
                            :key="t.id"
                            :value="t.id"
                        >
                            {{ t.label }}
                        </option>
                    </select>
                    <p v-if="errors.schedulable_type" class="text-xs text-red-600">{{ errors.schedulable_type }}</p>
                </div>

                <div v-if="form.schedulable_type" class="space-y-2">
                    <Label for="schedulable_id" required>Content</Label>
                    <EndpointSelect
                        id="schedulable_id"
                        endpoint="trainer.clients.assigned-content.index"
                        :endpoint-params="{ client: props.clientId }"
                        query-param="type"
                        :query-value="form.schedulable_type"
                        v-model="form.schedulable_id"
                        placeholder="Select content..."
                        @select="onContentSelect"
                    />
                    <p v-if="errors.schedulable_id" class="text-xs text-red-600">{{ errors.schedulable_id }}</p>
                </div>
            </div>

            <div class="space-y-2">
                <Label for="title" required>Title</Label>
                <Input
                    id="title"
                    v-model="form.title"
                    placeholder="e.g. Habit: Morning walk"
                />
                <p v-if="errors.title" class="text-xs text-red-600">{{ errors.title }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <Label for="starts_at" required>Start</Label>
                    <Input
                        id="starts_at"
                        v-model="form.starts_at"
                        type="datetime-local"
                    />
                    <p v-if="errors.starts_at" class="text-xs text-red-600">{{ errors.starts_at }}</p>
                </div>
                <div class="space-y-2">
                    <Label for="ends_at">End (optional)</Label>
                    <Input
                        id="ends_at"
                        v-model="form.ends_at"
                        type="datetime-local"
                    />
                </div>
            </div>

            <template v-if="!isEditing">
                <div class="space-y-2">
                    <Label>Repeat</Label>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="m in recurrenceModes"
                            :key="m.id"
                            type="button"
                            :class="[
                                'px-3 py-1.5 rounded-md text-sm font-medium transition-colors',
                                form.recurrence_mode === m.id
                                    ? 'bg-gray-900 text-white'
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
                            ]"
                            @click="form.recurrence_mode = m.id"
                        >
                            {{ m.label }}
                        </button>
                    </div>
                </div>

                <div
                    v-if="form.recurrence_mode === 'weekdays'"
                    class="space-y-2"
                >
                    <Label>On days</Label>
                    <div class="flex gap-2">
                        <button
                            v-for="d in weekdays"
                            :key="d.value"
                            type="button"
                            :class="[
                                'px-3 py-1.5 rounded-md text-sm font-medium transition-colors',
                                form.recurrence_weekdays.includes(d.value)
                                    ? 'bg-gray-900 text-white'
                                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200',
                            ]"
                            @click="toggleWeekday(d.value)"
                        >
                            {{ d.label }}
                        </button>
                    </div>
                </div>

                <div
                    v-if="['weekly', 'monthly'].includes(form.recurrence_mode)"
                    class="space-y-2"
                >
                    <Label for="recurrence_interval">Every</Label>
                    <div class="flex items-center gap-2">
                        <Input
                            id="recurrence_interval"
                            v-model.number="form.recurrence_interval"
                            type="number"
                            min="1"
                            class="w-20"
                        />
                        <span class="text-sm text-gray-600">
                            {{ form.recurrence_mode === 'weekly' ? 'week(s)' : 'month(s)' }}
                        </span>
                    </div>
                </div>

                <div
                    v-if="form.recurrence_mode !== 'one_off'"
                    class="space-y-2"
                >
                    <Label for="recurrence_ends_at">Repeat until (optional)</Label>
                    <Input
                        id="recurrence_ends_at"
                        v-model="form.recurrence_ends_at"
                        type="date"
                    />
                </div>
            </template>

            <div class="space-y-2">
                <Label for="notes">Notes (optional)</Label>
                <textarea
                    id="notes"
                    v-model="form.notes"
                    rows="2"
                    class="flex w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-950"
                    placeholder="Add any notes..."
                />
            </div>
        </form>

        <template #footer>
            <div class="flex justify-between">
                <div>
                    <Button
                        v-if="isEditing"
                        type="button"
                        variant="danger"
                        :disabled="saving"
                        @click="deleteSchedule"
                    >
                        Delete
                    </Button>
                </div>
                <div class="flex gap-2">
                    <Button type="button" variant="outline" @click="close">
                        Cancel
                    </Button>
                    <Button type="button" :disabled="saving" @click="submit">
                        {{ saving ? 'Saving...' : (isEditing ? 'Update' : 'Schedule') }}
                    </Button>
                </div>
            </div>
        </template>
    </DialogModal>
</template>
