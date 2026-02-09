<script setup>
import { useForm } from '@inertiajs/vue3';
import DialogModal from '@/Components/molecules/DialogModal.vue';
import Label from '@/Components/atoms/Label.vue';
import Input from '@/Components/atoms/Input.vue';
import Textarea from '@/Components/atoms/Textarea.vue';
import Button from '@/Components/atoms/Button.vue';
import Select from '@/Components/molecules/Select.vue';
import SelectTrigger from '@/Components/molecules/SelectTrigger.vue';
import SelectContent from '@/Components/molecules/SelectContent.vue';
import SelectItem from '@/Components/molecules/SelectItem.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    programs: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    program_id: null,
    status: 'trial',
    goals: '',
    notes: '',
});

const handleSubmit = () => {
    form.post(route('trainer.clients.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emit('close');
        },
    });
};

const handleClose = () => {
    if (!form.processing) {
        form.reset();
        form.clearErrors();
        emit('close');
    }
};
</script>

<template>
    <DialogModal
        :is-open="isOpen"
        title="Add New Client"
        description="Enter your client's information to get started"
        max-width="2xl"
        @close="handleClose"
    >
        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Personal Information -->
            <div class="space-y-4">
                <h3 class="font-semibold text-sm text-gray-700">Personal Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="firstName" :required="true">First Name</Label>
                        <Input
                            id="firstName"
                            v-model="form.firstName"
                            placeholder="Enter first name"
                            :disabled="form.processing"
                        />
                        <InputError :message="form.errors.firstName" />
                    </div>

                    <div class="space-y-2">
                        <Label for="lastName" :required="true">Last Name</Label>
                        <Input
                            id="lastName"
                            v-model="form.lastName"
                            placeholder="Enter last name"
                            :disabled="form.processing"
                        />
                        <InputError :message="form.errors.lastName" />
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="email" :required="true">Email</Label>
                    <Input
                        id="email"
                        v-model="form.email"
                        type="email"
                        placeholder="client@email.com"
                        :disabled="form.processing"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="space-y-2">
                    <Label for="phone">Phone Number</Label>
                    <Input
                        id="phone"
                        v-model="form.phone"
                        type="tel"
                        placeholder="(555) 123-4567"
                        :disabled="form.processing"
                    />
                    <InputError :message="form.errors.phone" />
                </div>
            </div>

            <!-- Program & Status -->
            <div class="space-y-4">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                 
                    <div class="space-y-2">
                        <Label for="status">Status</Label>
                        <Select v-model="form.status" :disabled="form.processing">
                            <SelectTrigger id="status" />
                            <SelectContent>
                                <SelectItem value="trial" label="Trial">Trial</SelectItem>
                                <SelectItem value="active" label="Active">Active</SelectItem>
                                <SelectItem value="inactive" label="Inactive">Inactive</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.status" />
                    </div>
                </div>
            </div>


            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <Button
                    type="button"
                    variant="outline"
                    :disabled="form.processing"
                    @click="handleClose"
                >
                    Cancel
                </Button>
                <Button
                    type="submit"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Adding...' : 'Add Client' }}
                </Button>
            </div>
        </form>
    </DialogModal>
</template>
