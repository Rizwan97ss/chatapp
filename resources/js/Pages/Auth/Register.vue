<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

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

                    <div class="mt-20">
                        <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-xs font-bold backdrop-blur">
                            <span class="size-2 rounded-full bg-emerald-300"></span>
                            Create your school workspace
                        </div>

                        <h2 class="mt-6 max-w-md text-5xl font-black leading-tight tracking-tight">
                            Start managing your school smarter.
                        </h2>

                        <p class="mt-6 max-w-md text-sm leading-7 text-indigo-50">
                            Register your account and access students, teachers, classes, attendance, exams, fees and reports from one modern dashboard.
                        </p>
                    </div>

                    <div class="mt-14 rounded-[2rem] bg-white/15 p-5 backdrop-blur">
                        <p class="text-sm font-black">Included modules</p>

                        <div class="mt-4 grid grid-cols-2 gap-3 text-sm font-semibold text-indigo-50">
                            <div class="rounded-2xl bg-white/10 px-4 py-3">Students</div>
                            <div class="rounded-2xl bg-white/10 px-4 py-3">Teachers</div>
                            <div class="rounded-2xl bg-white/10 px-4 py-3">Attendance</div>
                            <div class="rounded-2xl bg-white/10 px-4 py-3">Fees</div>
                            <div class="rounded-2xl bg-white/10 px-4 py-3">Exams</div>
                            <div class="rounded-2xl bg-white/10 px-4 py-3">Reports</div>
                        </div>
                    </div>
                </div>

                <!-- Register Form -->
                <div class="p-6 sm:p-10 lg:p-12">
                    <div class="mb-8 lg:hidden">
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
                            Create account
                        </p>

                        <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950">
                            Register for SchoolPro
                        </h2>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Fill in your details to create your school management account.
                        </p>
                    </div>

                    <form class="mt-8 space-y-5" @submit.prevent="submit">
                        <div>
                            <label for="name" class="text-sm font-bold text-slate-700">
                                Full name
                            </label>

                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="Enter your full name"
                                class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 shadow-sm transition focus:border-indigo-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100"
                            />

                            <p v-if="form.errors.name" class="mt-2 text-sm font-medium text-rose-600">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div>
                            <label for="email" class="text-sm font-bold text-slate-700">
                                Email address
                            </label>

                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                                autocomplete="username"
                                placeholder="you@example.com"
                                class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 shadow-sm transition focus:border-indigo-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100"
                            />

                            <p v-if="form.errors.email" class="mt-2 text-sm font-medium text-rose-600">
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="password" class="text-sm font-bold text-slate-700">
                                    Password
                                </label>

                                <input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Password"
                                    class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 shadow-sm transition focus:border-indigo-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100"
                                />

                                <p v-if="form.errors.password" class="mt-2 text-sm font-medium text-rose-600">
                                    {{ form.errors.password }}
                                </p>
                            </div>

                            <div>
                                <label for="password_confirmation" class="text-sm font-bold text-slate-700">
                                    Confirm password
                                </label>

                                <input
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    type="password"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Confirm password"
                                    class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 shadow-sm transition focus:border-indigo-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100"
                                />

                                <p v-if="form.errors.password_confirmation" class="mt-2 text-sm font-medium text-rose-600">
                                    {{ form.errors.password_confirmation }}
                                </p>
                            </div>
                        </div>

                        <div
                            v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature"
                            class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200"
                        >
                            <label class="flex items-start gap-3">
                                <input
                                    id="terms"
                                    v-model="form.terms"
                                    type="checkbox"
                                    name="terms"
                                    required
                                    class="mt-1 size-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                />

                                <span class="text-sm font-semibold leading-6 text-slate-500">
                                    I agree to the
                                    <a
                                        target="_blank"
                                        :href="route('terms.show')"
                                        class="font-bold text-indigo-600 hover:text-indigo-800"
                                    >
                                        Terms of Service
                                    </a>
                                    and
                                    <a
                                        target="_blank"
                                        :href="route('policy.show')"
                                        class="font-bold text-indigo-600 hover:text-indigo-800"
                                    >
                                        Privacy Policy
                                    </a>
                                </span>
                            </label>

                            <p v-if="form.errors.terms" class="mt-2 text-sm font-medium text-rose-600">
                                {{ form.errors.terms }}
                            </p>
                        </div>

                        <button
                            type="submit"
                            class="flex w-full items-center justify-center rounded-2xl bg-slate-950 px-6 py-3.5 text-sm font-bold text-white shadow-xl shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Creating account...</span>
                            <span v-else>Register</span>
                        </button>

                        <p class="text-center text-sm font-semibold text-slate-500">
                            Already registered?

                            <Link
                                :href="route('login')"
                                class="font-bold text-indigo-600 transition hover:text-indigo-800"
                            >
                                Log in
                            </Link>
                        </p>
                    </form>

                    <p class="mt-8 text-center text-xs font-semibold text-slate-400">
                        SchoolPro · Smart School Management System
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>