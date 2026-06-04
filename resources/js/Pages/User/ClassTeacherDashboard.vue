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

const classTeacher = {
    name: 'Sarah Wilson',
    avatar: 'SW',
    employeeId: 'TCH-204',
    assignedClass: 'Grade 8 - A',
    department: 'Science Department',
    academicYear: '2025 - 2026',
    status: 'Active',
};

const stats = [
    { label: 'Total Students', value: '32', sub: 'Grade 8 - A' },
    { label: 'Present Today', value: '29', sub: '3 absent' },
    { label: 'Subject Teachers', value: '6', sub: 'Assigned' },
    { label: 'Pending Actions', value: '5', sub: 'Today' },
];

const students = [
    { name: 'Ahmed Khan', rollNo: 'STU-1024', attendance: 'Present', performance: 'A', parent: 'Mr. Khan' },
    { name: 'Sara Ali', rollNo: 'STU-1025', attendance: 'Absent', performance: 'B+', parent: 'Mrs. Ali' },
    { name: 'Omar Hassan', rollNo: 'STU-1026', attendance: 'Present', performance: 'A+', parent: 'Mr. Hassan' },
];

const subjectTeachers = [
    { subject: 'Mathematics', teacher: 'Mr. David', status: 'Assigned' },
    { subject: 'Science', teacher: 'Ms. Sarah', status: 'Assigned' },
    { subject: 'English', teacher: 'Ms. Emma', status: 'Assigned' },
    { subject: 'Computer', teacher: 'Mr. Ali', status: 'Assigned' },
];

const timetable = [
    { time: '08:00 - 09:00', subject: 'Mathematics', teacher: 'Mr. David', room: 'Room 201' },
    { time: '09:00 - 10:00', subject: 'Science', teacher: 'Ms. Sarah', room: 'Lab 2' },
    { time: '10:30 - 11:30', subject: 'English', teacher: 'Ms. Emma', room: 'Room 105' },
    { time: '11:30 - 12:30', subject: 'Computer', teacher: 'Mr. Ali', room: 'ICT Lab' },
];

const exams = [
    { title: 'Unit Test 1', subject: 'Science', date: '12 May 2026', totalMarks: 100, status: 'Scheduled' },
    { title: 'Math Quiz', subject: 'Mathematics', date: '16 May 2026', totalMarks: 50, status: 'Draft' },
    { title: 'Semester Exam', subject: 'All Subjects', date: '05 June 2026', totalMarks: 100, status: 'Scheduled' },
];

const assignments = [
    { title: 'Science Lab Report', subject: 'Science', due: '10 May 2026', submitted: 24, total: 32 },
    { title: 'Algebra Worksheet', subject: 'Mathematics', due: '14 May 2026', submitted: 21, total: 32 },
    { title: 'English Essay', subject: 'English', due: '18 May 2026', submitted: 18, total: 32 },
];

const issues = [
    { student: 'Sara Ali', issue: 'Absent today', type: 'Attendance' },
    { student: 'Ahmed Khan', issue: 'Pending assignment', type: 'Assignment' },
    { student: 'Omar Hassan', issue: 'Excellent performance', type: 'Remark' },
];

