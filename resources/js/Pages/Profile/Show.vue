<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});
</script>

<template>
    <AppLayout title="Profile">
        <div class="space-y-6">
            <!-- Header -->
            <div class="overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-indigo-500 to-sky-400 px-6 py-8 text-white sm:px-8">
                    <div class="absolute -right-16 -top-16 size-52 rounded-full bg-white/10 blur-2xl"></div>
                    <div class="absolute -bottom-20 left-10 size-56 rounded-full bg-sky-200/20 blur-2xl"></div>

                    <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-xs font-bold backdrop-blur">
                                <span class="size-2 rounded-full bg-emerald-300"></span>
                                Account settings
                            </div>

                            <h1 class="mt-5 text-3xl font-black tracking-tight sm:text-4xl">
                                Profile
                            </h1>

                            <p class="mt-2 max-w-2xl text-sm leading-6 text-indigo-50">
                                Manage your personal information, password, two-factor authentication and active browser sessions.
                            </p>
                        </div>

                        <div class="flex items-center gap-4 rounded-3xl bg-white/15 p-4 backdrop-blur">
                            <div class="flex size-14 items-center justify-center rounded-2xl bg-white/20 text-lg font-black text-white">
                                {{ $page.props.auth.user.name.charAt(0) }}
                            </div>

                            <div class="min-w-0">
                                <p class="truncate text-sm font-black">
                                    {{ $page.props.auth.user.name }}
                                </p>
                                <p class="truncate text-xs font-semibold text-indigo-100">
                                    {{ $page.props.auth.user.email }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="mx-auto max-w-7xl space-y-6">
                <div
                    v-if="$page.props.jetstream.canUpdateProfileInformation"
                    class="overflow-hidden rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800 sm:p-8"
                >
                    <UpdateProfileInformationForm :user="$page.props.auth.user" />
                </div>

                <div
                    v-if="$page.props.jetstream.canUpdatePassword"
                    class="overflow-hidden rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800 sm:p-8"
                >
                    <UpdatePasswordForm />
                </div>

                <div
                    v-if="$page.props.jetstream.canManageTwoFactorAuthentication"
                    class="overflow-hidden rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800 sm:p-8"
                >
                    <TwoFactorAuthenticationForm
                        :requires-confirmation="confirmsTwoFactorAuthentication"
                    />
                </div>

                <div class="overflow-hidden rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800 sm:p-8">
                    <LogoutOtherBrowserSessionsForm :sessions="sessions" />
                </div>

                <template v-if="$page.props.jetstream.hasAccountDeletionFeatures">
                    <div class="overflow-hidden rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-rose-100 dark:bg-slate-900 dark:ring-rose-900/40 sm:p-8">
                        <DeleteUserForm />
                    </div>
                </template>
            </div>
        </div>
    </AppLayout>
</template>