<script setup>
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const showCreateModal = ref(false)

const stats = [
    { label: 'Total Reports', value: '64', change: '+12' },
    { label: 'Academic Reports', value: '21', change: '+6' },
    { label: 'Finance Reports', value: '18', change: '+4' },
    { label: 'Pending Reports', value: '7', change: '-2' },
]

const reports = [
    {
        id: 'RPT-1001',
        title: 'Monthly Attendance Report',
        type: 'Attendance',
        period: 'April 2026',
        generatedBy: 'Admin Office',
        date: '2026-04-25',
        format: 'PDF',
        status: 'Ready',
    },
    {
        id: 'RPT-1002',
        title: 'Fee Collection Summary',
        type: 'Finance',
        period: 'April 2026',
        generatedBy: 'Accounts',
        date: '2026-04-24',
        format: 'Excel',
        status: 'Ready',
    },
    {
        id: 'RPT-1003',
        title: 'Exam Performance Report',
        type: 'Academic',
        period: 'Term 1',
        generatedBy: 'Exams Department',
        date: '2026-04-20',
        format: 'PDF',
        status: 'Processing',
    },
    {
        id: 'RPT-1004',
        title: 'Transport Usage Report',
        type: 'Transport',
        period: 'April 2026',
        generatedBy: 'Transport Office',
        date: '2026-04-18',
        format: 'PDF',
        status: 'Ready',
    },
]

const reportUsage = [
    { label: 'Academic', value: 86 },
    { label: 'Finance', value: 74 },
    { label: 'Attendance', value: 91 },
    { label: 'Transport', value: 58 },
]
</script>

