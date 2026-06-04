<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <Head title="Email Verification" />

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
                        ✉️
                    </div>

                    <p class="mt-5 text-sm font-bold text-indigo-600">
                        Verify email
                    </p>

                    <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950">
                        Check your inbox
                    </h2>

                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        Before continuing, please verify your email address by clicking the link we emailed to you.
                        If you did not receive it, you can request another verification email.
                    </p>
                </div>

                <div
                    v-if="verificationLinkSent"
                    class="mt-6 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-semibold leading-6 text-emerald-700 ring-1 ring-emerald-100"
                >
                    A new verification link has been sent to the email address you provided in your profile settings.
                </div>

                <form class="mt-8 space-y-4" @submit.prevent="submit">
                    <button
                        type="submit"
                        class="flex w-full items-center justify-center rounded-2xl bg-slate-950 px-6 py-3.5 text-sm font-bold text-white shadow-xl shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Sending verification email...</span>
                        <span v-else>Resend Verification Email</span>
                    </button>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <Link
                            :href="route('profile.show')"
                            class="flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-950"
                        >
                            Edit Profile
                        </Link>

                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-950"
                        >
                            Log Out
                        </Link>
                    </div>
                </form>

                <p class="mt-8 text-center text-xs font-semibold text-slate-400">
                    SchoolPro · Email verification required
                </p>
            </div>
        </div>
    </div>
</template>