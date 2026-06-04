<script setup>
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const showSaveModal = ref(false)

const settingsCards = [
    {
        title: 'School Profile',
        description: 'Manage school name, address, contact information and logo.',
        status: 'Updated',
    },
    {
        title: 'Academic Year',
        description: 'Configure current session, term dates and academic calendar.',
        status: 'Active',
    },
    {
        title: 'Fee Settings',
        description: 'Set fee categories, due dates, penalties and payment options.',
        status: 'Configured',
    },
    {
        title: 'Notification Settings',
        description: 'Manage email, SMS and system notification preferences.',
        status: 'Enabled',
    },
]

const stats = [
    { label: 'Active Session', value: '2026-27', change: 'Current' },
    { label: 'Total Users', value: '248', change: '+16' },
    { label: 'Roles', value: '8', change: '+2' },
    { label: 'System Status', value: 'Live', change: 'Stable' },
]

const roles = [
    {
        name: 'Super Admin',
        users: 2,
        permissions: 'Full Access',
        status: 'Active',
    },
    {
        name: 'Admin',
        users: 5,
        permissions: 'Management',
        status: 'Active',
    },
    {
        name: 'Teacher',
        users: 42,
        permissions: 'Academic',
        status: 'Active',
    },
    {
        name: 'Accountant',
        users: 3,
        permissions: 'Finance',
        status: 'Active',
    },
]
</script>

<template>
    <AppLayout title="Settings">
        <div class="space-y-6">
            <!-- Header -->
            <div class="rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 ring-1 ring-slate-200 sm:p-6 lg:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">
                            System Configuration
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                            Settings
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                            Manage school profile, academic session, roles, permissions, notifications and system preferences.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                            Reset Defaults
                        </button>

                        <button
                            type="button"
                            class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800"
                            @click="showSaveModal = true"
                        >
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div
                    v-for="stat in stats"
                    :key="stat.label"
                    class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                >
                    <p class="text-sm font-semibold text-slate-500">
                        {{ stat.label }}
                    </p>

                    <h2 class="mt-3 text-3xl font-black text-slate-950">
                        {{ stat.value }}
                    </h2>

                    <p class="mt-3 text-xs font-bold text-emerald-600">
                        {{ stat.change }}
                    </p>
                </div>
            </div>

            <!-- Settings Cards -->
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <div
                    v-for="card in settingsCards"
                    :key="card.title"
                    class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-lg"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-black text-slate-950">
                                {{ card.title }}
                            </h2>

                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                {{ card.description }}
                            </p>
                        </div>

                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                            {{ card.status }}
                        </span>
                    </div>

                    <button class="mt-6 w-full rounded-2xl bg-indigo-50 px-4 py-2.5 text-sm font-bold text-indigo-700 transition hover:bg-indigo-100">
                        Configure
                    </button>
                </div>
            </div>

            <!-- Main Settings Form -->
            <div class="grid gap-6 xl:grid-cols-3">
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 xl:col-span-2">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">
                            School Information
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Basic dummy school profile settings.
                        </p>
                    </div>

                    <div class="mt-6 grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-slate-600">School Name</label>
                            <input
                                value="SchoolPro Academy"
                                class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                            />
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-600">School Code</label>
                            <input
                                value="SCH-2026"
                                class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                            />
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-600">Email</label>
                            <input
                                type="email"
                                value="info@schoolpro.test"
                                class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                            />
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-600">Phone</label>
                            <input
                                value="+971 50 123 4567"
                                class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                            />
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-600">Academic Session</label>
                            <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                <option>2026 - 2027</option>
                                <option>2025 - 2026</option>
                                <option>2024 - 2025</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-600">Timezone</label>
                            <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                <option>Asia/Dubai</option>
                                <option>Asia/Karachi</option>
                                <option>UTC</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="text-sm font-semibold text-slate-600">Address</label>
                            <textarea
                                rows="3"
                                class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                            >Dubai, United Arab Emirates</textarea>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-gradient-to-br from-indigo-600 to-sky-500 p-6 text-white shadow-lg shadow-indigo-100">
                    <h3 class="text-lg font-black">
                        System Summary
                    </h3>

                    <p class="mt-1 text-sm text-white/75">
                        Current configuration overview
                    </p>

                    <div class="mt-6 space-y-4">
                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Academic Year</p>
                            <p class="mt-1 text-xl font-black">2026 - 2027</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Timezone</p>
                            <p class="mt-1 text-xl font-black">Asia/Dubai</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Notifications</p>
                            <p class="mt-1 text-xl font-black">Enabled</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Roles Table -->
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">
                            Roles & Permissions
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Dummy user roles and access levels.
                        </p>
                    </div>

                    <button class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800">
                        Add Role
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[850px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Role</th>
                                <th class="px-6 py-4">Users</th>
                                <th class="px-6 py-4">Permissions</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="role in roles"
                                :key="role.name"
                                class="transition hover:bg-indigo-50/40"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 to-sky-100 text-sm font-black text-indigo-700">
                                            {{ role.name.charAt(0) }}
                                        </div>

                                        <div>
                                            <p class="font-bold text-slate-950">
                                                {{ role.name }}
                                            </p>
                                            <p class="text-xs text-slate-400">
                                                Access group
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-bold text-slate-900">
                                    {{ role.users }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ role.permissions }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                                        {{ role.status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200">
                                            View
                                        </button>

                                        <button class="rounded-xl bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100">
                                            Edit
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Save Modal -->
            <div
                v-if="showSaveModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
            >
                <div class="w-full max-w-lg overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div class="border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-black text-slate-950">
                                    Save Settings
                                </h2>
                                <p class="mt-1 text-sm text-slate-500">
                                    Confirm saving these dummy configuration changes.
                                </p>
                            </div>

                            <button
                                type="button"
                                class="rounded-2xl bg-white p-2 text-slate-400 shadow-sm ring-1 ring-slate-200 transition hover:text-slate-700"
                                @click="showSaveModal = false"
                            >
                                ✕
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <p class="text-sm leading-6 text-slate-600">
                            These settings are currently frontend-only dummy values. Later, you can connect this form to Laravel controllers, validation and database tables.
                        </p>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 p-5 sm:flex-row sm:justify-end">
                        <button
                            type="button"
                            class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                            @click="showSaveModal = false"
                        >
                            Cancel
                        </button>

                        <button
                            type="button"
                            class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:bg-slate-800"
                            @click="showSaveModal = false"
                        >
                            Confirm Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>