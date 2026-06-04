<script setup>
import { ref } from 'vue';

import AppLayout from '@/Layouts/AppLayout.vue';

const student = {
    name: 'Ahmed Khan',
    class: 'Grade 8 - A',
    rollNo: 'STU-1024',
    attendance: 92,
    avatar: 'AK',
    status: 'Active',
    section: 'A',
    academicYear: '2025 - 2026',
};

const stats = [
    { label: 'Attendance', value: '92%', sub: 'This month' },
    { label: 'Subjects', value: '8', sub: 'Assigned' },
    { label: 'Assignments', value: '4', sub: 'Pending' },
    { label: 'Average Grade', value: 'A', sub: 'Current term' },
];

const activities = [
    { title: 'Science Exhibition', date: '12 May 2026', type: 'Event' },
    { title: 'Inter House Football Match', date: '15 May 2026', type: 'Sports' },
    { title: 'Math Quiz Competition', date: '18 May 2026', type: 'Academic' },
];

const subjects = [
    { name: 'Mathematics', teacher: 'Mr. David' },
    { name: 'Science', teacher: 'Ms. Sarah' },
    { name: 'English', teacher: 'Ms. Emma' },
    { name: 'Computer', teacher: 'Mr. Ali' },
];

const timetable = [
    { time: '08:00 - 09:00', subject: 'Mathematics', room: 'Room 201' },
    { time: '09:00 - 10:00', subject: 'Science', room: 'Lab 2' },
    { time: '10:30 - 11:30', subject: 'English', room: 'Room 105' },
    { time: '11:30 - 12:30', subject: 'Computer', room: 'ICT Lab' },
];

const results = [
    { subject: 'Mathematics', marks: '88/100', grade: 'A', status: 'Passed' },
    { subject: 'Science', marks: '91/100', grade: 'A+', status: 'Passed' },
    { subject: 'English', marks: '76/100', grade: 'B', status: 'Passed' },
];

const assignments = [
    { title: 'Algebra Worksheet', subject: 'Mathematics', due: '09 May 2026' },
    { title: 'Science Lab Report', subject: 'Science', due: '11 May 2026' },
    { title: 'Essay Writing', subject: 'English', due: '14 May 2026' },
];
const exams = [
    {
        name: 'Unit Test 1',
        date: '15 March 2026',
        percentage: '86%',
        grade: 'A',
        results: [
            { subject: 'Mathematics', marks: '88/100', grade: 'A' },
            { subject: 'Science', marks: '91/100', grade: 'A+' },
            { subject: 'English', marks: '79/100', grade: 'B+' },
        ],
    },
    {
        name: 'Semester Exam',
        date: '20 April 2026',
        percentage: '89%',
        grade: 'A',
        results: [
            { subject: 'Mathematics', marks: '90/100', grade: 'A+' },
            { subject: 'Science', marks: '87/100', grade: 'A' },
            { subject: 'English', marks: '82/100', grade: 'A' },
        ],
    },
    {
        name: 'Annual Exam',
        date: '25 June 2026',
        percentage: 'Pending',
        grade: 'Pending',
        results: [],
    },
];

const selectedExam = ref(null);

const openExam = (exam) => {
    selectedExam.value = exam;
};

const closeExam = () => {
    selectedExam.value = null;
};
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-50 px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl space-y-6">

                <!-- Header -->
                <div class="flex flex-col gap-4 rounded-3xl bg-gradient-to-r from-indigo-600 via-blue-600 to-sky-500 p-6 text-white shadow-lg sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-100">Welcome back</p>
                        <h1 class="mt-1 text-2xl font-bold sm:text-3xl">
                            {{ student.name }}
                        </h1>
                        <p class="mt-2 text-sm text-blue-100">
                            {{ student.class }} · Roll No: {{ student.rollNo }} · Academic Year {{ student.academicYear }}
                        </p>
                    </div>

                    <div class="flex items-center gap-4 rounded-2xl bg-white/15 p-4 backdrop-blur">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-xl font-bold text-indigo-600">
                            {{ student.avatar }}
                        </div>
                        <div>
                            <p class="text-sm text-blue-100">Status</p>
                            <p class="font-semibold">{{ student.status }}</p>
                            <p class="text-sm text-blue-100">Section {{ student.section }}</p>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div
                        v-for="item in stats"
                        :key="item.label"
                        class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md"
                    >
                        <p class="text-sm font-medium text-slate-500">{{ item.label }}</p>
                        <div class="mt-3 flex items-end justify-between">
                            <h2 class="text-3xl font-bold text-slate-900">{{ item.value }}</h2>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                {{ item.sub }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

                    <!-- Main Content -->
                    <div class="space-y-6 xl:col-span-2">

                        <!-- Today Timetable -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h2 class="text-lg font-bold text-slate-900">Today Timetable</h2>
                                    <p class="text-sm text-slate-500">Your scheduled classes for today</p>
                                </div>
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600">
                                    Today
                                </span>
                            </div>

                            <div class="space-y-3">
                                <div
                                    v-for="item in timetable"
                                    :key="item.time"
                                    class="flex flex-col gap-3 rounded-2xl border border-slate-100 bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between"
                                >
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ item.subject }}</p>
                                        <p class="text-sm text-slate-500">{{ item.room }}</p>
                                    </div>
                                    <div class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm">
                                        {{ item.time }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Exam Results -->
                       <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-5">
        <h2 class="text-lg font-bold text-slate-900">Exam Results</h2>
        <p class="text-sm text-slate-500">Select an exam to view subject-wise marks</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <button
            v-for="exam in exams"
            :key="exam.name"
            type="button"
            @click="openExam(exam)"
            class="rounded-2xl border border-slate-200 bg-slate-50 p-5 text-left transition hover:-translate-y-1 hover:border-blue-300 hover:bg-blue-50 hover:shadow-md"
        >
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 class="font-bold text-slate-900">{{ exam.name }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ exam.date }}</p>
                </div>

                <span
                    class="rounded-full px-3 py-1 text-xs font-semibold"
                    :class="exam.grade === 'Pending'
                        ? 'bg-yellow-50 text-yellow-600'
                        : 'bg-emerald-50 text-emerald-600'"
                >
                    {{ exam.grade }}
                </span>
            </div>

            <div class="mt-5 flex items-center justify-between">
                <p class="text-sm text-slate-500">Overall</p>
                <p class="text-xl font-bold text-slate-900">{{ exam.percentage }}</p>
            </div>
        </button>
    </div>
