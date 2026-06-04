<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <div class="min-h-screen overflow-hidden bg-slate-50 text-slate-900">
        <!-- Background Effects -->
        <div class="pointer-events-none fixed inset-0">
            <div class="absolute -top-40 left-1/2 h-96 w-96 -translate-x-1/2 rounded-full bg-indigo-200/60 blur-3xl"></div>
            <div class="absolute right-0 top-40 h-96 w-96 rounded-full bg-sky-200/60 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 h-96 w-96 rounded-full bg-violet-200/40 blur-3xl"></div>
        </div>

        <div class="relative flex min-h-screen items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
            <div class="grid w-full max-w-6xl overflow-hidden rounded-[2rem] bg-white shadow-2xl ring-1 ring-slate-200 lg:grid-cols-2">
                <!-- Left Side -->
                <div class="hidden bg-gradient-to-br from-indigo-600 via-indigo-500 to-sky-400 p-10 text-white lg:block">
                    <Link href="/" class="flex items-center gap-3">
                        <div class="flex size-12 items-center justify-center rounded-2xl bg-white/20 text-sm font-black text-white shadow-lg backdrop-blur">
                            S
                        </div>

                        <div>
                            <h1 class="text-xl font-black tracking-tight">SchoolPro</h1>
                            <p class="-mt-1 text-xs font-semibold text-indigo-100">
                                Management System
                            </p>
                        </div>
                    </Link>

                    <div class="mt-24">
                        <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-xs font-bold backdrop-blur">
                            <span class="size-2 rounded-full bg-emerald-300"></span>
                            Laravel + Vue + Tailwind
                        </div>

                        <h2 class="mt-6 max-w-md text-5xl font-black leading-tight tracking-tight">
                            Welcome back to your school dashboard.
                        </h2>

                        <p class="mt-6 max-w-md text-sm leading-7 text-indigo-50">
                            Manage students, teachers, attendance, exams, fees and reports from one clean modern dashboard.
                        </p>
                    </div>

                    <div class="mt-16 grid grid-cols-3 gap-4">
                        <div class="rounded-3xl bg-white/15 p-4 backdrop-blur">
                            <p class="text-2xl font-black">1.2k+</p>
                            <p class="mt-1 text-xs font-semibold text-indigo-100">Students</p>
                        </div>

                        <div class="rounded-3xl bg-white/15 p-4 backdrop-blur">
                            <p class="text-2xl font-black">82</p>
                            <p class="mt-1 text-xs font-semibold text-indigo-100">Teachers</p>
                        </div>

                        <div class="rounded-3xl bg-white/15 p-4 backdrop-blur">
                            <p class="text-2xl font-black">94%</p>
                            <p class="mt-1 text-xs font-semibold text-indigo-100">Attendance</p>
                        </div>
                    </div>
                </div>

                <!-- Login Form -->
                <div class="p-6 sm:p-10 lg:p-12">
                    <div class="mb-10 lg:hidden">
                        <Link href="/" class="flex items-center gap-3">
                            <div class="flex size-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-sky-400 text-sm font-black text-white shadow-lg shadow-indigo-200">
                                S
                            </div>

                            <div>
                                <h1 class="text-lg font-black tracking-tight text-slate-950">
                                    SchoolPro
                                </h1>
                                <p class="-mt-1 text-xs font-semibold text-slate-400">
                                    Management System
                                </p>
                            </div>
                        </Link>
                    </div>

                    <div>
                        <p class="text-sm font-bold text-indigo-600">
                            Sign in
                        </p>

                        <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950">
                            Login to your account
                        </h2>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Enter your email and password to access the SchoolPro dashboard.
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

                        <div>
                            <label for="password" class="text-sm font-bold text-slate-700">
                                Password
                            </label>

                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                required
                                autocomplete="current-password"
                                placeholder="Enter your password"
                                class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 shadow-sm transition focus:border-indigo-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100"
                            />

                            <p v-if="form.errors.password" class="mt-2 text-sm font-medium text-rose-600">
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <label class="flex items-center gap-2">
                                <input
                                    v-model="form.remember"
                                    type="checkbox"
                                    name="remember"
                                    class="size-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                />

                                <span class="text-sm font-semibold text-slate-500">
                                    Remember me
                                </span>
                            </label>

                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-sm font-bold text-indigo-600 transition hover:text-indigo-800"
                            >
                                Forgot password?
                            </Link>
                        </div>

                        <button
                            type="submit"
                            class="flex w-full items-center justify-center rounded-2xl bg-slate-950 px-6 py-3.5 text-sm font-bold text-white shadow-xl shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Signing in...</span>
                            <span v-else>Log in</span>
                        </button>
                    </form>

                    <p class="mt-8 text-center text-xs font-semibold text-slate-400">
                        SchoolPro · Smart School Management System
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>