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

const teacher = {
    name: 'Sarah Wilson',
    email: 'teacher@example.com',
    phone: '+971 50 987 6543',
    subject: 'Science',
    classTeacher: 'Grade 8 - A',
    employeeId: 'TCH-204',
    avatar: 'SW',
    status: 'Active',
    department: 'Science Department',
    academicYear: '2025 - 2026',
};

const stats = [
    { label: 'Today Classes', value: '5', sub: 'Scheduled' },
    { label: 'Students', value: '148', sub: 'Assigned' },
    { label: 'Assignments', value: '7', sub: 'Active' },
    { label: 'Exams', value: '3', sub: 'Upcoming' },
];

const todayClasses = [
    { class: 'Grade 8 - A', subject: 'Science', time: '08:00 - 09:00', room: 'Lab 2', students: 32 },
    { class: 'Grade 9 - B', subject: 'Science', time: '10:00 - 11:00', room: 'Room 204', students: 30 },
    { class: 'Grade 7 - C', subject: 'Science', time: '12:00 - 01:00', room: 'Lab 1', students: 31 },
];

const subjects = [
    { name: 'Science', class: 'Grade 8 - A', students: 32 },
    { name: 'Science', class: 'Grade 9 - B', students: 30 },
    { name: 'Biology', class: 'Grade 10 - A', students: 28 },
];

const exams = [
    { title: 'Unit Test 1', class: 'Grade 8 - A', subject: 'Science', date: '12 May 2026', totalMarks: 100, status: 'Scheduled' },
    { title: 'Practical Test', class: 'Grade 9 - B', subject: 'Science', date: '18 May 2026', totalMarks: 50, status: 'Draft' },
    { title: 'Semester Exam', class: 'Grade 10 - A', subject: 'Biology', date: '05 June 2026', totalMarks: 100, status: 'Scheduled' },
];

const assignments = [
    { title: 'Science Lab Report', class: 'Grade 8 - A', due: '10 May 2026', submitted: 24, total: 32 },
    { title: 'Chapter 4 Worksheet', class: 'Grade 9 - B', due: '14 May 2026', submitted: 18, total: 30 },
    { title: 'Biology Diagram Task', class: 'Grade 10 - A', due: '20 May 2026', submitted: 12, total: 28 },
];

const students = [
    { name: 'Ahmed Khan', rollNo: 'STU-1024', class: 'Grade 8 - A', status: 'Present', marks: 88 },
    { name: 'Sara Ali', rollNo: 'STU-1025', class: 'Grade 8 - A', status: 'Absent', marks: 79 },
    { name: 'Omar Hassan', rollNo: 'STU-1026', class: 'Grade 8 - A', status: 'Present', marks: 91 },
];

const tasks = [
    { title: 'Upload Unit Test marks', priority: 'High' },
    { title: 'Review lab reports', priority: 'Medium' },
    { title: 'Prepare Semester question paper', priority: 'High' },
];

