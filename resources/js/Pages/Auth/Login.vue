<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const router = useRouter();

const form = ref({
    email: '',
    password: '',
    remember: false,
});

const errors = ref<Record<string, string>>({});
const processing = ref(false);
const status = ref('');
const canResetPassword = ref(true); // Optional: ambil dari settings config nanti

import { authState, fetchAuthUser } from '@/store/auth';

const submit = async () => {
    processing.value = true;
    errors.value = {};
    
    try {
        await axios.get('/sanctum/csrf-cookie');
        await axios.post('/login', form.value);
        await fetchAuthUser(true);
        router.push('/dashboard');
    } catch (error: any) {
        if (error.response?.data?.errors) {
            for (const key in error.response.data.errors) {
                errors.value[key] = error.response.data.errors[key][0];
            }
        }
    } finally {
        processing.value = false;
        form.value.password = '';
    }
};
</script>

<template>
    <GuestLayout>
        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600"
                        >Remember me</span
                    >
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <router-link
                    v-if="canResetPassword"
                    to="/forgot-password"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Forgot your password?
                </router-link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': processing }"
                    :disabled="processing"
                >
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
