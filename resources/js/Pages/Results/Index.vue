<script setup>
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const showCreateModal = ref(false)

const stats = [
    { label: 'Total Results', value: '156', change: '+18%' },
    { label: 'Passed Students', value: '128', change: '+12%' },
    { label: 'Failed Students', value: '21', change: '-5%' },
    { label: 'Pending Results', value: '7', change: '-2%' },
]

const results = [
    {
        id: 'RSL-1001',
        student: 'Ahmed Khan',
        class: 'Grade 10',
        subject: 'Mathematics',
        exam: 'Mid Term Mathematics',
        marks: 88,
        total: 100,
        grade: 'A',
        status: 'Passed',
    },
    {
        id: 'RSL-1002',
        student: 'Sara Ali',
        class: 'Grade 8',
        subject: 'English',
        exam: 'English Final Test',
        marks: 72,
        total: 80,
        grade: 'A',
        status: 'Passed',
    },
    {
        id: 'RSL-1003',
        student: 'Bilal Ahmed',
        class: 'Grade 11',
        subject: 'Physics',
        exam: 'Physics Practical',
        marks: 31,
        total: 50,
        grade: 'B',
        status: 'Passed',
    },
    {
        id: 'RSL-1004',
        student: 'Zain Malik',
        class: 'Grade 10',
        subject: 'Science',
        exam: 'Science Class Test',
        marks: 22,
        total: 60,
        grade: 'F',
        status: 'Failed',
    },
]

const performance = [
    { label: 'A+', value: 34 },
    { label: 'A', value: 48 },
    { label: 'B', value: 41 },
    { label: 'C', value: 26 },
]
</script>

