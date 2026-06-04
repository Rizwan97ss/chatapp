<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

const parent = {
    name: 'Mr. Khan',
    phone: '+971 50 123 4567',
    email: 'parent@example.com',
    child: 'Ahmed Khan',
    class: 'Grade 8 - A',
    rollNo: 'STU-1024',
    avatar: 'MK',
};

const stats = [
    { label: 'Attendance', value: '92%', sub: 'This month' },
    { label: 'Pending Fees', value: 'AED 1,500', sub: 'Due soon' },
    { label: 'Assignments', value: '4', sub: 'Pending' },
    { label: 'Average Grade', value: 'A', sub: 'Current term' },
];

const fees = {
    total: 'AED 4,500',
    paid: 'AED 3,000',
    pending: 'AED 1,500',
    dueDate: '20 May 2026',
};

const notices = [
    { title: 'Parent Teacher Meeting', date: '10 May 2026', type: 'Meeting' },
    { title: 'Fee Due Reminder', date: '20 May 2026', type: 'Fees' },
    { title: 'Annual Day Practice', date: '24 May 2026', type: 'Activity' },
];

const attendanceHistory = [
    { month: 'March', present: 24, absent: 2, percentage: '92%' },
    { month: 'April', present: 23, absent: 3, percentage: '88%' },
    { month: 'May', present: 12, absent: 1, percentage: '92%' },
];

const childResults = [
    { subject: 'Mathematics', marks: '88/100', grade: 'A' },
    { subject: 'Science', marks: '91/100', grade: 'A+' },
    { subject: 'English', marks: '76/100', grade: 'B' },
    { subject: 'Computer', marks: '94/100', grade: 'A+' },
];

const assignments = [
    { title: 'Algebra Worksheet', subject: 'Mathematics', due: '09 May 2026', status: 'Pending' },
    { title: 'Science Lab Report', subject: 'Science', due: '11 May 2026', status: 'Submitted' },
    { title: 'Essay Writing', subject: 'English', due: '14 May 2026', status: 'Pending' },
];

