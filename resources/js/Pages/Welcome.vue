<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="School Management System" />

    <div class="min-h-screen overflow-hidden bg-slate-50 text-slate-900">
        <div class="pointer-events-none fixed inset-0">
            <div class="absolute -top-40 left-1/2 h-96 w-96 -translate-x-1/2 rounded-full bg-indigo-200/60 blur-3xl"></div>
            <div class="absolute right-0 top-40 h-96 w-96 rounded-full bg-sky-200/60 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 h-96 w-96 rounded-full bg-violet-200/40 blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <header class="flex items-center justify-between py-6">
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

                <nav v-if="canLogin" class="flex items-center gap-2">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-2xl bg-slate-950 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800"
                    >
                        Dashboard
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-2xl px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-white hover:text-slate-950"
                        >
                            Log in
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-2xl bg-slate-950 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800"
                        >
                            Register
                        </Link>
                    </template>
                </nav>
            </header>

            <main class="pb-16 pt-10 sm:pt-16 lg:pb-24">
                <section class="grid items-center gap-12 lg:grid-cols-2">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-xs font-bold text-indigo-700 shadow-sm ring-1 ring-slate-200">
                            <span class="size-2 rounded-full bg-emerald-500"></span>
                            Laravel + Vue + Tailwind dashboard
                        </div>

                        <h2 class="mt-6 max-w-3xl text-4xl font-black tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">
                            Smart school management made simple.
                        </h2>

                        <p class="mt-6 max-w-2xl text-base leading-8 text-slate-500 sm:text-lg">
                            Manage students, teachers, classes, subjects, attendance, exams and fees from one modern responsive dashboard.
                        </p>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <Link
                                v-if="$page.props.auth.user"
                                :href="route('dashboard')"
                                class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-6 py-4 text-sm font-bold text-white shadow-xl shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800"
                            >
                                Go to Dashboard
                            </Link>

                            <template v-else>
                                <Link
                                    :href="route('login')"
                                    class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-6 py-4 text-sm font-bold text-white shadow-xl shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800"
                                >
                                    Login to Dashboard
                                </Link>

                                <Link
                                    v-if="canRegister"
                                    :href="route('register')"
                                    class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-4 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50"
                                >
                                    Create Account
                                </Link>
                            </template>
                        </div>

                        <div class="mt-10 grid max-w-xl grid-cols-3 gap-4">
                            <div class="rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                                <p class="text-2xl font-black text-slate-950">1.2k+</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">Students</p>
                            </div>

                            <div class="rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                                <p class="text-2xl font-black text-slate-950">82</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">Teachers</p>
                            </div>

                            <div class="rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                                <p class="text-2xl font-black text-slate-950">94%</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">Attendance</p>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="absolute -inset-4 rounded-[2rem] bg-gradient-to-br from-indigo-200 to-sky-200 blur-2xl"></div>

                        <div class="relative overflow-hidden rounded-[2rem] bg-white p-4 shadow-2xl ring-1 ring-slate-200">
                            <div class="rounded-[1.5rem] bg-slate-100 p-4">
                                <div class="flex items-center justify-between rounded-2xl bg-white p-4 shadow-sm">
                                    <div>
                                        <p class="text-xs font-bold text-indigo-600">Dashboard</p>
                                        <p class="mt-1 text-lg font-black text-slate-950">School Overview</p>
                                    </div>

                                    <div class="flex size-10 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-sky-400 text-sm font-black text-white">
                                        A
                                    </div>
                                </div>

                                <div class="mt-4 grid grid-cols-2 gap-3">
                                    <div class="rounded-2xl bg-white p-4 shadow-sm">
                                        <p class="text-xs font-semibold text-slate-500">Students</p>
                                        <p class="mt-2 text-2xl font-black text-slate-950">1,248</p>
                                    </div>

                                    <div class="rounded-2xl bg-white p-4 shadow-sm">
                                        <p class="text-xs font-semibold text-slate-500">Fees</p>
                                        <p class="mt-2 text-2xl font-black text-slate-950">AED 284k</p>
                                    </div>
                                </div>

                                <div class="mt-4 rounded-2xl bg-white p-4 shadow-sm">
                                    <div class="mb-4 flex items-center justify-between">
                                        <p class="text-sm font-black text-slate-950">Attendance</p>
                                        <p class="text-xs font-bold text-emerald-600">94%</p>
                                    </div>

                                    <div class="grid h-36 grid-cols-7 items-end gap-2">
                                        <div class="h-[55%] rounded-t-xl bg-indigo-300"></div>
                                        <div class="h-[70%] rounded-t-xl bg-indigo-400"></div>
                                        <div class="h-[64%] rounded-t-xl bg-indigo-300"></div>
                                        <div class="h-[90%] rounded-t-xl bg-indigo-500"></div>
                                        <div class="h-[78%] rounded-t-xl bg-indigo-400"></div>
                                        <div class="h-[66%] rounded-t-xl bg-indigo-300"></div>
                                        <div class="h-[95%] rounded-t-xl bg-indigo-600"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mt-20">
                    <p class="text-sm font-bold text-indigo-600">Core Modules</p>
                    <h3 class="mt-2 text-3xl font-black tracking-tight text-slate-950">
                        Everything your school needs.
                    </h3>

                    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                            <div class="flex size-12 items-center justify-center rounded-2xl bg-indigo-50 text-xl">🎓</div>
                            <h4 class="mt-5 text-lg font-black text-slate-950">Students</h4>
                            <p class="mt-2 text-sm leading-6 text-slate-500">Admissions, profiles, parent details and class assignment.</p>
                        </div>

                        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                            <div class="flex size-12 items-center justify-center rounded-2xl bg-sky-50 text-xl">👩‍🏫</div>
                            <h4 class="mt-5 text-lg font-black text-slate-950">Teachers</h4>
                            <p class="mt-2 text-sm leading-6 text-slate-500">Teacher records, subjects and staff management.</p>
                        </div>

                        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                            <div class="flex size-12 items-center justify-center rounded-2xl bg-emerald-50 text-xl">✅</div>
                            <h4 class="mt-5 text-lg font-black text-slate-950">Attendance</h4>
                            <p class="mt-2 text-sm leading-6 text-slate-500">Daily attendance, absences and class summaries.</p>
                        </div>

                        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                            <div class="flex size-12 items-center justify-center rounded-2xl bg-violet-50 text-xl">📚</div>
                            <h4 class="mt-5 text-lg font-black text-slate-950">Subjects</h4>
                            <p class="mt-2 text-sm leading-6 text-slate-500">Curriculum, subject codes and assigned teachers.</p>
                        </div>

                        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                            <div class="flex size-12 items-center justify-center rounded-2xl bg-amber-50 text-xl">📝</div>
                            <h4 class="mt-5 text-lg font-black text-slate-950">Exams</h4>
                            <p class="mt-2 text-sm leading-6 text-slate-500">Exam schedules, marks, results and performance.</p>
                        </div>

                        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                            <div class="flex size-12 items-center justify-center rounded-2xl bg-rose-50 text-xl">💳</div>
                            <h4 class="mt-5 text-lg font-black text-slate-950">Fees</h4>
                            <p class="mt-2 text-sm leading-6 text-slate-500">Invoices, paid fees, pending dues and summaries.</p>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="border-t border-slate-200 py-8 text-center text-xs font-semibold text-slate-400">
                SchoolPro · Laravel v{{ laravelVersion }} · PHP v{{ phpVersion }}
            </footer>
        </div>
    </div>
</template>