<script setup>
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const showCreateModal = ref(false)

const stats = [
    { label: 'Present Today', value: '1,112', change: '94%' },
    { label: 'Absent Today', value: '68', change: '5%' },
    { label: 'Late Arrivals', value: '18', change: '1%' },
    { label: 'Leave Requests', value: '12', change: '+3' },
]

const attendance = [
    {
        id: 'ATT-001',
        student: 'Ali Khan',
        admissionNo: 'STU-1001',
        class: 'Grade 8',
        section: 'A',
        date: '2026-04-27',
        checkIn: '07:45 AM',
        status: 'Present',
        remarks: 'On time',
    },
    {
        id: 'ATT-002',
        student: 'Sara Ahmed',
        admissionNo: 'STU-1002',
        class: 'Grade 6',
        section: 'B',
        date: '2026-04-27',
        checkIn: '07:58 AM',
        status: 'Present',
        remarks: 'On time',
    },
    {
        id: 'ATT-003',
        student: 'John Peter',
        admissionNo: 'STU-1003',
        class: 'Grade 10',
        section: 'C',
        date: '2026-04-27',
        checkIn: '--',
        status: 'Absent',
        remarks: 'No notice',
    },
    {
        id: 'ATT-004',
        student: 'Mariam Noor',
        admissionNo: 'STU-1004',
        class: 'Grade 7',
        section: 'A',
        date: '2026-04-27',
        checkIn: '08:21 AM',
        status: 'Late',
        remarks: 'Transport delay',
    },
]

const weeklyAttendance = [
    { day: 'Mon', percent: 94 },
    { day: 'Tue', percent: 91 },
    { day: 'Wed', percent: 96 },
    { day: 'Thu', percent: 89 },
    { day: 'Fri', percent: 93 },
    { day: 'Sat', percent: 87 },
    { day: 'Sun', percent: 95 },
]
</script>

