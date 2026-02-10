<script setup>
import Label from '@/Components/atoms/Label.vue';
import Input from '@/Components/atoms/Input.vue';
import Textarea from '@/Components/atoms/Textarea.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({ name: '', description: '' }),
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['update:modelValue']);

function update(field, value) {
    emit('update:modelValue', { ...props.modelValue, [field]: value });
}
</script>

<template>
    <div class="space-y-4">
        <p class="text-sm text-gray-600">Enter the habit details.</p>
        <div class="space-y-2">
            <Label for="habit-create-name" :required="true">Name</Label>
            <Input
                id="habit-create-name"
                :model-value="modelValue?.name ?? ''"
                placeholder="e.g. Drink 8 glasses of water"
                :disabled="disabled"
                @update:model-value="(v) => update('name', v)"
            />
            <InputError :message="errors?.name" />
        </div>
        <div class="space-y-2">
            <Label for="habit-create-description">Description</Label>
            <Textarea
                id="habit-create-description"
                :model-value="modelValue?.description ?? ''"
                placeholder="Optional description..."
                rows="3"
                :disabled="disabled"
                @update:model-value="(v) => update('description', v)"
            />
            <InputError :message="errors?.description" />
        </div>
    </div>
</template>
