<script setup>
import { ref, computed } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

const props = defineProps({
    teachers: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
        }),
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            subject: '',
            assigned_class: '',
            status: '',
        }),
    },
    teacherStats: {
        type: Object,
        default: () => ({
            cards: [],
            overview: {},
        }),
    },
    subjects: {
        type: Array,
        default: () => [],
    },
    assignedClasses: {
        type: Array,
        default: () => [],
    },
})
//console these props to verify data
console.log('Teachers:', props.teachers)
console.log('Subjects:', props.subjects)
console.log('Assigned Classes:', props.assignedClasses)

const showCreateModal = ref(false)
const showViewModal = ref(false)
const showEditModal = ref(false)
const selectedTeacher = ref(null)

const defaultSubjects = ['Mathematics', 'English', 'Physics', 'Science', 'Computer Science']
const defaultClasses = ['Grade 6', 'Grade 8', 'Grade 10', 'Grade 11']

const subjectOptions = computed(() => [...new Set([...props.subjects, ...defaultSubjects])])
const classOptions = computed(() => [...new Set([...props.assignedClasses, ...defaultClasses])])

const filterForm = useForm({
    search: props.filters.search || '',
    subject: props.filters.subject || '',
    assigned_class: props.filters.assigned_class || '',
    status: props.filters.status || '',
})

const form = useForm({
    teacher_id: '',
    full_name: '',
    email: '',
    phone: '',
    gender: '',
    date_of_birth: '',
    subject: '',
    assigned_class: '',
    joining_date: '',
    status: 'Active',
    address: '',
})

const editForm = useForm({
    teacher_id: '',
    full_name: '',
    email: '',
    phone: '',
    gender: '',
    date_of_birth: '',
    subject: '',
    assigned_class: '',
    joining_date: '',
    status: 'Active',
    address: '',
})

const validateTeacher = (targetForm) => {
    const errors = {}

    if (!targetForm.teacher_id.trim()) errors.teacher_id = 'Teacher ID is required.'
    if (!targetForm.full_name.trim()) errors.full_name = 'Full name is required.'
    if (!targetForm.subject) errors.subject = 'Subject is required.'

    if (targetForm.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(targetForm.email)) {
        errors.email = 'Enter a valid email address.'
    }

    if (targetForm.date_of_birth) {
        const dob = new Date(targetForm.date_of_birth)
        const today = new Date()
        today.setHours(0, 0, 0, 0)

        if (dob >= today) errors.date_of_birth = 'Date of birth must be before today.'
    }

    if (targetForm.phone && targetForm.phone.length > 30) {
        errors.phone = 'Phone number is too long.'
    }

    if (targetForm.address && targetForm.address.length > 1000) {
        errors.address = 'Address must not be greater than 1000 characters.'
    }

    return errors
}

const frontendErrors = computed(() => validateTeacher(form))
const editFrontendErrors = computed(() => validateTeacher(editForm))

const canSubmit = computed(() => Object.keys(frontendErrors.value).length === 0)
const canUpdate = computed(() => Object.keys(editFrontendErrors.value).length === 0)

const fieldError = (errors, targetForm, field) => errors[field] || targetForm.errors[field]

const inputClass = (errors, targetForm, field) => {
    return fieldError(errors, targetForm, field)
        ? 'border-rose-300 bg-rose-50 focus:border-rose-400 focus:ring-rose-100'
        : 'border-slate-200 bg-white focus:border-indigo-400 focus:ring-indigo-100'
}

const resetForm = () => {
    form.reset()
    form.status = 'Active'
    form.clearErrors()
}

const openCreateModal = () => {
    form.clearErrors()
    showCreateModal.value = true
}

const closeCreateModal = () => {
    form.clearErrors()
    showCreateModal.value = false
}

const submitTeacher = () => {
    if (!canSubmit.value || form.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    form.post(route('teachers.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Teacher created successfully.')
            resetForm()
            showCreateModal.value = false
        },
        onError: () => toast.error('Please check the form errors.'),
    })
}

const openViewModal = (teacher) => {
    selectedTeacher.value = teacher
    showViewModal.value = true
}

const closeViewModal = () => {
    selectedTeacher.value = null
    showViewModal.value = false
}

const openEditModal = (teacher) => {
    selectedTeacher.value = teacher

    editForm.teacher_id = teacher.teacher_id || ''
    editForm.full_name = teacher.full_name || ''
    editForm.email = teacher.email || ''
    editForm.phone = teacher.phone || ''
    editForm.gender = teacher.gender || ''
    editForm.date_of_birth = teacher.date_of_birth || ''
    editForm.subject = teacher.subject || ''
    editForm.assigned_class = teacher.assigned_class || ''
    editForm.joining_date = teacher.joining_date || ''
    editForm.status = teacher.status || 'Active'
    editForm.address = teacher.address || ''

    editForm.clearErrors()
    showEditModal.value = true
}

