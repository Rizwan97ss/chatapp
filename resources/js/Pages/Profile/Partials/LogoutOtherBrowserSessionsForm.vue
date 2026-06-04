<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import ActionSection from '@/Components/ActionSection.vue';
import DialogModal from '@/Components/DialogModal.vue';

defineProps({
    sessions: Array,
});

const confirmingLogout = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmLogout = () => {
    confirmingLogout.value = true;

    setTimeout(() => passwordInput.value?.focus(), 250);
};

const logoutOtherBrowserSessions = () => {
    form.delete(route('other-browser-sessions.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingLogout.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <ActionSection>
        <template #title>
            <span class="text-slate-950 dark:text-white">
                Browser Sessions
            </span>
        </template>

        <template #description>
            <span class="text-slate-500 dark:text-slate-400">
                Manage and log out active sessions on other browsers and devices.
            </span>
        </template>

        <template #content>
            <div class="max-w-2xl rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-200 dark:bg-slate-800/60 dark:ring-slate-700">
                <div class="flex gap-4">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-xl dark:bg-indigo-950/40">
                        🖥️
                    </div>

                    <p class="text-sm leading-6 text-slate-500 dark:text-slate-400">
                        If necessary, you may log out of all other browser sessions across your devices.
                        If you feel your account has been compromised, update your password as well.
                    </p>
                </div>
            </div>

            <!-- Other Browser Sessions -->
            <div v-if="sessions.length > 0" class="mt-6 grid gap-4">
                <div
                    v-for="(session, i) in sessions"
                    :key="i"
                    class="flex items-center gap-4 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:hover:bg-slate-800"
                >
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-300">
                        <svg
                            v-if="session.agent.is_desktop"
                            class="size-6"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.8"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"
                            />
                        </svg>

                        <svg
                            v-else
                            class="size-6"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.8"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"
                            />
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-black text-slate-800 dark:text-slate-100">
                            {{ session.agent.platform ? session.agent.platform : 'Unknown' }}
                            -
                            {{ session.agent.browser ? session.agent.browser : 'Unknown' }}
                        </p>

                        <p class="mt-1 truncate text-xs font-semibold text-slate-500">
                            {{ session.ip_address }}
                        </p>
                    </div>

                    <div class="shrink-0">
                        <span
                            v-if="session.is_current_device"
                            class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-600 ring-1 ring-emerald-100 dark:bg-emerald-950/30 dark:text-emerald-300 dark:ring-emerald-900/40"
                        >
                            This device
                        </span>

                        <span
                            v-else
                            class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500 dark:bg-slate-800 dark:text-slate-300"
                        >
                            Last active {{ session.last_active }}
                        </span>
                    </div>
                </div>
            </div>

            <div v-else class="mt-6 rounded-3xl border border-dashed border-slate-300 bg-white p-6 text-center dark:border-slate-700 dark:bg-slate-900">
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">
                    No other browser sessions found.
                </p>
            </div>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
                <button
                    type="button"
                    class="rounded-2xl bg-slate-950 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-white dark:text-slate-950 dark:shadow-none dark:hover:bg-slate-200"
                    @click="confirmLogout"
                >
                    Log Out Other Browser Sessions
                </button>

                <ActionMessage :on="form.recentlySuccessful" class="text-sm font-bold text-emerald-600">
                    Done.
                </ActionMessage>
            </div>

            <DialogModal :show="confirmingLogout" @close="closeModal">
                <template #title>
                    <span class="text-xl font-black text-slate-950 dark:text-white">
                        Log Out Other Browser Sessions
                    </span>
                </template>

                <template #content>
                    <div class="space-y-5">
                        <div class="rounded-3xl bg-indigo-50 p-4 ring-1 ring-indigo-100 dark:bg-indigo-950/20 dark:ring-indigo-900/40">
                            <p class="text-sm font-semibold leading-6 text-indigo-700 dark:text-indigo-200">
                                Please enter your password to confirm that you want to log out of your other browser sessions across all devices.
                            </p>
                        </div>

                        <div>
                            <label for="session-password" class="text-sm font-bold text-slate-700 dark:text-slate-300">
                                Password
                            </label>

                            <input
                                id="session-password"
                                ref="passwordInput"
                                v-model="form.password"
                                type="password"
                                class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 shadow-sm transition focus:border-indigo-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                @keyup.enter="logoutOtherBrowserSessions"
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
                            class="rounded-2xl bg-slate-950 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-white dark:text-slate-950 dark:shadow-none dark:hover:bg-slate-200"
                            :disabled="form.processing"
                            @click="logoutOtherBrowserSessions"
                        >
                            <span v-if="form.processing">Logging out...</span>
                            <span v-else>Log Out Other Sessions</span>
                        </button>
                    </div>
                </template>
            </DialogModal>
        </template>
    </ActionSection>
</template>