<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionSection from '@/Components/ActionSection.vue';
import DialogModal from '@/Components/DialogModal.vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    setTimeout(() => passwordInput.value?.focus(), 250);
};

const deleteUser = () => {
    form.delete(route('current-user.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <ActionSection>
        <template #title>
            <span class="text-slate-950 dark:text-white">
                Delete Account
            </span>
        </template>

        <template #description>
            <span class="text-slate-500 dark:text-slate-400">
                Permanently delete your account and all related data.
            </span>
        </template>

        <template #content>
            <div class="rounded-3xl bg-rose-50 p-5 ring-1 ring-rose-100 dark:bg-rose-950/20 dark:ring-rose-900/40">
                <div class="flex gap-4">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-rose-100 text-xl dark:bg-rose-900/40">
                        ⚠️
                    </div>

                    <div>
                        <h3 class="text-sm font-black text-rose-700 dark:text-rose-300">
                            This action cannot be undone
                        </h3>

                        <p class="mt-2 max-w-xl text-sm leading-6 text-rose-600/80 dark:text-rose-200/80">
                            Once your account is deleted, all of its resources and data will be permanently deleted.
                            Please download any data or information you wish to retain before continuing.
                        </p>

                        <button
                            type="button"
                            class="mt-5 rounded-2xl bg-rose-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-rose-100 transition hover:-translate-y-0.5 hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-50 dark:shadow-none"
                            @click="confirmUserDeletion"
                        >
                            Delete Account
                        </button>
                    </div>
                </div>
            </div>

            <DialogModal :show="confirmingUserDeletion" @close="closeModal">
                <template #title>
                    <span class="text-xl font-black text-slate-950 dark:text-white">
                        Delete Account
                    </span>
                </template>

                <template #content>
                    <div class="space-y-5">
                        <div class="rounded-3xl bg-rose-50 p-4 ring-1 ring-rose-100 dark:bg-rose-950/20 dark:ring-rose-900/40">
                            <p class="text-sm font-semibold leading-6 text-rose-700 dark:text-rose-200">
                                Are you sure you want to delete your account? Once deleted, all resources and data
                                will be permanently removed. Enter your password to confirm.
                            </p>
                        </div>

                        <div>
                            <label for="delete-password" class="text-sm font-bold text-slate-700 dark:text-slate-300">
                                Password
                            </label>

                            <input
                                id="delete-password"
                                ref="passwordInput"
                                v-model="form.password"
                                type="password"
                                class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 shadow-sm transition focus:border-rose-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-rose-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                @keyup.enter="deleteUser"
                            />

                            <p v-if="form.errors.password" class="mt-2 text-sm font-medium text-rose-600">
                                {{ form.errors.password }}
                            </p>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <div class="flex w-full flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <button
                            type="button"
                            class="rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-950 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                            @click="closeModal"
                        >
                            Cancel
                        </button>

                        <button
                            type="button"
                            class="rounded-2xl bg-rose-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-rose-100 transition hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-50 dark:shadow-none"
                            :disabled="form.processing"
                            @click="deleteUser"
                        >
                            <span v-if="form.processing">Deleting...</span>
                            <span v-else>Delete Account</span>
                        </button>
                    </div>
                </template>
            </DialogModal>
        </template>
    </ActionSection>
</template>