const timetable = [
    { time: '08:00 - 09:00', subject: 'Mathematics' },
    { time: '09:00 - 10:00', subject: 'Science' },
    { time: '10:30 - 11:30', subject: 'English' },
    { time: '11:30 - 12:30', subject: 'Computer' },
];
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-50 px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl space-y-6">

                <!-- Header -->
                <div class="rounded-3xl bg-gradient-to-r from-violet-600 via-indigo-600 to-blue-600 p-6 text-white shadow-lg">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-sm font-medium text-indigo-100">Parent Portal</p>
                            <h1 class="mt-1 text-2xl font-bold sm:text-3xl">
                                Welcome, {{ parent.name }}
                            </h1>
                            <p class="mt-2 text-sm text-indigo-100">
                                Monitor your child’s academics, attendance, fees, notices and daily updates.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 backdrop-blur">
                            <div class="flex items-center gap-4">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-xl font-bold text-indigo-600">
                                    {{ parent.avatar }}
                                </div>
                                <div>
                                    <p class="font-semibold">{{ parent.child }}</p>
                                    <p class="text-sm text-indigo-100">{{ parent.class }}</p>
                                    <p class="text-sm text-indigo-100">Roll No: {{ parent.rollNo }}</p>
                                </div>
                            </div>
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
                            <h2 class="text-2xl font-bold text-slate-900">{{ item.value }}</h2>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                {{ item.sub }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

                    <!-- Left/Main -->
                    <div class="space-y-6 xl:col-span-2">

                        <!-- Child Overview -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="mb-5">
                                <h2 class="text-lg font-bold text-slate-900">Child Overview</h2>
                                <p class="text-sm text-slate-500">Basic student information</p>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <p class="text-sm text-slate-500">Student Name</p>
                                    <p class="mt-1 font-semibold text-slate-900">{{ parent.child }}</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <p class="text-sm text-slate-500">Class</p>
                                    <p class="mt-1 font-semibold text-slate-900">{{ parent.class }}</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <p class="text-sm text-slate-500">Roll Number</p>
                                    <p class="mt-1 font-semibold text-slate-900">{{ parent.rollNo }}</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <p class="text-sm text-slate-500">Parent Contact</p>
                                    <p class="mt-1 font-semibold text-slate-900">{{ parent.phone }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Fee Summary -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h2 class="text-lg font-bold text-slate-900">Fee Summary</h2>
                                    <p class="text-sm text-slate-500">Current academic fee status</p>
                                </div>

                                <span class="rounded-full bg-red-50 px-4 py-2 text-sm font-semibold text-red-600">
                                    Due: {{ fees.dueDate }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <div class="rounded-2xl bg-blue-50 p-4">
                                    <p class="text-sm font-medium text-blue-600">Total Fees</p>
                                    <p class="mt-2 text-xl font-bold text-blue-900">{{ fees.total }}</p>
                                </div>

                                <div class="rounded-2xl bg-emerald-50 p-4">
                                    <p class="text-sm font-medium text-emerald-600">Paid</p>
                                    <p class="mt-2 text-xl font-bold text-emerald-900">{{ fees.paid }}</p>
                                </div>

                                <div class="rounded-2xl bg-red-50 p-4">
                                    <p class="text-sm font-medium text-red-600">Pending</p>
                                    <p class="mt-2 text-xl font-bold text-red-900">{{ fees.pending }}</p>
                                </div>
                            </div>

                            <div class="mt-5">
                                <div class="mb-2 flex justify-between text-sm">
                                    <span class="text-slate-500">Payment Progress</span>
                                    <span class="font-semibold text-slate-700">67%</span>
                                </div>
                                <div class="h-3 rounded-full bg-slate-100">
                                    <div class="h-3 w-2/3 rounded-full bg-emerald-500"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Results -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="mb-5">
                                <h2 class="text-lg font-bold text-slate-900">Latest Results</h2>
                                <p class="text-sm text-slate-500">Subject-wise performance</p>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full min-w-[600px] text-left text-sm">
                                    <thead>
                                        <tr class="border-b bg-slate-50 text-slate-500">
                                            <th class="rounded-l-xl px-4 py-3 font-semibold">Subject</th>
                                            <th class="px-4 py-3 font-semibold">Marks</th>
                                            <th class="rounded-r-xl px-4 py-3 font-semibold">Grade</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="result in childResults"
                                            :key="result.subject"
                                            class="border-b last:border-0"
                                        >
                                            <td class="px-4 py-4 font-medium text-slate-900">{{ result.subject }}</td>
                                            <td class="px-4 py-4 text-slate-600">{{ result.marks }}</td>
                                            <td class="px-4 py-4">
                                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-600">
                                                    {{ result.grade }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Attendance History -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="mb-5">
                                <h2 class="text-lg font-bold text-slate-900">Attendance History</h2>
                                <p class="text-sm text-slate-500">Monthly attendance overview</p>
                            </div>

                            <div class="space-y-3">
                                <div
                                    v-for="item in attendanceHistory"
                                    :key="item.month"
                                    class="rounded-2xl bg-slate-50 p-4"
                                >
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ item.month }}</p>
                                            <p class="text-sm text-slate-500">
                                                Present: {{ item.present }} · Absent: {{ item.absent }}
                                            </p>
                                        </div>
                                        <p class="text-lg font-bold text-blue-600">{{ item.percentage }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Sidebar -->
                    <div class="space-y-6">

                        <!-- Quick Contact -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-bold text-slate-900">Parent Info</h2>
                            <div class="mt-4 space-y-3">
                                <div>
                                    <p class="text-sm text-slate-500">Name</p>
                                    <p class="font-semibold text-slate-900">{{ parent.name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500">Phone</p>
                                    <p class="font-semibold text-slate-900">{{ parent.phone }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500">Email</p>
                                    <p class="break-all font-semibold text-slate-900">{{ parent.email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Notices -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-bold text-slate-900">School Notices</h2>

                            <div class="mt-4 space-y-4">
                                <div
                                    v-for="notice in notices"
                                    :key="notice.title"
                                    class="border-b border-slate-100 pb-4 last:border-0 last:pb-0"
                                >
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ notice.title }}</p>
                                            <p class="text-sm text-slate-500">{{ notice.date }}</p>
                                        </div>

                                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-600">
                                            {{ notice.type }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Today Timetable -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-bold text-slate-900">Today Timetable</h2>

                            <div class="mt-4 space-y-3">
                                <div
                                    v-for="item in timetable"
                                    :key="item.time"
                                    class="rounded-2xl bg-slate-50 p-4"
                                >
                                    <p class="font-semibold text-slate-900">{{ item.subject }}</p>
                                    <p class="text-sm text-slate-500">{{ item.time }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Assignments -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-bold text-slate-900">Assignments</h2>

                            <div class="mt-4 space-y-3">
                                <div
                                    v-for="assignment in assignments"
                                    :key="assignment.title"
                                    class="rounded-2xl border p-4"
                                    :class="assignment.status === 'Submitted'
                                        ? 'border-emerald-100 bg-emerald-50'
                                        : 'border-orange-100 bg-orange-50'"
                                >
                                    <p class="font-semibold text-slate-900">{{ assignment.title }}</p>
                                    <p class="text-sm text-slate-600">{{ assignment.subject }}</p>
                                    <div class="mt-2 flex items-center justify-between">
                                        <p class="text-xs text-slate-500">Due: {{ assignment.due }}</p>
                                        <span
                                            class="rounded-full px-2 py-1 text-xs font-semibold"
                                            :class="assignment.status === 'Submitted'
                                                ? 'bg-emerald-100 text-emerald-700'
                                                : 'bg-orange-100 text-orange-700'"
                                        >
                                            {{ assignment.status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>