</div>

                    </div>

                    <!-- Sidebar Content -->
                    <div class="space-y-6">

                        <!-- Attendance Card -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-bold text-slate-900">Attendance</h2>
                            <p class="mt-1 text-sm text-slate-500">Current month overview</p>

                            <div class="mt-6 flex justify-center">
                                <div class="relative flex h-36 w-36 items-center justify-center rounded-full bg-blue-50">
                                    <div class="flex h-28 w-28 items-center justify-center rounded-full bg-white shadow-inner">
                                        <div class="text-center">
                                            <p class="text-3xl font-bold text-blue-600">{{ student.attendance }}%</p>
                                            <p class="text-xs text-slate-500">Present</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Subjects -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-bold text-slate-900">My Subjects</h2>
                            <div class="mt-4 space-y-3">
                                <div
                                    v-for="subject in subjects"
                                    :key="subject.name"
                                    class="rounded-2xl bg-slate-50 p-4"
                                >
                                    <p class="font-semibold text-slate-900">{{ subject.name }}</p>
                                    <p class="text-sm text-slate-500">{{ subject.teacher }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Activities -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-bold text-slate-900">Upcoming Activities</h2>
                            <div class="mt-4 space-y-4">
                                <div
                                    v-for="activity in activities"
                                    :key="activity.title"
                                    class="border-b border-slate-100 pb-4 last:border-0 last:pb-0"
                                >
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ activity.title }}</p>
                                            <p class="text-sm text-slate-500">{{ activity.date }}</p>
                                        </div>
                                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-600">
                                            {{ activity.type }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Assignments -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-bold text-slate-900">Pending Assignments</h2>
                            <div class="mt-4 space-y-3">
                                <div
                                    v-for="assignment in assignments"
                                    :key="assignment.title"
                                    class="rounded-2xl border border-orange-100 bg-orange-50 p-4"
                                >
                                    <p class="font-semibold text-slate-900">{{ assignment.title }}</p>
                                    <p class="text-sm text-slate-600">{{ assignment.subject }}</p>
                                    <p class="mt-1 text-xs font-medium text-orange-600">
                                        Due: {{ assignment.due }}
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div
    v-if="selectedExam"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-6"
>
    <div class="w-full max-w-2xl rounded-3xl bg-white shadow-2xl">
        <div class="flex items-start justify-between border-b border-slate-100 p-6">
            <div>
                <h2 class="text-xl font-bold text-slate-900">
                    {{ selectedExam.name }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    {{ selectedExam.date }} · Overall {{ selectedExam.percentage }}
                </p>
            </div>

            <button
                type="button"
                @click="closeExam"
                class="rounded-full bg-slate-100 px-3 py-1 text-sm font-bold text-slate-600 hover:bg-slate-200"
            >
                ✕
            </button>
        </div>

        <div class="p-6">
            <div v-if="selectedExam.results.length" class="overflow-x-auto">
                <table class="w-full min-w-[500px] text-left text-sm">
                    <thead>
                        <tr class="border-b bg-slate-50 text-slate-500">
                            <th class="rounded-l-xl px-4 py-3 font-semibold">Subject</th>
                            <th class="px-4 py-3 font-semibold">Marks</th>
                            <th class="rounded-r-xl px-4 py-3 font-semibold">Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="result in selectedExam.results"
                            :key="result.subject"
                            class="border-b last:border-0"
                        >
                            <td class="px-4 py-4 font-medium text-slate-900">
                                {{ result.subject }}
                            </td>
                            <td class="px-4 py-4 text-slate-600">
                                {{ result.marks }}
                            </td>
                            <td class="px-4 py-4">
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-600">
                                    {{ result.grade }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="rounded-2xl bg-yellow-50 p-5 text-center">
                <p class="font-semibold text-yellow-700">
                    Result not published yet.
                </p>
                <p class="mt-1 text-sm text-yellow-600">
                    Please check again later.
                </p>
            </div>
        </div>
    </div>
</div>
    </AppLayout>
</template>