<script setup>
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import TrainerLayout from '@/Layouts/TrainerLayout.vue';
import Tabs from '@/Components/molecules/Tabs.vue';
import Card from '@/Components/molecules/Card.vue';
import CardContent from '@/Components/molecules/CardContent.vue';
import Label from '@/Components/atoms/Label.vue';
import Input from '@/Components/atoms/Input.vue';
import Button from '@/Components/atoms/Button.vue';
import InputError from '@/Components/InputError.vue';
import { User, CreditCard, Mail } from 'lucide-vue-next';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    subscription: {
        type: Object,
        required: true,
    },
    timezones: {
        type: Array,
        default: () => [],
    },
});

const activeTab = ref('general');

const tabs = [
    { id: 'general', label: 'General', icon: User },
    { id: 'subscription', label: 'Subscription', icon: CreditCard },
    { id: 'email', label: 'Custom Email', icon: Mail },
];

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    timezone: props.user.timezone,
});

const formattedRenewalDate = computed(() => {
    if (!props.subscription.renewal_date) return '—';
    try {
        return new Date(props.subscription.renewal_date + 'T00:00:00').toLocaleDateString(undefined, {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    } catch {
        return props.subscription.renewal_date;
    }
});

const saveGeneral = () => {
    form.patch(route('trainer.settings.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Settings" />

    <TrainerLayout>
        <div class="max-w-4xl mx-auto pb-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Settings</h1>

            <Tabs v-model="activeTab" :tabs="tabs" />

            <div class="mt-6">
                <!-- General -->
                <Card v-if="activeTab === 'general'" class="overflow-hidden">
                    <CardContent class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-1">Profile</h2>
                        <p class="text-sm text-gray-500 mb-6">
                            Manage your name, email, and timezone.
                        </p>
                        <form @submit.prevent="saveGeneral" class="space-y-5 max-w-xl">
                            <div class="space-y-2">
                                <Label for="name" :required="true">Name</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Your name"
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="space-y-2">
                                <Label for="email" :required="true">Email</Label>
                                <Input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    placeholder="you@example.com"
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.email" />
                            </div>
                            <div class="space-y-2">
                                <Label for="timezone" :required="true">Timezone</Label>
                                <select
                                    id="timezone"
                                    v-model="form.timezone"
                                    :disabled="form.processing"
                                    class="flex h-10 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm ring-offset-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <option
                                        v-for="tz in timezones"
                                        :key="tz"
                                        :value="tz"
                                    >
                                        {{ tz }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.timezone" />
                            </div>
                            <div class="flex items-center gap-4">
                                <Button type="submit" :disabled="form.processing">
                                    Save changes
                                </Button>
                                <Transition
                                    enter-active-class="transition ease-in-out"
                                    enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out"
                                    leave-to-class="opacity-0"
                                >
                                    <p
                                        v-if="form.recentlySuccessful"
                                        class="text-sm text-gray-600"
                                    >
                                        Saved.
                                    </p>
                                </Transition>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                <!-- Subscription -->
                <Card v-if="activeTab === 'subscription'" class="overflow-hidden">
                    <CardContent class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-1">Subscription</h2>
                        <p class="text-sm text-gray-500 mb-6">
                            Your current plan and billing status.
                        </p>
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 max-w-xl">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Plan</dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900">
                                    {{ subscription.plan_name }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900 capitalize">
                                    {{ subscription.status }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Renewal date</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ formattedRenewalDate }}
                                </dd>
                            </div>
                        </dl>
                    </CardContent>
                </Card>

                <!-- Custom Email (placeholder) -->
                <Card v-if="activeTab === 'email'" class="overflow-hidden">
                    <CardContent class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-1">Custom Email</h2>
                        <p class="text-sm text-gray-500 mb-6">
                            Configure SMTP and email templates for client communications.
                        </p>
                        <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50/50 px-6 py-10 text-center text-sm text-gray-500">
                            Custom email setup will be available here soon. You’ll be able to add your own SMTP settings and manage email templates.
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </TrainerLayout>
</template>
