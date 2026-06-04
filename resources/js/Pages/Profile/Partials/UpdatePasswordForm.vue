<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('user-password.update'), {
        errorBag: 'updatePassword',
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }

            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};
</script>

<template>
    <FormSection @submitted="updatePassword">
        <template #title>
            <span class="text-slate-950 dark:text-white">
                Update Password
            </span>
        </template>

        <template #description>
            <span class="text-slate-500 dark:text-slate-400">
                Use a strong password to keep your SchoolPro account secure.
            </span>
        </template>

        <template #form>
            <div class="col-span-6">
                <div class="rounded-3xl bg-indigo-50 p-5 ring-1 ring-indigo-100 dark:bg-indigo-950/20 dark:ring-indigo-900/40">
                    <div class="flex gap-4">
                        <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-white text-xl dark:bg-indigo-900/40">
                            🔐
                        </div>

                        <p class="text-sm font-semibold leading-6 text-indigo-700 dark:text-indigo-200">
                            For better security, choose a long password that you do not use on other websites.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <label for="current_password" class="text-sm font-bold text-slate-700 dark:text-slate-300">
                    Current Password
                </label>

                <input
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    autocomplete="current-password"
                    class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 shadow-sm transition focus:border-indigo-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                    placeholder="Enter current password"
                />

                <p v-if="form.errors.current_password" class="mt-2 text-sm font-medium text-rose-600">
                    {{ form.errors.current_password }}
                </p>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <label for="password" class="text-sm font-bold text-slate-700 dark:text-slate-300">
                    New Password
                </label>

                <input
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    autocomplete="new-password"
                    class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 shadow-sm transition focus:border-indigo-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                    placeholder="Enter new password"
                />

                <p v-if="form.errors.password" class="mt-2 text-sm font-medium text-rose-600">
                    {{ form.errors.password }}
                </p>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <label for="password_confirmation" class="text-sm font-bold text-slate-700 dark:text-slate-300">
                    Confirm Password
                </label>

                <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 shadow-sm transition focus:border-indigo-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                    placeholder="Confirm new password"
                />

                <p v-if="form.errors.password_confirmation" class="mt-2 text-sm font-medium text-rose-600">
                    {{ form.errors.password_confirmation }}
                </p>
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3 text-sm font-bold text-emerald-600">
                Saved.
            </ActionMessage>

            <button
                type="submit"
                class="rounded-2xl bg-slate-950 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-white dark:text-slate-950 dark:shadow-none dark:hover:bg-slate-200"
                :disabled="form.processing"
            >
                <span v-if="form.processing">Saving...</span>
                <span v-else>Save</span>
            </button>
        </template>
    </FormSection>
</template>