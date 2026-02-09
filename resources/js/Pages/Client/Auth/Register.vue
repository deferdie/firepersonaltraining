<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    invitation: {
        type: Object,
        default: null,
    },
    invitationLinkUsed: {
        type: Boolean,
        default: false,
    },
});

const page = usePage();
const flashError = page.props.flash?.error;

const initialData = props.invitation
    ? {
          password: '',
          password_confirmation: '',
          client_id: props.invitation.client_id,
          expires: props.invitation.expires,
          signature: props.invitation.signature,
      }
    : {
          name: '',
          email: '',
          password: '',
          password_confirmation: '',
      };

const form = useForm(initialData);

const submit = () => {
    form.post(route('client.register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="invitation ? 'Complete your setup' : 'Client Register'" />

        <!-- Link already used -->
        <div
            v-if="invitationLinkUsed || flashError"
            class="space-y-4"
        >
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ flashError || 'This signup link has already been used. If you have an account, please log in.' }}
            </p>
            <Link
                :href="route('client.login')"
                class="inline-flex items-center rounded-md border border-transparent bg-gray-900 px-4 py-2 text-sm font-medium text-white shadow hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:bg-gray-700 dark:hover:bg-gray-600"
            >
                Log in
            </Link>
        </div>

        <!-- Invitation: confirm email + password only -->
        <form
            v-else-if="invitation"
            @submit.prevent="submit"
            class="space-y-4"
        >
            <input type="hidden" name="client_id" :value="form.client_id" />
            <input type="hidden" name="expires" :value="form.expires" />
            <input type="hidden" name="signature" :value="form.signature" />

            <div>
                <InputLabel value="Confirm your email" />
                <p
                    id="invitation-email"
                    class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                >
                    {{ invitation.email }}
                </p>
            </div>

            <div>
                <InputLabel for="password" value="Create a password" required />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autofocus
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                    required
                />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Complete setup
                </PrimaryButton>
            </div>
        </form>

        <!-- Standard registration (no invitation) -->
        <form
            v-else
            @submit.prevent="submit"
        >
            <div>
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('client.login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                >
                    Already registered?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Client Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