const closeEditModal = () => {
    selectedTeacher.value = null
    editForm.clearErrors()
    showEditModal.value = false
}

const updateTeacher = () => {
    if (!selectedTeacher.value || !canUpdate.value || editForm.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    editForm.put(route('teachers.update', selectedTeacher.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Teacher updated successfully.')
            closeEditModal()
        },
        onError: () => toast.error('Please check the form errors.'),
    })
}

const deleteTeacher = (teacher) => {
    if (!confirm(`Delete ${teacher.full_name}?`)) return

    router.delete(route('teachers.destroy', teacher.id), {
        preserveScroll: true,
        onSuccess: () => toast.success('Teacher deleted successfully.'),
        onError: () => toast.error('Unable to delete teacher.'),
    })
}

const applyFilters = () => {
    router.get(route('teachers'), {
        search: filterForm.search,
        subject: filterForm.subject,
        assigned_class: filterForm.assigned_class,
        status: filterForm.status,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

const resetFilters = () => {
    filterForm.search = ''
    filterForm.subject = ''
    filterForm.assigned_class = ''
    filterForm.status = ''

    router.get(route('teachers'), {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}
</script>

<template>
    <AppLayout title="Teachers">
        <div class="space-y-6">
            <!-- Header -->
            <div class="rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 ring-1 ring-slate-200 sm:p-6 lg:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">Teacher Management</p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                            Teachers
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                            Manage teacher records, subject assignments, class responsibilities, contact details and staff status.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800"
                        @click="openCreateModal"
                    >
                        Add Teacher
                    </button>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div
                    v-for="(stat, index) in teacherStats.cards"
                    :key="stat.label"
                    class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                >
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-500">{{ stat.label }}</p>
                            <h2 class="mt-3 text-3xl font-black text-slate-950">{{ stat.value }}</h2>
                        </div>

                        <div
                            class="flex size-12 items-center justify-center rounded-2xl text-white shadow-lg"
                            :class="[
                                index === 0 ? 'bg-gradient-to-br from-indigo-500 to-sky-400' : '',
                                index === 1 ? 'bg-gradient-to-br from-emerald-500 to-teal-400' : '',
                                index === 2 ? 'bg-gradient-to-br from-amber-500 to-orange-400' : '',
                                index === 3 ? 'bg-gradient-to-br from-rose-500 to-pink-400' : '',
                            ]"
                        >
                            <span class="text-lg font-black">{{ index + 1 }}</span>
                        </div>
                    </div>

                    <p class="mt-4 text-xs font-semibold text-slate-500">
                        {{ stat.change }}
                    </p>
                </div>
            </div>

            <!-- Overview -->
            <div class="grid gap-6 xl:grid-cols-3">
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 xl:col-span-2">
                    <h2 class="text-lg font-black text-slate-950">Teaching Staff Overview</h2>
                    <p class="mt-1 text-sm text-slate-500">Live summary from teacher database.</p>

                    <div class="mt-6 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl bg-indigo-50 p-5">
                            <p class="text-xs font-semibold text-indigo-600">Departments</p>
                            <p class="mt-2 text-2xl font-black text-slate-950">
                                {{ teacherStats.overview.departments || 0 }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">Unique subjects</p>
                        </div>

                        <div class="rounded-2xl bg-emerald-50 p-5">
                            <p class="text-xs font-semibold text-emerald-600">Active</p>
                            <p class="mt-2 text-2xl font-black text-slate-950">
                                {{ teacherStats.overview.active || 0 }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">Currently teaching</p>
                        </div>

                        <div class="rounded-2xl bg-amber-50 p-5">
                            <p class="text-xs font-semibold text-amber-600">On Leave</p>
                            <p class="mt-2 text-2xl font-black text-slate-950">
                                {{ teacherStats.overview.on_leave || 0 }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">Temporarily away</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-gradient-to-br from-indigo-600 to-sky-500 p-6 text-white shadow-lg shadow-indigo-100">
                    <div class="flex items-center gap-4">
                        <div class="flex size-16 items-center justify-center rounded-3xl bg-white/20 text-2xl font-black ring-1 ring-white/30">
                            T
                        </div>

                        <div>
                            <h3 class="text-lg font-black">Staff Snapshot</h3>
                            <p class="text-sm text-white/75">Teacher status summary</p>
                        </div>
                    </div>

                    <div class="mt-8 space-y-4">
                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Active Staff</p>
                            <p class="mt-1 font-bold">{{ teacherStats.overview.active || 0 }} teachers</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Departments</p>
                            <p class="mt-1 font-bold">{{ teacherStats.overview.departments || 0 }} subjects</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Leave Status</p>
                            <p class="mt-1 font-bold">{{ teacherStats.overview.on_leave || 0 }} on leave</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <form class="grid gap-4 md:grid-cols-2 xl:grid-cols-6" @submit.prevent="applyFilters">
                    <div class="xl:col-span-2">
                        <label class="text-sm font-semibold text-slate-600">Search Teacher</label>
                        <input
                            v-model="filterForm.search"
                            type="text"
                            placeholder="Search by name, ID, email or phone"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        />
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Subject</label>
                        <select
                            v-model="filterForm.subject"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        >
                            <option value="">All Subjects</option>
                            <option v-for="subject in subjectOptions" :key="subject" :value="subject">
                                {{ subject }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Class</label>
                        <select
                            v-model="filterForm.assigned_class"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        >
                            <option value="">All Classes</option>
                            <option v-for="className in classOptions" :key="className" :value="className">
                                {{ className }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Status</label>
                        <select
                            v-model="filterForm.status"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        >
                            <option value="">All Status</option>
                            <option>Active</option>
                            <option>On Leave</option>
                            <option>Inactive</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button
                            type="submit"
                            class="w-full rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-100 transition hover:bg-indigo-700"
                        >
                            Filter
                        </button>

                        <button
                            type="button"
                            class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50"
                            @click="resetFilters"
                        >
                            Reset
                        </button>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">Teacher Records</h2>
                        <p class="mt-1 text-sm text-slate-500">Teacher list from database</p>
                    </div>

                    <button
                        type="button"
                        class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white hover:bg-slate-800"
                        @click="openCreateModal"
                    >
                        New Teacher
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1050px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Teacher</th>
                                <th class="px-6 py-4">Teacher ID</th>
                                <th class="px-6 py-4">Subject</th>
                                <th class="px-6 py-4">Class</th>
                                <th class="px-6 py-4">Phone</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="teacher in teachers.data"
                                :key="teacher.id"
                                class="transition hover:bg-indigo-50/40"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 to-sky-100 text-sm font-black text-indigo-700">
                                            {{ teacher.full_name?.charAt(0) || 'T' }}
                                        </div>

                                        <div>
                                            <p class="font-bold text-slate-950">{{ teacher.full_name }}</p>
                                            <p class="text-xs text-slate-400">{{ teacher.email || 'No email' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-700">
                                    {{ teacher.teacher_id }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ teacher.subject }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ teacher.assigned_class || '-' }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ teacher.phone || '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                                        :class="
                                            teacher.status === 'Active'
                                                ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                                : teacher.status === 'On Leave'
                                                    ? 'bg-amber-50 text-amber-700 ring-amber-100'
                                                    : 'bg-rose-50 text-rose-700 ring-rose-100'
                                        "
                                    >
                                        {{ teacher.status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            type="button"
                                            class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200"
                                            @click="openViewModal(teacher)"
                                        >
                                            View
                                        </button>

                                        <button
                                            type="button"
                                            class="rounded-xl bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100"
                                            @click="openEditModal(teacher)"
                                        >
                                            Edit
                                        </button>

                                        <button
                                            type="button"
                                            class="rounded-xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100"
                                            @click="deleteTeacher(teacher)"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="teachers.data.length === 0">
                                <td colspan="7" class="px-6 py-10 text-center text-sm font-bold text-slate-400">
                                    No teachers found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="teachers.links && teachers.links.length > 3"
                    class="flex flex-col gap-4 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <p class="text-sm font-semibold text-slate-500">
                        Showing
                        <span class="font-black text-slate-800">{{ teachers.from || 0 }}</span>
                        to
                        <span class="font-black text-slate-800">{{ teachers.to || 0 }}</span>
                        of
                        <span class="font-black text-slate-800">{{ teachers.total || 0 }}</span>
                        teachers
                    </p>

                    <div class="flex flex-wrap gap-2">
                        <Link
                            v-for="link in teachers.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            preserve-scroll
                            class="rounded-xl px-4 py-2 text-sm font-bold transition"
                            :class="[
                                link.active
                                    ? 'bg-slate-950 text-white'
                                    : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-100',
                                !link.url ? 'pointer-events-none cursor-not-allowed opacity-40' : ''
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>

            <!-- Create Modal -->
            <div
                v-if="showCreateModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
            >
                <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">Add New Teacher</h2>
                            <p class="mt-1 text-sm text-slate-500">Add teacher profile, subject assignment and contact details.</p>
                        </div>

                        <button
                            type="button"
                            class="rounded-2xl bg-white p-2 text-slate-400 shadow-sm ring-1 ring-slate-200 transition hover:text-slate-700"
                            @click="closeCreateModal"
                        >
                            ✕
                        </button>
                    </div>

                    <div class="max-h-[75vh] overflow-y-auto p-5 sm:p-6">
                        <p
                            v-if="form.errors.server"
                            class="mb-5 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-bold text-rose-600"
                        >
                            {{ form.errors.server }}
                        </p>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="text-sm font-semibold text-slate-600">Full Name *</label>
                                <input
                                    v-model="form.full_name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'full_name')"
                                    placeholder="Teacher full name"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'full_name')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'full_name') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Teacher ID *</label>
                                <input
                                    v-model="form.teacher_id"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'teacher_id')"
                                    placeholder="TCH-004"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'teacher_id')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'teacher_id') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Email</label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'email')"
                                    placeholder="teacher@example.com"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'email')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'email') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Phone</label>
                                <input
                                    v-model="form.phone"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'phone')"
                                    placeholder="+971 50 000 0000"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'phone')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'phone') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Gender</label>
                                <select
                                    v-model="form.gender"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option value="">Select gender</option>
                                    <option>Male</option>
                                    <option>Female</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Date of Birth</label>
                                <input
                                    v-model="form.date_of_birth"
                                    type="date"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'date_of_birth')"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'date_of_birth')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'date_of_birth') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Subject *</label>
                                <select
                                    v-model="form.subject"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'subject')"
                                >
                                    <option value="">Select subject</option>
                                    <option v-for="subject in subjectOptions" :key="subject" :value="subject">
                                        {{ subject }}
                                    </option>
                                </select>
                                <p v-if="fieldError(frontendErrors, form, 'subject')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'subject') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Assigned Class</label>
                                <select
                                    v-model="form.assigned_class"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option value="">Select class</option>
                                    <option v-for="className in classOptions" :key="className" :value="className">
                                        {{ className }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Joining Date</label>
                                <input
                                    v-model="form.joining_date"
                                    type="date"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status *</label>
                                <select
                                    v-model="form.status"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option>Active</option>
                                    <option>On Leave</option>
                                    <option>Inactive</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Address</label>
                                <textarea
                                    v-model="form.address"
                                    rows="3"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'address')"
                                    placeholder="Teacher address"
                                ></textarea>
                                <p v-if="fieldError(frontendErrors, form, 'address')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'address') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 p-5 sm:flex-row sm:justify-end sm:p-6">
                        <button
                            type="button"
                            class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                            @click="closeCreateModal"
                        >
                            Cancel
                        </button>

                        <button
                            type="button"
                            class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="form.processing || !canSubmit"
                            @click="submitTeacher"
                        >
                            {{ form.processing ? 'Saving...' : 'Save Teacher' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- View Modal -->
            <div
                v-if="showViewModal && selectedTeacher"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
            >
                <div class="w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div class="flex items-center gap-4">
                            <div class="flex size-14 items-center justify-center rounded-3xl bg-gradient-to-br from-indigo-500 to-sky-400 text-xl font-black text-white shadow-lg">
                                {{ selectedTeacher.full_name?.charAt(0) || 'T' }}
                            </div>

                            <div>
                                <h2 class="text-xl font-black text-slate-950">
                                    {{ selectedTeacher.full_name }}
                                </h2>
                                <p class="mt-1 text-sm font-semibold text-slate-500">
                                    {{ selectedTeacher.teacher_id }}
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            class="rounded-2xl bg-white p-2 text-slate-400 shadow-sm ring-1 ring-slate-200 transition hover:text-slate-700"
                            @click="closeViewModal"
                        >
                            ✕
                        </button>
                    </div>

                    <div class="max-h-[75vh] overflow-y-auto p-5 sm:p-6">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Email</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedTeacher.email || '-' }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Phone</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedTeacher.phone || '-' }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Gender</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedTeacher.gender || '-' }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Date of Birth</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedTeacher.date_of_birth || '-' }}</p>
                            </div>

                            <div class="rounded-2xl bg-indigo-50 p-4 ring-1 ring-indigo-100">
                                <p class="text-xs font-bold uppercase text-indigo-500">Subject</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedTeacher.subject }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Assigned Class</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedTeacher.assigned_class || '-' }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Joining Date</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedTeacher.joining_date || '-' }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Status</p>
                                <p class="mt-1">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                                        :class="
                                            selectedTeacher.status === 'Active'
                                                ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                                : selectedTeacher.status === 'On Leave'
                                                    ? 'bg-amber-50 text-amber-700 ring-amber-100'
                                                    : 'bg-rose-50 text-rose-700 ring-rose-100'
                                        "
                                    >
                                        {{ selectedTeacher.status }}
                                    </span>
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100 sm:col-span-2">
                                <p class="text-xs font-bold uppercase text-slate-400">Address</p>
                                <p class="mt-1 text-sm font-bold leading-6 text-slate-800">
                                    {{ selectedTeacher.address || '-' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 p-5 sm:p-6">
                        <button
                            type="button"
                            class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                            @click="closeViewModal"
                        >
                            Close
                        </button>

                        <button
                            type="button"
                            class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800"
                            @click="openEditModal(selectedTeacher); showViewModal = false"
                        >
                            Edit Teacher
                        </button>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div
                v-if="showEditModal && selectedTeacher"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
            >
                <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">Edit Teacher</h2>
                            <p class="mt-1 text-sm text-slate-500">Update teacher profile, subject assignment and contact details.</p>
                        </div>

                        <button
                            type="button"
                            class="rounded-2xl bg-white p-2 text-slate-400 shadow-sm ring-1 ring-slate-200 transition hover:text-slate-700"
                            @click="closeEditModal"
                        >
                            ✕
                        </button>
                    </div>

                    <div class="max-h-[75vh] overflow-y-auto p-5 sm:p-6">
                        <p
                            v-if="editForm.errors.edit_server"
                            class="mb-5 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-bold text-rose-600"
                        >
                            {{ editForm.errors.edit_server }}
                        </p>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="text-sm font-semibold text-slate-600">Full Name *</label>
                                <input
                                    v-model="editForm.full_name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'full_name')"
                                />
                                <p v-if="fieldError(editFrontendErrors, editForm, 'full_name')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(editFrontendErrors, editForm, 'full_name') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Teacher ID *</label>
                                <input
                                    v-model="editForm.teacher_id"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'teacher_id')"
                                />
                                <p v-if="fieldError(editFrontendErrors, editForm, 'teacher_id')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(editFrontendErrors, editForm, 'teacher_id') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Email</label>
                                <input
                                    v-model="editForm.email"
                                    type="email"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'email')"
                                />
                                <p v-if="fieldError(editFrontendErrors, editForm, 'email')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(editFrontendErrors, editForm, 'email') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Phone</label>
                                <input
                                    v-model="editForm.phone"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'phone')"
                                />
                                <p v-if="fieldError(editFrontendErrors, editForm, 'phone')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(editFrontendErrors, editForm, 'phone') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Gender</label>
                                <select
                                    v-model="editForm.gender"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option value="">Select gender</option>
                                    <option>Male</option>
                                    <option>Female</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Date of Birth</label>
                                <input
                                    v-model="editForm.date_of_birth"
                                    type="date"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'date_of_birth')"
                                />
                                <p v-if="fieldError(editFrontendErrors, editForm, 'date_of_birth')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(editFrontendErrors, editForm, 'date_of_birth') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Subject *</label>
                                <select
                                    v-model="editForm.subject"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'subject')"
                                >
                                    <option value="">Select subject</option>
                                    <option v-for="subject in subjectOptions" :key="subject" :value="subject">
                                        {{ subject }}
                                    </option>
                                </select>
                                <p v-if="fieldError(editFrontendErrors, editForm, 'subject')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(editFrontendErrors, editForm, 'subject') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Assigned Class</label>
                                <select
                                    v-model="editForm.assigned_class"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option value="">Select class</option>
                                    <option v-for="className in classOptions" :key="className" :value="className">
                                        {{ className }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Joining Date</label>
                                <input
                                    v-model="editForm.joining_date"
                                    type="date"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status *</label>
                                <select
                                    v-model="editForm.status"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option>Active</option>
                                    <option>On Leave</option>
                                    <option>Inactive</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Address</label>
                                <textarea
                                    v-model="editForm.address"
                                    rows="3"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'address')"
                                ></textarea>
                                <p v-if="fieldError(editFrontendErrors, editForm, 'address')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(editFrontendErrors, editForm, 'address') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 p-5 sm:flex-row sm:justify-end sm:p-6">
                        <button
                            type="button"
                            class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                            @click="closeEditModal"
                        >
                            Cancel
                        </button>

                        <button
                            type="button"
                            class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="editForm.processing || !canUpdate"
                            @click="updateTeacher"
                        >
                            {{ editForm.processing ? 'Updating...' : 'Update Teacher' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>