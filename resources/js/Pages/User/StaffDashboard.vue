<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const activeModal = ref(null);
const selectedItem = ref(null);

const openModal = (type, item = null) => {
    activeModal.value = type;
    selectedItem.value = item;
};

const closeModal = () => {
    activeModal.value = null;
    selectedItem.value = null;
};

const staff = {
    name: 'Robert Mathew',
    role: 'Accountant',
    department: 'Finance Department',
    employeeId: 'EMP-1045',
    status: 'Active',
    joiningDate: '12 Jan 2023',
    phone: '+971 50 123 7788',
    email: 'staff@example.com',
    avatar: 'RM',
    academicYear: '2025 - 2026',
};

const stats = [
    { label: 'Monthly Salary', value: 'AED 6,500', sub: 'Current month' },
    { label: 'Attendance', value: '94%', sub: 'This month' },
    { label: 'Leaves Left', value: '8', sub: 'Annual leave' },
    { label: 'Pending Tasks', value: '5', sub: 'Assigned' },
];

const salary = {
    basic: 'AED 5,000',
    allowance: 'AED 1,500',
    deduction: 'AED 250',
    net: 'AED 6,250',
    status: 'Paid',
    paidDate: '01 May 2026',
};

const attendance = [
    { date: '06 May 2026', checkIn: '08:02 AM', checkOut: '04:05 PM', status: 'Present' },
    { date: '05 May 2026', checkIn: '08:10 AM', checkOut: '04:00 PM', status: 'Present' },
    { date: '04 May 2026', checkIn: '-', checkOut: '-', status: 'Leave' },
];

const leaves = [
    { type: 'Annual Leave', from: '15 May 2026', to: '16 May 2026', status: 'Pending' },
    { type: 'Sick Leave', from: '04 May 2026', to: '04 May 2026', status: 'Approved' },
];

const tasks = [
    { title: 'Prepare monthly fee report', due: '08 May 2026', priority: 'High' },
    { title: 'Verify vendor payment', due: '10 May 2026', priority: 'Medium' },
    { title: 'Update payroll records', due: '12 May 2026', priority: 'High' },
];

const notices = [
    { title: 'Staff meeting at 2 PM', date: '06 May 2026', type: 'Meeting' },
    { title: 'Submit leave requests before Friday', date: '08 May 2026', type: 'HR' },
];