<template>
    <AppLayout title="Reports">
        <div class="space-y-6">
            <div class="rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 ring-1 ring-slate-200 sm:p-6 lg:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">
                            Report Center
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                            Reports
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                            Generate, manage and export academic, attendance, finance, transport and staff reports.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                            Export All
                        </button>

                        <button
                            type="button"
                            class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800"
                            @click="showCreateModal = true"
                        >
                            Generate Report
                        </button>
                    </div>
                </div>
            </div>

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

                    <p
                        class="mt-3 text-xs font-bold"
                        :class="stat.label.includes('Pending') ? 'text-amber-600' : 'text-emerald-600'"
                    >
                        {{ stat.change }} this month
                    </p>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                    <div class="xl:col-span-2">
                        <label class="text-sm font-semibold text-slate-600">Search Report</label>
                        <input
                            type="text"
                            placeholder="Search by title, type, period or department"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        />
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Report Type</label>
                        <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option>All Types</option>
                            <option>Academic</option>
                            <option>Attendance</option>
                            <option>Finance</option>
                            <option>Transport</option>
                            <option>Staff</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Format</label>
                        <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option>All Formats</option>
                            <option>PDF</option>
                            <option>Excel</option>
                            <option>CSV</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Status</label>
                        <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option>All Status</option>
                            <option>Ready</option>
                            <option>Processing</option>
                            <option>Failed</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button class="w-full rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-100 transition hover:bg-indigo-700">
                            Filter
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-3">
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 xl:col-span-2">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-black text-slate-950">
                                Report Usage Overview
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Dummy report generation percentage by category.
                            </p>
                        </div>

                        <span class="w-fit rounded-full bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                            92% Completed
                        </span>
                    </div>

                    <div class="mt-8 grid h-72 grid-cols-4 items-end gap-4">
                        <div
                            v-for="item in reportUsage"
                            :key="item.label"
                            class="flex h-full flex-col items-center justify-end gap-3"
                        >
                            <div
                                class="w-full max-w-16 rounded-t-2xl bg-gradient-to-t from-indigo-500 to-sky-300 shadow-sm transition hover:scale-105"
                                :style="{ height: `${item.value}%` }"
                            ></div>

                            <div class="text-center">
                                <p class="text-xs font-bold text-slate-700">
                                    {{ item.value }}%
                                </p>
                                <p class="text-[11px] text-slate-400">
                                    {{ item.label }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-gradient-to-br from-indigo-600 to-sky-500 p-6 text-white shadow-lg shadow-indigo-100">
                    <h3 class="text-lg font-black">
                        Report Summary
                    </h3>

                    <p class="mt-1 text-sm text-white/75">
                        Current month overview
                    </p>

                    <div class="mt-6 space-y-4">
                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Most Generated</p>
                            <p class="mt-1 text-xl font-black">Attendance</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Ready Reports</p>
                            <p class="mt-1 text-xl font-black">57 Reports</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Pending Queue</p>
                            <p class="mt-1 text-xl font-black">7 Reports</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <div
                    v-for="report in reports"
                    :key="report.id"
                    class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-lg"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                {{ report.id }}
                            </p>

                            <h2 class="mt-2 text-lg font-black text-slate-950">
                                {{ report.title }}
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                {{ report.type }} · {{ report.period }}
                            </p>
                        </div>

                        <span
                            class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                            :class="
                                report.status === 'Ready'
                                    ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                    : report.status === 'Processing'
                                        ? 'bg-amber-50 text-amber-700 ring-amber-100'
                                        : 'bg-rose-50 text-rose-700 ring-rose-100'
                            "
                        >
                            {{ report.status }}
                        </span>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-indigo-50 p-4">
                            <p class="text-xs font-semibold text-indigo-500">Type</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ report.type }}</p>
                        </div>

                        <div class="rounded-2xl bg-sky-50 p-4">
                            <p class="text-xs font-semibold text-sky-500">Format</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ report.format }}</p>
                        </div>

                        <div class="rounded-2xl bg-violet-50 p-4">
                            <p class="text-xs font-semibold text-violet-500">Date</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ report.date }}</p>
                        </div>

                        <div class="rounded-2xl bg-amber-50 p-4">
                            <p class="text-xs font-semibold text-amber-500">By</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ report.generatedBy }}</p>
                        </div>
                    </div>

                    <div class="mt-5 flex gap-2">
                        <button class="flex-1 rounded-2xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                            View
                        </button>

                        <button class="flex-1 rounded-2xl bg-indigo-50 px-4 py-2.5 text-sm font-bold text-indigo-700 transition hover:bg-indigo-100">
                            Download
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">
                            Report Records
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Complete dummy report records.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800"
                        @click="showCreateModal = true"
                    >
                        New Report
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1050px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Report</th>
                                <th class="px-6 py-4">ID</th>
                                <th class="px-6 py-4">Type</th>
                                <th class="px-6 py-4">Period</th>
                                <th class="px-6 py-4">Generated By</th>
                                <th class="px-6 py-4">Format</th>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="report in reports"
                                :key="report.id"
                                class="transition hover:bg-indigo-50/40"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 to-sky-100 text-sm font-black text-indigo-700">
                                            {{ report.title.charAt(0) }}
                                        </div>

                                        <div>
                                            <p class="font-bold text-slate-950">
                                                {{ report.title }}
                                            </p>
                                            <p class="text-xs text-slate-400">
                                                {{ report.type }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-700">{{ report.id }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ report.type }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ report.period }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ report.generatedBy }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900">{{ report.format }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ report.date }}</td>

                                <td class="px-6 py-4">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                                        :class="
                                            report.status === 'Ready'
                                                ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                                : report.status === 'Processing'
                                                    ? 'bg-amber-50 text-amber-700 ring-amber-100'
                                                    : 'bg-rose-50 text-rose-700 ring-rose-100'
                                        "
                                    >
                                        {{ report.status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200">
                                            View
                                        </button>

                                        <button class="rounded-xl bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100">
                                            Download
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div
                v-if="showCreateModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
            >
                <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">
                                Generate Report
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Select report type, period, format and generate a new report.
                            </p>
                        </div>

                        <button
                            type="button"
                            class="rounded-2xl bg-white p-2 text-slate-400 shadow-sm ring-1 ring-slate-200 transition hover:text-slate-700"
                            @click="showCreateModal = false"
                        >
                            ✕
                        </button>
                    </div>

                    <div class="max-h-[75vh] overflow-y-auto p-5 sm:p-6">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="text-sm font-semibold text-slate-600">Report Title</label>
                                <input class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" placeholder="Monthly Attendance Report" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Report ID</label>
                                <input class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" placeholder="RPT-1005" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Report Type</label>
                                <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>Academic</option>
                                    <option>Attendance</option>
                                    <option>Finance</option>
                                    <option>Transport</option>
                                    <option>Staff</option>
                                    <option>Library</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Period</label>
                                <input class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" placeholder="April 2026" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Format</label>
                                <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>PDF</option>
                                    <option>Excel</option>
                                    <option>CSV</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Generated By</label>
                                <input class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" placeholder="Admin Office" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Date</label>
                                <input type="date" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status</label>
                                <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>Ready</option>
                                    <option>Processing</option>
                                    <option>Failed</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Description</label>
                                <textarea rows="3" placeholder="Optional report description" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 p-5 sm:flex-row sm:justify-end sm:p-6">
                        <button
                            type="button"
                            class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                            @click="showCreateModal = false"
                        >
                            Cancel
                        </button>

                        <button
                            type="button"
                            class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:bg-slate-800"
                        >
                            Save Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>