<template>
    <AppLayout title="Results">
        <div class="space-y-6">
            <!-- Header -->
            <div class="rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 ring-1 ring-slate-200 sm:p-6 lg:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">
                            Result Management
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                            Results
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                            Manage student marks, grades, pass/fail status, result publishing and performance reports.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                            Export Results
                        </button>

                        <button
                            type="button"
                            class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800"
                            @click="showCreateModal = true"
                        >
                            Add Result
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

                    <p
                        class="mt-3 text-xs font-bold"
                        :class="stat.label.includes('Failed') || stat.label.includes('Pending') ? 'text-amber-600' : 'text-emerald-600'"
                    >
                        {{ stat.change }} this term
                    </p>
                </div>
            </div>

            <!-- Filters -->
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                    <div class="xl:col-span-2">
                        <label class="text-sm font-semibold text-slate-600">Search Result</label>
                        <input
                            type="text"
                            placeholder="Search by student, subject or exam"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        />
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Class</label>
                        <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option>All Classes</option>
                            <option>Grade 6</option>
                            <option>Grade 8</option>
                            <option>Grade 10</option>
                            <option>Grade 11</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Subject</label>
                        <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option>All Subjects</option>
                            <option>Mathematics</option>
                            <option>English</option>
                            <option>Physics</option>
                            <option>Science</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Status</label>
                        <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option>All Status</option>
                            <option>Passed</option>
                            <option>Failed</option>
                            <option>Pending</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button class="w-full rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-100 transition hover:bg-indigo-700">
                            Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Overview -->
            <div class="grid gap-6 xl:grid-cols-3">
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 xl:col-span-2">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-black text-slate-950">
                                Grade Distribution
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Dummy result distribution by grade.
                            </p>
                        </div>

                        <span class="w-fit rounded-full bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                            Pass Rate 82%
                        </span>
                    </div>

                    <div class="mt-8 grid h-72 grid-cols-4 items-end gap-4">
                        <div
                            v-for="item in performance"
                            :key="item.label"
                            class="flex h-full flex-col items-center justify-end gap-3"
                        >
                            <div
                                class="w-full max-w-16 rounded-t-2xl bg-gradient-to-t from-indigo-500 to-sky-300 shadow-sm transition hover:scale-105"
                                :style="{ height: `${item.value * 2}%` }"
                            ></div>

                            <div class="text-center">
                                <p class="text-xs font-bold text-slate-700">
                                    {{ item.value }}
                                </p>
                                <p class="text-[11px] text-slate-400">
                                    Grade {{ item.label }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-gradient-to-br from-indigo-600 to-sky-500 p-6 text-white shadow-lg shadow-indigo-100">
                    <h3 class="text-lg font-black">
                        Result Summary
                    </h3>

                    <p class="mt-1 text-sm text-white/75">
                        Current academic term overview
                    </p>

                    <div class="mt-6 space-y-4">
                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Highest Score</p>
                            <p class="mt-1 text-xl font-black">91%</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Top Class</p>
                            <p class="mt-1 text-xl font-black">Grade 10</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Pending Uploads</p>
                            <p class="mt-1 text-xl font-black">7 Results</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Result Cards -->
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <div
                    v-for="result in results"
                    :key="result.id"
                    class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-lg"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                {{ result.id }}
                            </p>

                            <h2 class="mt-2 text-lg font-black text-slate-950">
                                {{ result.student }}
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                {{ result.class }} · {{ result.subject }}
                            </p>
                        </div>

                        <span
                            class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                            :class="result.status === 'Passed'
                                ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                : 'bg-rose-50 text-rose-700 ring-rose-100'"
                        >
                            {{ result.status }}
                        </span>
                    </div>

                    <div class="mt-6 rounded-2xl bg-indigo-50 p-4">
                        <p class="text-xs font-semibold text-indigo-500">
                            Marks
                        </p>

                        <p class="mt-1 text-2xl font-black text-slate-950">
                            {{ result.marks }}/{{ result.total }}
                        </p>

                        <p class="mt-1 text-sm font-bold text-indigo-700">
                            Grade {{ result.grade }}
                        </p>
                    </div>

                    <div class="mt-5 flex gap-2">
                        <button class="flex-1 rounded-2xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                            View
                        </button>

                        <button class="flex-1 rounded-2xl bg-indigo-50 px-4 py-2.5 text-sm font-bold text-indigo-700 transition hover:bg-indigo-100">
                            Edit
                        </button>
                    </div>
                </div>
            </div>

            <!-- Results Table -->
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">
                            Result Records
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Complete dummy result records.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800"
                        @click="showCreateModal = true"
                    >
                        New Result
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1050px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Student</th>
                                <th class="px-6 py-4">Result ID</th>
                                <th class="px-6 py-4">Class</th>
                                <th class="px-6 py-4">Subject</th>
                                <th class="px-6 py-4">Exam</th>
                                <th class="px-6 py-4">Marks</th>
                                <th class="px-6 py-4">Grade</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="result in results"
                                :key="result.id"
                                class="transition hover:bg-indigo-50/40"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 to-sky-100 text-sm font-black text-indigo-700">
                                            {{ result.student.charAt(0) }}
                                        </div>

                                        <div>
                                            <p class="font-bold text-slate-950">
                                                {{ result.student }}
                                            </p>
                                            <p class="text-xs text-slate-400">
                                                {{ result.exam }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-700">
                                    {{ result.id }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ result.class }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ result.subject }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ result.exam }}
                                </td>

                                <td class="px-6 py-4 font-bold text-slate-900">
                                    {{ result.marks }}/{{ result.total }}
                                </td>

                                <td class="px-6 py-4 font-bold text-indigo-700">
                                    {{ result.grade }}
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                                        :class="result.status === 'Passed'
                                            ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                            : 'bg-rose-50 text-rose-700 ring-rose-100'"
                                    >
                                        {{ result.status }}
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

            <!-- Create Result Modal -->
            <div
                v-if="showCreateModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
            >
                <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">
                                Add Result
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Add student exam marks, grade and pass/fail status.
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
                                <label class="text-sm font-semibold text-slate-600">Student Name</label>
                                <input class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" placeholder="Ahmed Khan" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Result ID</label>
                                <input class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" placeholder="RSL-1005" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Class</label>
                                <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>Select class</option>
                                    <option>Grade 6</option>
                                    <option>Grade 8</option>
                                    <option>Grade 10</option>
                                    <option>Grade 11</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Subject</label>
                                <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>Select subject</option>
                                    <option>Mathematics</option>
                                    <option>English</option>
                                    <option>Physics</option>
                                    <option>Science</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Exam</label>
                                <input class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" placeholder="Mid Term Mathematics" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Total Marks</label>
                                <input type="number" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" placeholder="100" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Obtained Marks</label>
                                <input type="number" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" placeholder="88" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Grade</label>
                                <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>A+</option>
                                    <option>A</option>
                                    <option>B</option>
                                    <option>C</option>
                                    <option>F</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status</label>
                                <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>Passed</option>
                                    <option>Failed</option>
                                    <option>Pending</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Remarks</label>
                                <textarea rows="3" placeholder="Optional remarks" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"></textarea>
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
                            Save Result
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>