const notices = [
    { title: 'Parent meeting scheduled', date: '10 May 2026', type: 'Meeting' },
    { title: 'Submit class attendance before 10 AM', date: 'Daily', type: 'Attendance' },
];
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-50 px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl space-y-6">

                <!-- Header -->
                <div class="flex flex-col gap-4 rounded-3xl bg-gradient-to-r from-indigo-600 via-blue-600 to-sky-500 p-6 text-white shadow-lg sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-100">Class Teacher Portal</p>
                        <h1 class="mt-1 text-2xl font-bold sm:text-3xl">
                            {{ classTeacher.assignedClass }}
                        </h1>
                        <p class="mt-2 text-sm text-blue-100">
                            Class Teacher: {{ classTeacher.name }} · Academic Year {{ classTeacher.academicYear }}
                        </p>
                    </div>

                    <div class="flex items-center gap-4 rounded-2xl bg-white/15 p-4 backdrop-blur">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-xl font-bold text-indigo-600">
                            {{ classTeacher.avatar }}
                        </div>
                        <div>
                            <p class="text-sm text-blue-100">Status</p>
                            <p class="font-semibold">{{ classTeacher.status }}</p>
                            <p class="text-sm text-blue-100">ID: {{ classTeacher.employeeId }}</p>
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
            <h2 class="text-lg font-bold text-slate-900">Class Teacher Actions</h2>
            <p class="text-sm text-slate-500">
                Manage daily class operations quickly
            </p>
        </div>

        <span class="w-fit rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600">
            Grade 8 - A
        </span>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <button
            type="button"
            @click="openModal('attendance')"
            class="group rounded-2xl border border-blue-100 bg-blue-50 p-4 text-left transition hover:-translate-y-1 hover:border-blue-300 hover:bg-blue-100 hover:shadow-md"
        >
            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-lg font-bold text-white shadow-sm">
                A
            </div>
            <h3 class="font-bold text-slate-900">Mark Attendance</h3>
            <p class="mt-1 text-sm text-slate-500">Update today’s student attendance</p>
        </button>

        <button
            type="button"
            @click="openModal('exam')"
            class="group rounded-2xl border border-indigo-100 bg-indigo-50 p-4 text-left transition hover:-translate-y-1 hover:border-indigo-300 hover:bg-indigo-100 hover:shadow-md"
        >
            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-600 text-lg font-bold text-white shadow-sm">
                E
            </div>
            <h3 class="font-bold text-slate-900">Create Exam</h3>
            <p class="mt-1 text-sm text-slate-500">Schedule unit, semester or annual exams</p>
        </button>

        <button
            type="button"
            @click="openModal('assignment')"
            class="group rounded-2xl border border-sky-100 bg-sky-50 p-4 text-left transition hover:-translate-y-1 hover:border-sky-300 hover:bg-sky-100 hover:shadow-md"
        >
            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-sky-600 text-lg font-bold text-white shadow-sm">
                W
            </div>
            <h3 class="font-bold text-slate-900">Add Assignment</h3>
            <p class="mt-1 text-sm text-slate-500">Create homework or project tasks</p>
        </button>

        <button
            type="button"
            @click="openModal('notice')"
            class="group rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left transition hover:-translate-y-1 hover:border-slate-400 hover:bg-slate-100 hover:shadow-md"
        >
            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-slate-900 text-lg font-bold text-white shadow-sm">
                N
            </div>
            <h3 class="font-bold text-slate-900">Class Notice</h3>
            <p class="mt-1 text-sm text-slate-500">Send updates to students or parents</p>
        </button>

        <button
            type="button"
            @click="openModal('remark')"
            class="group rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-left transition hover:-translate-y-1 hover:border-emerald-300 hover:bg-emerald-100 hover:shadow-md"
        >
            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-600 text-lg font-bold text-white shadow-sm">
                R
            </div>
            <h3 class="font-bold text-slate-900">Add Remark</h3>
            <p class="mt-1 text-sm text-slate-500">Record academic or behavior notes</p>
        </button>
    </div>