const notices = [
    { title: 'Staff meeting at 2 PM', date: '06 May 2026', type: 'Meeting' },
    { title: 'Submit exam papers before Friday', date: '08 May 2026', type: 'Exam' },
];
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-50 px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl space-y-6">

                <!-- Header -->
                <div class="flex flex-col gap-4 rounded-3xl bg-gradient-to-r from-indigo-600 via-blue-600 to-sky-500 p-6 text-white shadow-lg sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-100">Teacher Portal</p>
                        <h1 class="mt-1 text-2xl font-bold sm:text-3xl">
                            Welcome, {{ teacher.name }}
                        </h1>
                        <p class="mt-2 text-sm text-blue-100">
                            {{ teacher.department }} · {{ teacher.subject }} · Academic Year {{ teacher.academicYear }}
                        </p>
                    </div>

                    <div class="flex items-center gap-4 rounded-2xl bg-white/15 p-4 backdrop-blur">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-xl font-bold text-indigo-600">
                            {{ teacher.avatar }}
                        </div>
                        <div>
                            <p class="text-sm text-blue-100">Status</p>
                            <p class="font-semibold">{{ teacher.status }}</p>
                            <p class="text-sm text-blue-100">ID: {{ teacher.employeeId }}</p>
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
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-5">
                        <h2 class="text-lg font-bold text-slate-900">Quick Actions</h2>
                        <p class="text-sm text-slate-500">Manage exams, assignments, attendance and marks</p>
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <button
                            @click="openModal('createExam')"
                            class="rounded-2xl bg-blue-600 px-4 py-4 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-1 hover:bg-blue-700 hover:shadow-md"
                        >
                            + Create Exam
                        </button>

                        <button
                            @click="openModal('createAssignment')"
                            class="rounded-2xl bg-indigo-600 px-4 py-4 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-1 hover:bg-indigo-700 hover:shadow-md"
                        >
                            + Add Assignment
                        </button>

                        <button
                            @click="openModal('attendance')"
                            class="rounded-2xl bg-sky-600 px-4 py-4 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-1 hover:bg-sky-700 hover:shadow-md"
                        >
                            Mark Attendance
                        </button>

                        <button
                            @click="openModal('marks')"
                            class="rounded-2xl bg-slate-900 px-4 py-4 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-1 hover:bg-slate-800 hover:shadow-md"
                        >
                            Upload Marks
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                    <!-- Main Content -->
                    <div class="space-y-6 xl:col-span-2">

                        <!-- Today Classes -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h2 class="text-lg font-bold text-slate-900">Today Classes</h2>
                                    <p class="text-sm text-slate-500">Your scheduled classes for today</p>
                                </div>
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600">
                                    Today
                                </span>
                            </div>

                            <div class="space-y-3">
                                <button
                                    v-for="item in todayClasses"
                                    :key="item.class + item.time"
                                    @click="openModal('classDetails', item)"
                                    class="w-full rounded-2xl border border-slate-100 bg-slate-50 p-4 text-left transition hover:border-blue-200 hover:bg-blue-50 hover:shadow-sm"
                                >
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ item.subject }} - {{ item.class }}</p>
                                            <p class="text-sm text-slate-500">{{ item.room }} · {{ item.students }} students</p>
                                        </div>
                                        <div class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm">
                                            {{ item.time }}
                                        </div>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Exams -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h2 class="text-lg font-bold text-slate-900">Exams & Tests</h2>
                                    <p class="text-sm text-slate-500">Create and manage unit tests, practicals and semester exams</p>
                                </div>
                                <button
                                    @click="openModal('createExam')"
                                    class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                                >
                                    New
                                </button>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <button
                                    v-for="exam in exams"
                                    :key="exam.title"
                                    @click="openModal('examDetails', exam)"
                                    class="rounded-2xl border border-slate-200 bg-slate-50 p-5 text-left transition hover:-translate-y-1 hover:border-blue-300 hover:bg-blue-50 hover:shadow-md"
                                >
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <h3 class="font-bold text-slate-900">{{ exam.title }}</h3>
                                            <p class="mt-1 text-sm text-slate-500">{{ exam.class }} · {{ exam.subject }}</p>
                                        </div>

                                        <span
                                            class="rounded-full px-3 py-1 text-xs font-semibold"
                                            :class="exam.status === 'Draft'
                                                ? 'bg-yellow-50 text-yellow-600'
                                                : 'bg-emerald-50 text-emerald-600'"
                                        >
                                            {{ exam.status }}
                                        </span>
                                    </div>

                                    <div class="mt-5 flex items-center justify-between">
                                        <p class="text-sm text-slate-500">{{ exam.date }}</p>
                                        <p class="text-sm font-bold text-slate-900">{{ exam.totalMarks }} Marks</p>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Assignments -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h2 class="text-lg font-bold text-slate-900">Assignments</h2>
                                    <p class="text-sm text-slate-500">Track assignment submissions</p>
                                </div>
                                <button
                                    @click="openModal('createAssignment')"
                                    class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                                >
                                    New
                                </button>
                            </div>

                            <div class="space-y-4">
                                <button
                                    v-for="assignment in assignments"
                                    :key="assignment.title"
                                    @click="openModal('assignmentDetails', assignment)"
                                    class="w-full rounded-2xl border border-slate-100 bg-slate-50 p-4 text-left transition hover:border-indigo-200 hover:bg-indigo-50 hover:shadow-sm"
                                >
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ assignment.title }}</p>
                                            <p class="text-sm text-slate-500">{{ assignment.class }} · Due {{ assignment.due }}</p>
                                        </div>
                                        <p class="text-sm font-semibold text-slate-700">
                                            {{ assignment.submitted }}/{{ assignment.total }} submitted
                                        </p>
                                    </div>

                                    <div class="mt-4 h-3 rounded-full bg-white">
                                        <div
                                            class="h-3 rounded-full bg-indigo-500"
                                            :style="{ width: `${(assignment.submitted / assignment.total) * 100}%` }"
                                        ></div>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Students -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="mb-5">
                                <h2 class="text-lg font-bold text-slate-900">Class Students</h2>
                                <p class="text-sm text-slate-500">Attendance and marks overview</p>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full min-w-[650px] text-left text-sm">
                                    <thead>
                                        <tr class="border-b bg-slate-50 text-slate-500">
                                            <th class="rounded-l-xl px-4 py-3 font-semibold">Student</th>
                                            <th class="px-4 py-3 font-semibold">Roll No</th>
                                            <th class="px-4 py-3 font-semibold">Class</th>
                                            <th class="px-4 py-3 font-semibold">Marks</th>
                                            <th class="rounded-r-xl px-4 py-3 font-semibold">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="student in students"
                                            :key="student.rollNo"
                                            class="border-b last:border-0"
                                        >
                                            <td class="px-4 py-4 font-medium text-slate-900">{{ student.name }}</td>
                                            <td class="px-4 py-4 text-slate-600">{{ student.rollNo }}</td>
                                            <td class="px-4 py-4 text-slate-600">{{ student.class }}</td>
                                            <td class="px-4 py-4 font-semibold text-slate-700">{{ student.marks }}/100</td>
                                            <td class="px-4 py-4">
                                                <span
                                                    class="rounded-full px-3 py-1 text-xs font-bold"
                                                    :class="student.status === 'Present'
                                                        ? 'bg-emerald-50 text-emerald-600'
                                                        : 'bg-red-50 text-red-600'"
                                                >
                                                    {{ student.status }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">

                        <!-- Teacher Info -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-bold text-slate-900">Teacher Info</h2>
                            <div class="mt-4 space-y-3">
                                <div>
                                    <p class="text-sm text-slate-500">Name</p>
                                    <p class="font-semibold text-slate-900">{{ teacher.name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500">Email</p>
                                    <p class="break-all font-semibold text-slate-900">{{ teacher.email }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500">Phone</p>
                                    <p class="font-semibold text-slate-900">{{ teacher.phone }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Subjects -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-bold text-slate-900">My Subjects</h2>
                            <div class="mt-4 space-y-3">
                                <div
                                    v-for="subject in subjects"
                                    :key="subject.name + subject.class"
                                    class="rounded-2xl bg-slate-50 p-4"
                                >
                                    <p class="font-semibold text-slate-900">{{ subject.name }}</p>
                                    <p class="text-sm text-slate-500">{{ subject.class }}</p>
                                    <p class="mt-1 text-xs font-semibold text-blue-600">{{ subject.students }} students</p>
                                </div>
                            </div>
                        </div>

                        <!-- Tasks -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-bold text-slate-900">Pending Tasks</h2>
                            <div class="mt-4 space-y-3">
                                <div
                                    v-for="task in tasks"
                                    :key="task.title"
                                    class="rounded-2xl border p-4"
                                    :class="task.priority === 'High'
                                        ? 'border-red-100 bg-red-50'
                                        : 'border-yellow-100 bg-yellow-50'"
                                >
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="font-semibold text-slate-900">{{ task.title }}</p>
                                        <span
                                            class="rounded-full px-2 py-1 text-xs font-bold"
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
                                <span v-if="activeModal === 'createExam'">Create Exam / Test</span>
                                <span v-else-if="activeModal === 'createAssignment'">Create Assignment</span>
                                <span v-else-if="activeModal === 'attendance'">Mark Attendance</span>
                                <span v-else-if="activeModal === 'marks'">Upload Marks</span>
                                <span v-else-if="activeModal === 'classDetails'">Class Details</span>
                                <span v-else-if="activeModal === 'examDetails'">Exam Details</span>
                                <span v-else-if="activeModal === 'assignmentDetails'">Assignment Details</span>
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">Dummy UI now, backend can be connected later</p>
                        </div>

                        <button
                            type="button"
                            @click="closeModal"
                            class="rounded-full bg-slate-100 px-3 py-1 text-sm font-bold text-slate-600 hover:bg-slate-200"
                        >
                            ✕
                        </button>
                    </div>

                    <div class="p-6">
                        <!-- Create Exam -->
                        <div v-if="activeModal === 'createExam'" class="space-y-4">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <input class="rounded-2xl border-slate-200" placeholder="Exam title">
                                <select class="rounded-2xl border-slate-200">
                                    <option>Unit Test</option>
                                    <option>Semester Exam</option>
                                    <option>Annual Exam</option>
                                    <option>Practical Test</option>
                                </select>
                                <select class="rounded-2xl border-slate-200">
                                    <option>Grade 8 - A</option>
                                    <option>Grade 9 - B</option>
                                    <option>Grade 10 - A</option>
                                </select>
                                <input type="date" class="rounded-2xl border-slate-200">
                                <input class="rounded-2xl border-slate-200" placeholder="Total marks">
                                <input class="rounded-2xl border-slate-200" placeholder="Pass marks">
                            </div>
                            <textarea class="w-full rounded-2xl border-slate-200" rows="4" placeholder="Exam instructions"></textarea>
                        </div>

                        <!-- Create Assignment -->
                        <div v-if="activeModal === 'createAssignment'" class="space-y-4">
                            <input class="w-full rounded-2xl border-slate-200" placeholder="Assignment title">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <select class="rounded-2xl border-slate-200">
                                    <option>Grade 8 - A</option>
                                    <option>Grade 9 - B</option>
                                    <option>Grade 10 - A</option>
                                </select>
                                <input type="date" class="rounded-2xl border-slate-200">
                            </div>
                            <textarea class="w-full rounded-2xl border-slate-200" rows="5" placeholder="Assignment description"></textarea>
                            <input type="file" class="w-full rounded-2xl border border-slate-200 p-3">
                        </div>

                        <!-- Attendance -->
                        <div v-if="activeModal === 'attendance'" class="space-y-4">
                            <select class="w-full rounded-2xl border-slate-200">
                                <option>Grade 8 - A</option>
                                <option>Grade 9 - B</option>
                                <option>Grade 10 - A</option>
                            </select>

                            <div class="space-y-3">
                                <div
                                    v-for="student in students"
                                    :key="student.rollNo"
                                    class="flex items-center justify-between rounded-2xl bg-slate-50 p-4"
                                >
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ student.name }}</p>
                                        <p class="text-sm text-slate-500">{{ student.rollNo }}</p>
                                    </div>
                                    <select class="rounded-xl border-slate-200 text-sm">
                                        <option>Present</option>
                                        <option>Absent</option>
                                        <option>Late</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Marks -->
                        <div v-if="activeModal === 'marks'" class="space-y-4">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <select class="rounded-2xl border-slate-200">
                                    <option>Unit Test 1</option>
                                    <option>Practical Test</option>
                                    <option>Semester Exam</option>
                                </select>
                                <select class="rounded-2xl border-slate-200">
                                    <option>Grade 8 - A</option>
                                    <option>Grade 9 - B</option>
                                </select>
                            </div>

                            <div class="space-y-3">
                                <div
                                    v-for="student in students"
                                    :key="student.rollNo"
                                    class="grid grid-cols-1 gap-3 rounded-2xl bg-slate-50 p-4 sm:grid-cols-3 sm:items-center"
                                >
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ student.name }}</p>
                                        <p class="text-sm text-slate-500">{{ student.rollNo }}</p>
                                    </div>
                                    <input class="rounded-xl border-slate-200" placeholder="Marks">
                                    <input class="rounded-xl border-slate-200" placeholder="Grade">
                                </div>
                            </div>
                        </div>

                        <!-- Details -->
                        <div
                            v-if="['classDetails', 'examDetails', 'assignmentDetails'].includes(activeModal)"
                            class="space-y-4"
                        >
                            <div class="rounded-2xl bg-blue-50 p-5">
                                <pre class="whitespace-pre-wrap text-sm text-slate-700">{{ selectedItem }}</pre>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            <button
                                type="button"
                                @click="closeModal"
                                class="rounded-2xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50"
                            >
                                Cancel
                            </button>

                            <button
                                type="button"
                                class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700"
                            >
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>