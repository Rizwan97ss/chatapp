<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Forgot Password" />

    <div class="min-h-screen overflow-hidden bg-slate-50 text-slate-900">
        <!-- Background Effects -->
        <div class="pointer-events-none fixed inset-0">
            <div class="absolute -top-40 left-1/2 h-96 w-96 -translate-x-1/2 rounded-full bg-indigo-200/60 blur-3xl"></div>
            <div class="absolute right-0 top-40 h-96 w-96 rounded-full bg-sky-200/60 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 h-96 w-96 rounded-full bg-violet-200/40 blur-3xl"></div>
        </div>

        <div class="relative flex min-h-screen items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
            <div class="w-full max-w-md rounded-[2rem] bg-white p-6 shadow-2xl ring-1 ring-slate-200 sm:p-8">
                <!-- Logo -->
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
                        🔑
                    </div>

                    <p class="mt-5 text-sm font-bold text-indigo-600">
                        Password recovery
                    </p>

                    <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950">
                        Forgot password?
                    </h2>

                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        Enter your email address and we’ll send you a password reset link to create a new password.
                    </p>
                </div>

                <div
                    v-if="status"
                    class="mt-6 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 ring-1 ring-emerald-100"
                >
                    {{ status }}
                </div>

                <form class="mt-8 space-y-5" @submit.prevent="submit">
                    <div>
                        <label for="email" class="text-sm font-bold text-slate-700">
                            Email address
                        </label>

                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="you@example.com"
                            class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 shadow-sm transition focus:border-indigo-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100"
                        />

                        <p v-if="form.errors.email" class="mt-2 text-sm font-medium text-rose-600">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <button
                        type="submit"
                        class="flex w-full items-center justify-center rounded-2xl bg-slate-950 px-6 py-3.5 text-sm font-bold text-white shadow-xl shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Sending reset link...</span>
                        <span v-else>Email Password Reset Link</span>
                    </button>

                    <p class="text-center text-sm font-semibold text-slate-500">
                        Remember your password?

                        <Link
                            :href="route('login')"
                            class="font-bold text-indigo-600 transition hover:text-indigo-800"
                        >
                            Log in
                        </Link>
                    </p>
                </form>

                <p class="mt-8 text-center text-xs font-semibold text-slate-400">
                    SchoolPro · Account recovery
                </p>
            </div>
        </div>
    </div>
</template>