const documents = [
    { name: 'Salary Slip - May 2026', type: 'Payslip' },
    { name: 'Employment Contract', type: 'HR Document' },
    { name: 'ID Card Copy', type: 'Profile Document' },
];
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-50 px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl space-y-6">

                <!-- Header -->
                <div class="flex flex-col gap-4 rounded-3xl bg-gradient-to-r from-indigo-600 via-blue-600 to-sky-500 p-6 text-white shadow-lg sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-100">Staff Portal</p>
                        <h1 class="mt-1 text-2xl font-bold sm:text-3xl">
                            Welcome, {{ staff.name }}
                        </h1>
                        <p class="mt-2 text-sm text-blue-100">
                            {{ staff.role }} · {{ staff.department }} · Academic Year {{ staff.academicYear }}
                        </p>
                    </div>

                    <div class="flex items-center gap-4 rounded-2xl bg-white/15 p-4 backdrop-blur">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-xl font-bold text-indigo-600">
                            {{ staff.avatar }}
                        </div>
                        <div>
                            <p class="text-sm text-blue-100">Status</p>
                            <p class="font-semibold">{{ staff.status }}</p>
                            <p class="text-sm text-blue-100">ID: {{ staff.employeeId }}</p>
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

                <!-- Quick Actions -->
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Staff Actions</h2>
                            <p class="text-sm text-slate-500">Access salary, attendance, leaves, tasks and documents</p>
                        </div>

                        <span class="w-fit rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600">
                            {{ staff.role }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
                        <button @click="openModal('salary')" class="rounded-2xl border border-blue-100 bg-blue-50 p-4 text-left transition hover:-translate-y-1 hover:border-blue-300 hover:bg-blue-100 hover:shadow-md">
                            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-lg font-bold text-white">S</div>
                            <h3 class="font-bold text-slate-900">Salary Slip</h3>
                            <p class="mt-1 text-sm text-slate-500">View salary and payment details</p>
                        </button>

                        <button @click="openModal('leave')" class="rounded-2xl border border-indigo-100 bg-indigo-50 p-4 text-left transition hover:-translate-y-1 hover:border-indigo-300 hover:bg-indigo-100 hover:shadow-md">
                            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-600 text-lg font-bold text-white">L</div>
                            <h3 class="font-bold text-slate-900">Apply Leave</h3>
                            <p class="mt-1 text-sm text-slate-500">Request annual, sick or emergency leave</p>
                        </button>

                        <button @click="openModal('attendance')" class="rounded-2xl border border-sky-100 bg-sky-50 p-4 text-left transition hover:-translate-y-1 hover:border-sky-300 hover:bg-sky-100 hover:shadow-md">
                            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-sky-600 text-lg font-bold text-white">A</div>
                            <h3 class="font-bold text-slate-900">Attendance</h3>
                            <p class="mt-1 text-sm text-slate-500">View check-in and check-out records</p>
                        </button>

                        <button @click="openModal('task')" class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-left transition hover:-translate-y-1 hover:border-emerald-300 hover:bg-emerald-100 hover:shadow-md">
                            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-600 text-lg font-bold text-white">T</div>
                            <h3 class="font-bold text-slate-900">My Tasks</h3>
                            <p class="mt-1 text-sm text-slate-500">Track assigned daily work</p>
                        </button>

                        <button @click="openModal('document')" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left transition hover:-translate-y-1 hover:border-slate-400 hover:bg-slate-100 hover:shadow-md">
                            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-slate-900 text-lg font-bold text-white">D</div>
                            <h3 class="font-bold text-slate-900">Documents</h3>
                            <p class="mt-1 text-sm text-slate-500">View payslips and HR documents</p>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

                    <!-- Main -->
                    <div class="space-y-6 xl:col-span-2">

                        <!-- Salary Summary -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h2 class="text-lg font-bold text-slate-900">Salary Summary</h2>
                                    <p class="text-sm text-slate-500">Current month payment overview</p>
                                </div>
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-600">
                                    {{ salary.status }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                                <div class="rounded-2xl bg-blue-50 p-4">
                                    <p class="text-sm font-medium text-blue-600">Basic</p>
                                    <p class="mt-2 text-lg font-bold text-blue-900">{{ salary.basic }}</p>
                                </div>
                                <div class="rounded-2xl bg-indigo-50 p-4">
                                    <p class="text-sm font-medium text-indigo-600">Allowance</p>
                                    <p class="mt-2 text-lg font-bold text-indigo-900">{{ salary.allowance }}</p>
                                </div>
                                <div class="rounded-2xl bg-red-50 p-4">
                                    <p class="text-sm font-medium text-red-600">Deduction</p>
                                    <p class="mt-2 text-lg font-bold text-red-900">{{ salary.deduction }}</p>
                                </div>
                                <div class="rounded-2xl bg-emerald-50 p-4">
                                    <p class="text-sm font-medium text-emerald-600">Net Pay</p>
                                    <p class="mt-2 text-lg font-bold text-emerald-900">{{ salary.net }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Records -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="mb-5">
                                <h2 class="text-lg font-bold text-slate-900">Recent Attendance</h2>
                                <p class="text-sm text-slate-500">Latest check-in and check-out records</p>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full min-w-[650px] text-left text-sm">
                                    <thead>
                                        <tr class="border-b bg-slate-50 text-slate-500">
                                            <th class="rounded-l-xl px-4 py-3 font-semibold">Date</th>
                                            <th class="px-4 py-3 font-semibold">Check In</th>
                                            <th class="px-4 py-3 font-semibold">Check Out</th>
                                            <th class="rounded-r-xl px-4 py-3 font-semibold">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="item in attendance" :key="item.date" class="border-b last:border-0">
                                            <td class="px-4 py-4 font-medium text-slate-900">{{ item.date }}</td>
                                            <td class="px-4 py-4 text-slate-600">{{ item.checkIn }}</td>
                                            <td class="px-4 py-4 text-slate-600">{{ item.checkOut }}</td>
                                            <td class="px-4 py-4">
                                                <span
                                                    class="rounded-full px-3 py-1 text-xs font-bold"
                                                    :class="item.status === 'Present'
                                                        ? 'bg-emerald-50 text-emerald-600'
                                                        : 'bg-yellow-50 text-yellow-600'"
                                                >
                                                    {{ item.status }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tasks -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="mb-5">
                                <h2 class="text-lg font-bold text-slate-900">Assigned Tasks</h2>
                                <p class="text-sm text-slate-500">Your current work items</p>
                            </div>

                            <div class="space-y-4">
                                <div
                                    v-for="task in tasks"
                                    :key="task.title"
                                    class="rounded-2xl border p-4"
                                    :class="task.priority === 'High'
                                        ? 'border-red-100 bg-red-50'
                                        : 'border-yellow-100 bg-yellow-50'"
                                >
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ task.title }}</p>
                                            <p class="text-sm text-slate-600">Due: {{ task.due }}</p>
                                        </div>

                                        <span
                                            class="w-fit rounded-full px-3 py-1 text-xs font-bold"
                                            :class="task.priority === 'High'
                                                ? 'bg-red-100 text-red-700'
                                                : 'bg-yellow-100 text-yellow-700'"
                                        >
                                            {{ task.priority }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">

                        <!-- Staff Info -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-bold text-slate-900">Staff Info</h2>

                            <div class="mt-4 space-y-3">
                                <div>
                                    <p class="text-sm text-slate-500">Name</p>
                                    <p class="font-semibold text-slate-900">{{ staff.name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500">Role</p>
                                    <p class="font-semibold text-slate-900">{{ staff.role }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500">Department</p>
                                    <p class="font-semibold text-slate-900">{{ staff.department }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500">Joined</p>
                                    <p class="font-semibold text-slate-900">{{ staff.joiningDate }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500">Phone</p>
                                    <p class="font-semibold text-slate-900">{{ staff.phone }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Leaves -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="mb-4 flex items-center justify-between">
                                <h2 class="text-lg font-bold text-slate-900">Leave Requests</h2>
                                <button @click="openModal('leave')" class="text-sm font-semibold text-blue-600">
                                    Apply
                                </button>
                            </div>

                            <div class="space-y-3">
                                <div
                                    v-for="leave in leaves"
                                    :key="leave.type + leave.from"
                                    class="rounded-2xl bg-slate-50 p-4"
                                >
                                    <p class="font-semibold text-slate-900">{{ leave.type }}</p>
                                    <p class="text-sm text-slate-500">{{ leave.from }} - {{ leave.to }}</p>
                                    <p
                                        class="mt-1 text-xs font-bold"
                                        :class="leave.status === 'Approved'
                                            ? 'text-emerald-600'
                                            : 'text-yellow-600'"
                                    >
                                        {{ leave.status }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Documents -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-bold text-slate-900">Documents</h2>

                            <div class="mt-4 space-y-3">
                                <button
                                    v-for="document in documents"
                                    :key="document.name"
                                    @click="openModal('document', document)"
                                    class="w-full rounded-2xl bg-slate-50 p-4 text-left transition hover:bg-blue-50"
                                >
                                    <p class="font-semibold text-slate-900">{{ document.name }}</p>
                                    <p class="text-sm text-slate-500">{{ document.type }}</p>
                                </button>
                            </div>
                        </div>

                        <!-- Notices -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-bold text-slate-900">Staff Notices</h2>

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

                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div
                v-if="activeModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-6 backdrop-blur-sm"
            >
                <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-3xl bg-white shadow-2xl">
                    <div class="sticky top-0 z-10 flex items-start justify-between border-b border-slate-100 bg-white p-6">
                        <div>
                            <h2 class="text-xl font-bold text-slate-900">
                                <span v-if="activeModal === 'salary'">Salary Slip</span>
                                <span v-else-if="activeModal === 'leave'">Apply Leave</span>
                                <span v-else-if="activeModal === 'attendance'">Attendance Records</span>
                                <span v-else-if="activeModal === 'task'">My Tasks</span>
                                <span v-else-if="activeModal === 'document'">Documents</span>
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">Dummy UI now, backend can be connected later</p>
                        </div>

                        <button @click="closeModal" class="rounded-full bg-slate-100 px-3 py-1 text-sm font-bold text-slate-600 hover:bg-slate-200">
                            ✕
                        </button>
                    </div>

                    <div class="p-6">
                        <div v-if="activeModal === 'salary'" class="space-y-4">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="rounded-2xl bg-blue-50 p-4">
                                    <p class="text-sm text-blue-600">Basic Salary</p>
                                    <p class="text-xl font-bold text-blue-900">{{ salary.basic }}</p>
                                </div>
                                <div class="rounded-2xl bg-indigo-50 p-4">
                                    <p class="text-sm text-indigo-600">Allowance</p>
                                    <p class="text-xl font-bold text-indigo-900">{{ salary.allowance }}</p>
                                </div>
                                <div class="rounded-2xl bg-red-50 p-4">
                                    <p class="text-sm text-red-600">Deduction</p>
                                    <p class="text-xl font-bold text-red-900">{{ salary.deduction }}</p>
                                </div>
                                <div class="rounded-2xl bg-emerald-50 p-4">
                                    <p class="text-sm text-emerald-600">Net Pay</p>
                                    <p class="text-xl font-bold text-emerald-900">{{ salary.net }}</p>
                                </div>
                            </div>
                        </div>

                        <div v-else-if="activeModal === 'leave'" class="space-y-4">
                            <select class="w-full rounded-2xl border-slate-200">
                                <option>Annual Leave</option>
                                <option>Sick Leave</option>
                                <option>Emergency Leave</option>
                            </select>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <input type="date" class="rounded-2xl border-slate-200">
                                <input type="date" class="rounded-2xl border-slate-200">
                            </div>
                            <textarea class="w-full rounded-2xl border-slate-200" rows="5" placeholder="Reason"></textarea>
                        </div>

                        <div v-else-if="activeModal === 'attendance'" class="space-y-3">
                            <div v-for="item in attendance" :key="item.date" class="rounded-2xl bg-slate-50 p-4">
                                <p class="font-semibold text-slate-900">{{ item.date }}</p>
                                <p class="text-sm text-slate-500">Check In: {{ item.checkIn }} · Check Out: {{ item.checkOut }}</p>
                                <p class="mt-1 text-xs font-bold text-blue-600">{{ item.status }}</p>
                            </div>
                        </div>

                        <div v-else-if="activeModal === 'task'" class="space-y-3">
                            <div v-for="task in tasks" :key="task.title" class="rounded-2xl bg-slate-50 p-4">
                                <p class="font-semibold text-slate-900">{{ task.title }}</p>
                                <p class="text-sm text-slate-500">Due: {{ task.due }}</p>
                            </div>
                        </div>

                        <div v-else-if="activeModal === 'document'" class="space-y-3">
                            <div v-if="selectedItem" class="rounded-2xl bg-blue-50 p-5">
                                <p class="font-bold text-slate-900">{{ selectedItem.name }}</p>
                                <p class="text-sm text-slate-600">{{ selectedItem.type }}</p>
                            </div>

                            <div v-else class="space-y-3">
                                <button
                                    v-for="document in documents"
                                    :key="document.name"
                                    class="w-full rounded-2xl bg-slate-50 p-4 text-left"
                                >
                                    <p class="font-semibold text-slate-900">{{ document.name }}</p>
                                    <p class="text-sm text-slate-500">{{ document.type }}</p>
                                </button>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            <button @click="closeModal" class="rounded-2xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">
                                Cancel
                            </button>

                            <button class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700">
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>