</div>

                <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

                    <!-- Main -->
                    <div class="space-y-6 xl:col-span-2">

                        <!-- Students -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h2 class="text-lg font-bold text-slate-900">Class Students</h2>
                                    <p class="text-sm text-slate-500">Attendance, parent and performance overview</p>
                                </div>
                                <button @click="openModal('student')" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                    View All
                                </button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full min-w-[700px] text-left text-sm">
                                    <thead>
                                        <tr class="border-b bg-slate-50 text-slate-500">
                                            <th class="rounded-l-xl px-4 py-3 font-semibold">Student</th>
                                            <th class="px-4 py-3 font-semibold">Roll No</th>
                                            <th class="px-4 py-3 font-semibold">Attendance</th>
                                            <th class="px-4 py-3 font-semibold">Performance</th>
                                            <th class="rounded-r-xl px-4 py-3 font-semibold">Parent</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="student in students" :key="student.rollNo" class="border-b last:border-0">
                                            <td class="px-4 py-4 font-medium text-slate-900">{{ student.name }}</td>
                                            <td class="px-4 py-4 text-slate-600">{{ student.rollNo }}</td>
                                            <td class="px-4 py-4">
                                                <span
                                                    class="rounded-full px-3 py-1 text-xs font-bold"
                                                    :class="student.attendance === 'Present'
                                                        ? 'bg-emerald-50 text-emerald-600'
                                                        : 'bg-red-50 text-red-600'"
                                                >
                                                    {{ student.attendance }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 font-bold text-slate-700">{{ student.performance }}</td>
                                            <td class="px-4 py-4 text-slate-600">{{ student.parent }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Timetable -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="mb-5">
                                <h2 class="text-lg font-bold text-slate-900">Class Timetable</h2>
                                <p class="text-sm text-slate-500">Today schedule with subject teachers</p>
                            </div>

                            <div class="space-y-3">
                                <div
                                    v-for="item in timetable"
                                    :key="item.time"
                                    class="flex flex-col gap-3 rounded-2xl border border-slate-100 bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between"
                                >
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ item.subject }}</p>
                                        <p class="text-sm text-slate-500">{{ item.teacher }} · {{ item.room }}</p>
                                    </div>
                                    <div class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm">
                                        {{ item.time }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Exams -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h2 class="text-lg font-bold text-slate-900">Class Exams</h2>
                                    <p class="text-sm text-slate-500">Manage exams and tests for this class</p>
                                </div>
                                <button @click="openModal('exam')" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                    New
                                </button>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <button
                                    v-for="exam in exams"
                                    :key="exam.title"
                                    @click="openModal('examDetails', exam)"
                                    class="rounded-2xl border border-slate-200 bg-slate-50 p-5 text-left transition hover:-translate-y-1 hover:border-indigo-300 hover:bg-indigo-50 hover:shadow-md"
                                >
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <h3 class="font-bold text-slate-900">{{ exam.title }}</h3>
                                            <p class="mt-1 text-sm text-slate-500">{{ exam.subject }}</p>
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
                                    <h2 class="text-lg font-bold text-slate-900">Class Assignments</h2>
                                    <p class="text-sm text-slate-500">Track assignment submissions</p>
                                </div>
                                <button @click="openModal('assignment')" class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">
                                    New
                                </button>
                            </div>

                            <div class="space-y-4">
                                <button
                                    v-for="assignment in assignments"
                                    :key="assignment.title"
                                    @click="openModal('assignmentDetails', assignment)"
                                    class="w-full rounded-2xl border border-slate-100 bg-slate-50 p-4 text-left transition hover:border-sky-200 hover:bg-sky-50"
                                >
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ assignment.title }}</p>
                                            <p class="text-sm text-slate-500">{{ assignment.subject }} · Due {{ assignment.due }}</p>
                                        </div>
                                        <p class="text-sm font-semibold text-slate-700">
                                            {{ assignment.submitted }}/{{ assignment.total }} submitted
                                        </p>
                                    </div>

                                    <div class="mt-4 h-3 rounded-full bg-white">
                                        <div
                                            class="h-3 rounded-full bg-sky-500"
                                            :style="{ width: `${(assignment.submitted / assignment.total) * 100}%` }"
                                        ></div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">

                        <!-- Subject Teachers -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="mb-4 flex items-center justify-between">
                                <h2 class="text-lg font-bold text-slate-900">Subject Teachers</h2>
                                <button @click="openModal('subjectTeacher')" class="text-sm font-semibold text-blue-600">
                                    Manage
                                </button>
                            </div>

                            <div class="space-y-3">
                                <div
                                    v-for="item in subjectTeachers"
                                    :key="item.subject"
                                    class="rounded-2xl bg-slate-50 p-4"
                                >
                                    <p class="font-semibold text-slate-900">{{ item.subject }}</p>
                                    <p class="text-sm text-slate-500">{{ item.teacher }}</p>
                                    <p class="mt-1 text-xs font-semibold text-blue-600">{{ item.status }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Issues -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-bold text-slate-900">Student Notes</h2>

                            <div class="mt-4 space-y-3">
                                <div
                                    v-for="issue in issues"
                                    :key="issue.student + issue.issue"
                                    class="rounded-2xl border p-4"
                                    :class="issue.type === 'Attendance'
                                        ? 'border-red-100 bg-red-50'
                                        : issue.type === 'Assignment'
                                            ? 'border-yellow-100 bg-yellow-50'
                                            : 'border-emerald-100 bg-emerald-50'"
                                >
                                    <p class="font-semibold text-slate-900">{{ issue.student }}</p>
                                    <p class="text-sm text-slate-600">{{ issue.issue }}</p>
                                    <p class="mt-1 text-xs font-bold">{{ issue.type }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Notices -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-bold text-slate-900">Class Notices</h2>

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
                                <span v-if="activeModal === 'attendance'">Mark Class Attendance</span>
                                <span v-else-if="activeModal === 'exam'">Create Class Exam</span>
                                <span v-else-if="activeModal === 'assignment'">Create Assignment</span>
                                <span v-else-if="activeModal === 'notice'">Create Class Notice</span>
                                <span v-else-if="activeModal === 'remark'">Add Student Remark</span>
                                <span v-else-if="activeModal === 'subjectTeacher'">Manage Subject Teachers</span>
                                <span v-else>Details</span>
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

                        <!-- Attendance -->
                        <div v-if="activeModal === 'attendance'" class="space-y-4">
                            <input type="date" class="w-full rounded-2xl border-slate-200">

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
                                        <option>Leave</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Exam -->
                        <div v-else-if="activeModal === 'exam'" class="space-y-4">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <input class="rounded-2xl border-slate-200" placeholder="Exam title">
                                <select class="rounded-2xl border-slate-200">
                                    <option>Unit Test</option>
                                    <option>Semester Exam</option>
                                    <option>Annual Exam</option>
                                </select>
                                <select class="rounded-2xl border-slate-200">
                                    <option>All Subjects</option>
                                    <option>Mathematics</option>
                                    <option>Science</option>
                                    <option>English</option>
                                </select>
                                <input type="date" class="rounded-2xl border-slate-200">
                                <input class="rounded-2xl border-slate-200" placeholder="Total marks">
                                <input class="rounded-2xl border-slate-200" placeholder="Pass marks">
                            </div>
                            <textarea class="w-full rounded-2xl border-slate-200" rows="4" placeholder="Exam instructions"></textarea>
                        </div>

                        <!-- Assignment -->
                        <div v-else-if="activeModal === 'assignment'" class="space-y-4">
                            <input class="w-full rounded-2xl border-slate-200" placeholder="Assignment title">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <select class="rounded-2xl border-slate-200">
                                    <option>Mathematics</option>
                                    <option>Science</option>
                                    <option>English</option>
                                </select>
                                <input type="date" class="rounded-2xl border-slate-200">
                            </div>
                            <textarea class="w-full rounded-2xl border-slate-200" rows="5" placeholder="Assignment description"></textarea>
                            <input type="file" class="w-full rounded-2xl border border-slate-200 p-3">
                        </div>

                        <!-- Notice -->
                        <div v-else-if="activeModal === 'notice'" class="space-y-4">
                            <input class="w-full rounded-2xl border-slate-200" placeholder="Notice title">
                            <select class="w-full rounded-2xl border-slate-200">
                                <option>Students</option>
                                <option>Parents</option>
                                <option>Subject Teachers</option>
                                <option>All</option>
                            </select>
                            <textarea class="w-full rounded-2xl border-slate-200" rows="5" placeholder="Notice message"></textarea>
                        </div>

                        <!-- Remark -->
                        <div v-else-if="activeModal === 'remark'" class="space-y-4">
                            <select class="w-full rounded-2xl border-slate-200">
                                <option>Ahmed Khan</option>
                                <option>Sara Ali</option>
                                <option>Omar Hassan</option>
                            </select>
                            <select class="w-full rounded-2xl border-slate-200">
                                <option>Academic</option>
                                <option>Attendance</option>
                                <option>Discipline</option>
                                <option>Positive Remark</option>
                            </select>
                            <textarea class="w-full rounded-2xl border-slate-200" rows="5" placeholder="Write remark"></textarea>
                        </div>

                        <!-- Subject Teachers -->
                        <div v-else-if="activeModal === 'subjectTeacher'" class="space-y-4">
                            <div
                                v-for="item in subjectTeachers"
                                :key="item.subject"
                                class="grid grid-cols-1 gap-3 rounded-2xl bg-slate-50 p-4 sm:grid-cols-3 sm:items-center"
                            >
                                <p class="font-semibold text-slate-900">{{ item.subject }}</p>
                                <input class="rounded-xl border-slate-200" :value="item.teacher">
                                <select class="rounded-xl border-slate-200">
                                    <option>Assigned</option>
                                    <option>Pending</option>
                                </select>
                            </div>
                        </div>

                        <!-- Details -->
                        <div v-else class="rounded-2xl bg-blue-50 p-5">
                            <pre class="whitespace-pre-wrap text-sm text-slate-700">{{ selectedItem }}</pre>
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