<template>
    <AppLayout title="Attendance">
        <div class="space-y-6">
            <!-- Header -->
            <div class="rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 ring-1 ring-slate-200 sm:p-6 lg:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">
                            Daily Attendance
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                            Attendance
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                            Track student attendance, late arrivals, absences and daily class-wise attendance records.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button
                            class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50"
                        >
                            Export Report
                        </button>

                        <button
                            type="button"
                            class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800"
                            @click="showCreateModal = true"
                        >
                            Mark Attendance
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
                        {{ stat.change }} today
                    </p>
                </div>
            </div>

            <!-- Filters -->
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                    <div class="xl:col-span-2">
                        <label class="text-sm font-semibold text-slate-600">Search Student</label>
                        <input
                            type="text"
                            placeholder="Search by student name or admission number"
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
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Section</label>
                        <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option>All Sections</option>
                            <option>A</option>
                            <option>B</option>
                            <option>C</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Date</label>
                        <input
                            type="date"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        />
                    </div>

                    <div class="flex items-end">
                        <button class="w-full rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-100 transition hover:bg-indigo-700">
                            Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Overview + Chart -->
            <div class="grid gap-6 xl:grid-cols-3">
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 xl:col-span-2">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-black text-slate-950">
                                Weekly Attendance Overview
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Dummy attendance percentage by day.
                            </p>
                        </div>

                        <span class="w-fit rounded-full bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                            Avg 92.1%
                        </span>
                    </div>

                    <div class="mt-8 grid h-72 grid-cols-7 items-end gap-2 sm:gap-4">
                        <div
                            v-for="item in weeklyAttendance"
                            :key="item.day"
                            class="flex h-full flex-col items-center justify-end gap-3"
                        >
                            <div
                                class="w-full max-w-12 rounded-t-2xl bg-gradient-to-t from-indigo-500 to-sky-300 shadow-sm transition hover:scale-105"
                                :style="{ height: `${item.percent}%` }"
                            ></div>

                            <div class="text-center">
                                <p class="text-xs font-bold text-slate-700">
                                    {{ item.percent }}%
                                </p>
                                <p class="text-[11px] text-slate-400">
                                    {{ item.day }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-gradient-to-br from-indigo-600 to-sky-500 p-6 text-white shadow-lg shadow-indigo-100">
                    <h3 class="text-lg font-black">
                        Attendance Summary
                    </h3>

                    <p class="mt-1 text-sm text-white/75">
                        Today’s school-wide summary
                    </p>

                    <div class="mt-6 space-y-4">
                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <div class="flex justify-between text-sm">
                                <span>Present</span>
                                <span class="font-bold">94%</span>
                            </div>
                            <div class="mt-3 h-2 rounded-full bg-white/20">
                                <div class="h-2 w-[94%] rounded-full bg-white"></div>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <div class="flex justify-between text-sm">
                                <span>Absent</span>
                                <span class="font-bold">5%</span>
                            </div>
                            <div class="mt-3 h-2 rounded-full bg-white/20">
                                <div class="h-2 w-[5%] rounded-full bg-white"></div>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <div class="flex justify-between text-sm">
                                <span>Late</span>
                                <span class="font-bold">1%</span>
                            </div>
                            <div class="mt-3 h-2 rounded-full bg-white/20">
                                <div class="h-2 w-[1%] rounded-full bg-white"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Class Attendance Cards -->
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-bold text-slate-900">Grade 6</p>
                    <p class="mt-1 text-xs text-slate-500">Section A - B</p>
                    <div class="mt-4 h-2 rounded-full bg-slate-100">
                        <div class="h-2 w-[91%] rounded-full bg-emerald-500"></div>
                    </div>
                    <p class="mt-3 text-sm font-bold text-emerald-600">91% Present</p>
                </div>

                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-bold text-slate-900">Grade 8</p>
                    <p class="mt-1 text-xs text-slate-500">Section A - C</p>
                    <div class="mt-4 h-2 rounded-full bg-slate-100">
                        <div class="h-2 w-[95%] rounded-full bg-indigo-500"></div>
                    </div>
                    <p class="mt-3 text-sm font-bold text-indigo-600">95% Present</p>
                </div>

                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-bold text-slate-900">Grade 10</p>
                    <p class="mt-1 text-xs text-slate-500">Section A - C</p>
                    <div class="mt-4 h-2 rounded-full bg-slate-100">
                        <div class="h-2 w-[88%] rounded-full bg-amber-500"></div>
                    </div>
                    <p class="mt-3 text-sm font-bold text-amber-600">88% Present</p>
                </div>

                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-bold text-slate-900">Grade 11</p>
                    <p class="mt-1 text-xs text-slate-500">Section A - B</p>
                    <div class="mt-4 h-2 rounded-full bg-slate-100">
                        <div class="h-2 w-[93%] rounded-full bg-sky-500"></div>
                    </div>
                    <p class="mt-3 text-sm font-bold text-sky-600">93% Present</p>
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">
                            Attendance Records
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Daily dummy attendance list.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800"
                        @click="showCreateModal = true"
                    >
                        Mark Attendance
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1000px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Student</th>
                                <th class="px-6 py-4">Admission No</th>
                                <th class="px-6 py-4">Class</th>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Check In</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Remarks</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="item in attendance"
                                :key="item.id"
                                class="transition hover:bg-indigo-50/40"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 to-sky-100 text-sm font-black text-indigo-700">
                                            {{ item.student.charAt(0) }}
                                        </div>

                                        <div>
                                            <p class="font-bold text-slate-950">
                                                {{ item.student }}
                                            </p>
                                            <p class="text-xs text-slate-400">
                                                {{ item.id }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-700">
                                    {{ item.admissionNo }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ item.class }} - {{ item.section }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ item.date }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ item.checkIn }}
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                                        :class="
                                            item.status === 'Present'
                                                ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                                : item.status === 'Late'
                                                    ? 'bg-amber-50 text-amber-700 ring-amber-100'
                                                    : 'bg-rose-50 text-rose-700 ring-rose-100'
                                        "
                                    >
                                        {{ item.status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ item.remarks }}
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

            <!-- Mark Attendance Dialog -->
            <div
                v-if="showCreateModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
            >
                <div class="w-full max-w-5xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">
                                Mark Attendance
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Select class, date and mark present, absent, late or leave for students.
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
                        <!-- Form Controls -->
                        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <label class="text-sm font-semibold text-slate-600">Class</label>
                                <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>Select class</option>
                                    <option>Grade 6</option>
                                    <option>Grade 8</option>
                                    <option>Grade 10</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Section</label>
                                <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>Select section</option>
                                    <option>A</option>
                                    <option>B</option>
                                    <option>C</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Date</label>
                                <input
                                    type="date"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Default Status</label>
                                <select class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>Present</option>
                                    <option>Absent</option>
                                    <option>Late</option>
                                    <option>Leave</option>
                                </select>
                            </div>
                        </div>

                        <!-- Student Attendance List -->
                        <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200">
                            <div class="bg-slate-50 px-5 py-4">
                                <h3 class="text-sm font-black text-slate-950">
                                    Class Student List
                                </h3>
                                <p class="mt-1 text-xs text-slate-500">
                                    Dummy students for attendance marking.
                                </p>
                            </div>

                            <div class="divide-y divide-slate-100">
                                <div
                                    v-for="student in ['Ali Khan', 'Sara Ahmed', 'John Peter', 'Mariam Noor']"
                                    :key="student"
                                    class="grid gap-4 p-4 sm:grid-cols-[1fr_auto] sm:items-center"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-10 items-center justify-center rounded-2xl bg-indigo-100 font-bold text-indigo-700">
                                            {{ student.charAt(0) }}
                                        </div>

                                        <div>
                                            <p class="text-sm font-bold text-slate-950">
                                                {{ student }}
                                            </p>
                                            <p class="text-xs text-slate-400">
                                                Student attendance status
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <button class="rounded-xl bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                                            Present
                                        </button>

                                        <button class="rounded-xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 ring-1 ring-rose-100">
                                            Absent
                                        </button>

                                        <button class="rounded-xl bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 ring-1 ring-amber-100">
                                            Late
                                        </button>

                                        <button class="rounded-xl bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 ring-1 ring-sky-100">
                                            Leave
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <label class="text-sm font-semibold text-slate-600">Remarks</label>
                            <textarea
                                rows="3"
                                placeholder="Optional attendance note"
                                class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                            ></textarea>
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
                            Save Attendance
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>