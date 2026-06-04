<script setup>
import { nextTick, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const recovery = ref(false);

const form = useForm({
    code: '',
    recovery_code: '',
});

const recoveryCodeInput = ref(null);
const codeInput = ref(null);

const toggleRecovery = async () => {
    recovery.value = !recovery.value;

    await nextTick();

    if (recovery.value) {
        recoveryCodeInput.value?.focus();
        form.code = '';
    } else {
        codeInput.value?.focus();
        form.recovery_code = '';
    }
};

const submit = () => {
    form.post(route('two-factor.login'));
};
</script>

<template>
    <Head title="Two-factor Confirmation" />

    <div class="min-h-screen overflow-hidden bg-slate-50 text-slate-900">
        <div class="pointer-events-none fixed inset-0">
            <div class="absolute -top-40 left-1/2 h-96 w-96 -translate-x-1/2 rounded-full bg-indigo-200/60 blur-3xl"></div>
            <div class="absolute right-0 top-40 h-96 w-96 rounded-full bg-sky-200/60 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 h-96 w-96 rounded-full bg-violet-200/40 blur-3xl"></div>
        </div>

        <div class="relative flex min-h-screen items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
            <div class="w-full max-w-md rounded-[2rem] bg-white p-6 shadow-2xl ring-1 ring-slate-200 sm:p-8">
                <Link href="/" class="flex items-center justify-center gap-3">
                    <div class="flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-sky-400 text-sm font-black text-white shadow-lg shadow-indigo-200">
                        S
                    </div>

                    <div>
                        <h1 class="text-xl font-black tracking-tight text-slate-950">
                            SchoolPro
                        </h1>
                        <p class="-mt-1 text-xs font-semibold text-slate-400">
                            Management System
                        </p>
                    </div>
                </Link>

                <div class="mt-8 text-center">
                    <div class="mx-auto flex size-14 items-center justify-center rounded-3xl bg-indigo-50 text-2xl">
                        🛡️
                    </div>

                    <p class="mt-5 text-sm font-bold text-indigo-600">
                        Two-factor security
                    </p>

                    <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950">
                        Verify your login
                    </h2>

                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        <template v-if="!recovery">
                            Enter the authentication code from your authenticator app to continue.
                        </template>

                        <template v-else>
                            Enter one of your emergency recovery codes to access your account.
                        </template>
                    </p>
                </div>

                <form class="mt-8 space-y-5" @submit.prevent="submit">
                    <div v-if="!recovery">
                        <label for="code" class="text-sm font-bold text-slate-700">
                            Authentication code
                        </label>

                        <input
                            id="code"
                            ref="codeInput"
                            v-model="form.code"
                            type="text"
                            inputmode="numeric"
                            autofocus
                            autocomplete="one-time-code"
                            placeholder="Enter 6-digit code"
                            class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-center text-lg font-black tracking-[0.35em] text-slate-700 placeholder:text-sm placeholder:font-semibold placeholder:tracking-normal placeholder:text-slate-400 shadow-sm transition focus:border-indigo-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100"
                        />

                        <p v-if="form.errors.code" class="mt-2 text-sm font-medium text-rose-600">
                            {{ form.errors.code }}
                        </p>
                    </div>

                    <div v-else>
                        <label for="recovery_code" class="text-sm font-bold text-slate-700">
                            Recovery code
                        </label>

                        <input
                            id="recovery_code"
                            ref="recoveryCodeInput"
                            v-model="form.recovery_code"
                            type="text"
                            autocomplete="one-time-code"
                            placeholder="Enter recovery code"
                            class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 placeholder:text-slate-400 shadow-sm transition focus:border-indigo-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100"
                        />

                        <p v-if="form.errors.recovery_code" class="mt-2 text-sm font-medium text-rose-600">
                            {{ form.errors.recovery_code }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200">
                        <p class="text-xs font-semibold leading-5 text-slate-500">
                            <template v-if="!recovery">
                                Open your authenticator app and copy the latest one-time code before it expires.
                            </template>

                            <template v-else>
                                Recovery codes can usually be used only once. Keep your remaining codes safe.
                            </template>
                        </p>
                    </div>

                    <button
                        type="submit"
                        class="flex w-full items-center justify-center rounded-2xl bg-slate-950 px-6 py-3.5 text-sm font-bold text-white shadow-xl shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Verifying...</span>
                        <span v-else>Log in</span>
                    </button>

                    <button
                        type="button"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-950"
                        @click.prevent="toggleRecovery"
                    >
                        <template v-if="!recovery">
                            Use a recovery code
                        </template>

                        <template v-else>
                            Use an authentication code
                        </template>
                    </button>
                </form>

                <p class="mt-8 text-center text-xs font-semibold text-slate-400">
                    SchoolPro · Secure two-factor login
                </p>
            </div>
        </div>
    </div>
</template>