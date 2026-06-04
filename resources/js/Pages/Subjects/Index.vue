<script setup>
import { ref, computed } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    subjects: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            class_name: '',
            type: '',
            status: '',
        }),
    },
    subjectStats: {
        type: Object,
        default: () => ({
            cards: [],
            overview: {},
        }),
    },
    classNames: {
        type: Array,
        default: () => [],
    },
    teacherNames: {
        type: Array,
        default: () => [],
    },
})

const subjectList = computed(() => props.subjects || { data: [], links: [] })
const statCards = computed(() => props.subjectStats?.cards || [])
const overview = computed(() => props.subjectStats?.overview || {})

const defaultClasses = ['Grade 8', 'Grade 10', 'Grade 11', 'Grade 12']
const defaultTeachers = ['Ahmed Raza', 'Fatima Noor', 'John Smith']

const classOptions = computed(() => [...new Set([...(props.classNames || []), ...defaultClasses])])
const teacherOptions = computed(() => [...new Set([...(props.teacherNames || []), ...defaultTeachers])])

const showCreateModal = ref(false)
const showViewModal = ref(false)
const showEditModal = ref(false)
const selectedSubject = ref(null)

const filterForm = useForm({
    search: props.filters?.search || '',
    class_name: props.filters?.class_name || '',
    type: props.filters?.type || '',
    status: props.filters?.status || '',
})

const form = useForm({
    subject_id: '',
    name: '',
    code: '',
    class_name: '',
    teacher_name: '',
    type: 'Core',
    weekly_hours: '',
    credit: '',
    status: 'Active',
    description: '',
})

const editForm = useForm({
    subject_id: '',
    name: '',
    code: '',
    class_name: '',
    teacher_name: '',
    type: 'Core',
    weekly_hours: '',
    credit: '',
    status: 'Active',
    description: '',
})

const validateSubject = (targetForm) => {
    const errors = {}

    if (!targetForm.subject_id.trim()) errors.subject_id = 'Subject ID is required.'
    if (!targetForm.name.trim()) errors.name = 'Subject name is required.'
    if (!targetForm.code.trim()) errors.code = 'Subject code is required.'
    if (!targetForm.class_name) errors.class_name = 'Class is required.'
    if (!targetForm.type) errors.type = 'Subject type is required.'

    if (targetForm.weekly_hours === '' || targetForm.weekly_hours === null) {
        errors.weekly_hours = 'Weekly hours are required.'
    }

    if (targetForm.weekly_hours !== '' && Number(targetForm.weekly_hours) < 0) {
        errors.weekly_hours = 'Weekly hours cannot be negative.'
    }

    if (targetForm.credit !== '' && targetForm.credit !== null && Number(targetForm.credit) < 0) {
        errors.credit = 'Credit cannot be negative.'
    }

    if (targetForm.description && targetForm.description.length > 1000) {
        errors.description = 'Description must not be greater than 1000 characters.'
    }

    return errors
}

const frontendErrors = computed(() => validateSubject(form))
const editFrontendErrors = computed(() => validateSubject(editForm))

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
    form.type = 'Core'
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

const submitSubject = () => {
    if (!canSubmit.value || form.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    form.post(route('subjects.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Subject created successfully.')
            resetForm()
            showCreateModal.value = false
        },
        onError: () => toast.error('Please check the form errors.'),
    })
}

const openViewModal = (subject) => {
    selectedSubject.value = subject
    showViewModal.value = true
}

const closeViewModal = () => {
    selectedSubject.value = null
    showViewModal.value = false
}

const openEditModal = (subject) => {
    selectedSubject.value = subject

    editForm.subject_id = subject.subject_id || ''
    editForm.name = subject.name || ''
    editForm.code = subject.code || ''
    editForm.class_name = subject.class_name || ''
    editForm.teacher_name = subject.teacher_name || ''
    editForm.type = subject.type || 'Core'
    editForm.weekly_hours = subject.weekly_hours ?? ''
    editForm.credit = subject.credit ?? ''
    editForm.status = subject.status || 'Active'
    editForm.description = subject.description || ''

    editForm.clearErrors()
    showEditModal.value = true
}

const closeEditModal = () => {
    selectedSubject.value = null
    editForm.clearErrors()
    showEditModal.value = false
}

const updateSubject = () => {
    if (!selectedSubject.value || !canUpdate.value || editForm.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    editForm.put(route('subjects.update', selectedSubject.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Subject updated successfully.')
            closeEditModal()
        },
        onError: () => toast.error('Please check the form errors.'),
    })
}

const deleteSubject = (subject) => {
    if (!confirm(`Delete ${subject.name}?`)) return

    router.delete(route('subjects.destroy', subject.id), {
        preserveScroll: true,
        onSuccess: () => toast.success('Subject deleted successfully.'),
        onError: () => toast.error('Unable to delete subject.'),
    })
}

const applyFilters = () => {
    router.get(route('subjects'), {
        search: filterForm.search,
        class_name: filterForm.class_name,
        type: filterForm.type,
        status: filterForm.status,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

const resetFilters = () => {
    filterForm.search = ''
    filterForm.class_name = ''
    filterForm.type = ''
    filterForm.status = ''

    router.get(route('subjects'), {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}
</script>

<template>
    <AppLayout title="Subjects">
        <div class="space-y-6">
            <!-- Header -->
            <div class="rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 ring-1 ring-slate-200 sm:p-6 lg:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">Academic Curriculum</p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                            Subjects
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                            Manage subjects, subject codes, assigned teachers, weekly hours and class-wise curriculum.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800"
                        @click="openCreateModal"
                    >
                        Add Subject
                    </button>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div
                    v-for="(stat, index) in statCards"
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
                                index === 2 ? 'bg-gradient-to-br from-violet-500 to-fuchsia-400' : '',
                                index === 3 ? 'bg-gradient-to-br from-amber-500 to-orange-400' : '',
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
                    <h2 class="text-lg font-black text-slate-950">Curriculum Overview</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Live summary of subject distribution and academic workload.
                    </p>

                    <div class="mt-6 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl bg-indigo-50 p-5">
                            <p class="text-xs font-semibold text-indigo-600">Weekly Hours</p>
                            <p class="mt-2 text-2xl font-black text-slate-950">
                                {{ overview.weekly_hours || 0 }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">Total scheduled hours</p>
                        </div>

                        <div class="rounded-2xl bg-emerald-50 p-5">
                            <p class="text-xs font-semibold text-emerald-600">Active Subjects</p>
                            <p class="mt-2 text-2xl font-black text-slate-950">
                                {{ overview.active || 0 }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">Currently running</p>
                        </div>

                        <div class="rounded-2xl bg-amber-50 p-5">
                            <p class="text-xs font-semibold text-amber-600">Pending Assignment</p>
                            <p class="mt-2 text-2xl font-black text-slate-950">
                                {{ overview.pending_assignment || 0 }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">Need teacher assignment</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-gradient-to-br from-indigo-600 to-sky-500 p-6 text-white shadow-lg shadow-indigo-100">
                    <h3 class="text-lg font-black">Subject Load</h3>
                    <p class="mt-1 text-sm text-white/75">Academic distribution</p>

                    <div class="mt-6 space-y-4">
                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <div class="flex justify-between text-sm">
                                <span>Core Subjects</span>
                                <span class="font-bold">{{ overview.core || 0 }}</span>
                            </div>
                            <div class="mt-3 h-2 rounded-full bg-white/20">
                                <div class="h-2 rounded-full bg-white" :style="{ width: `${Math.min(((overview.core || 0) / Math.max((overview.core || 0) + (overview.elective || 0) + (overview.optional || 0), 1)) * 100, 100)}%` }"></div>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <div class="flex justify-between text-sm">
                                <span>Electives</span>
                                <span class="font-bold">{{ overview.elective || 0 }}</span>
                            </div>
                            <div class="mt-3 h-2 rounded-full bg-white/20">
                                <div class="h-2 rounded-full bg-white" :style="{ width: `${Math.min(((overview.elective || 0) / Math.max((overview.core || 0) + (overview.elective || 0) + (overview.optional || 0), 1)) * 100, 100)}%` }"></div>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <div class="flex justify-between text-sm">
                                <span>Optional</span>
                                <span class="font-bold">{{ overview.optional || 0 }}</span>
                            </div>
                            <div class="mt-3 h-2 rounded-full bg-white/20">
                                <div class="h-2 rounded-full bg-white" :style="{ width: `${Math.min(((overview.optional || 0) / Math.max((overview.core || 0) + (overview.elective || 0) + (overview.optional || 0), 1)) * 100, 100)}%` }"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <form class="grid gap-4 md:grid-cols-2 xl:grid-cols-6" @submit.prevent="applyFilters">
                    <div class="xl:col-span-2">
                        <label class="text-sm font-semibold text-slate-600">Search Subject</label>
                        <input
                            v-model="filterForm.search"
                            type="text"
                            placeholder="Search by subject, code, class or teacher"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        />
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Class</label>
                        <select
                            v-model="filterForm.class_name"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        >
                            <option value="">All Classes</option>
                            <option v-for="className in classOptions" :key="className" :value="className">
                                {{ className }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Subject Type</label>
                        <select
                            v-model="filterForm.type"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        >
                            <option value="">All Types</option>
                            <option>Core</option>
                            <option>Elective</option>
                            <option>Optional</option>
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

            <!-- Subject Cards -->
            <!-- <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                <div
                    v-for="subject in subjectList.data"
                    :key="subject.id"
                    class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-lg"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                {{ subject.code }}
                            </p>

                            <h2 class="mt-2 text-xl font-black text-slate-950">
                                {{ subject.name }}
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                {{ subject.class_name }}
                            </p>
                        </div>

                        <span
                            class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                            :class="
                                subject.status === 'Active'
                                    ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                    : 'bg-rose-50 text-rose-700 ring-rose-100'
                            "
                        >
                            {{ subject.status }}
                        </span>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-indigo-50 p-4">
                            <p class="text-xs font-semibold text-indigo-500">Teacher</p>
                            <p class="mt-1 truncate text-sm font-bold text-slate-900">
                                {{ subject.teacher_name || '-' }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-sky-50 p-4">
                            <p class="text-xs font-semibold text-sky-500">Weekly Hours</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">
                                {{ subject.weekly_hours }} Hours
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 flex gap-2">
                        <button
                            type="button"
                            class="flex-1 rounded-2xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                            @click="openViewModal(subject)"
                        >
                            View
                        </button>

                        <button
                            type="button"
                            class="flex-1 rounded-2xl bg-indigo-50 px-4 py-2.5 text-sm font-bold text-indigo-700 transition hover:bg-indigo-100"
                            @click="openEditModal(subject)"
                        >
                            Edit
                        </button>
                    </div>
                </div>
            </div> -->

            <!-- Table -->
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">Subject Records</h2>
                        <p class="mt-1 text-sm text-slate-500">Complete subject list from database.</p>
                    </div>

                    <button
                        type="button"
                        class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800"
                        @click="openCreateModal"
                    >
                        New Subject
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1080px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Subject</th>
                                <th class="px-6 py-4">Subject ID</th>
                                <th class="px-6 py-4">Class</th>
                                <th class="px-6 py-4">Teacher</th>
                                <th class="px-6 py-4">Type</th>
                                <th class="px-6 py-4">Hours</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="subject in subjectList.data"
                                :key="subject.id"
                                class="transition hover:bg-indigo-50/40"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 to-sky-100 text-sm font-black text-indigo-700">
                                            {{ subject.name?.charAt(0) || 'S' }}
                                        </div>

                                        <div>
                                            <p class="font-bold text-slate-950">{{ subject.name }}</p>
                                            <p class="text-xs text-slate-400">{{ subject.code }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-700">
                                    {{ subject.subject_id }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ subject.class_name }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ subject.teacher_name || '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                                        :class="
                                            subject.type === 'Core'
                                                ? 'bg-indigo-50 text-indigo-700 ring-indigo-100'
                                                : subject.type === 'Elective'
                                                    ? 'bg-violet-50 text-violet-700 ring-violet-100'
                                                    : 'bg-amber-50 text-amber-700 ring-amber-100'
                                        "
                                    >
                                        {{ subject.type }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ subject.weekly_hours }} hrs/week
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                                        :class="
                                            subject.status === 'Active'
                                                ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                                : 'bg-rose-50 text-rose-700 ring-rose-100'
                                        "
                                    >
                                        {{ subject.status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            type="button"
                                            class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200"
                                            @click="openViewModal(subject)"
                                        >
                                            View
                                        </button>

                                        <button
                                            type="button"
                                            class="rounded-xl bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100"
                                            @click="openEditModal(subject)"
                                        >
                                            Edit
                                        </button>

                                        <button
                                            type="button"
                                            class="rounded-xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100"
                                            @click="deleteSubject(subject)"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="subjectList.data.length === 0">
                                <td colspan="8" class="px-6 py-10 text-center text-sm font-bold text-slate-400">
                                    No subjects found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="subjectList.links && subjectList.links.length > 3"
                    class="flex flex-col gap-4 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <p class="text-sm font-semibold text-slate-500">
                        Showing
                        <span class="font-black text-slate-800">{{ subjectList.from || 0 }}</span>
                        to
                        <span class="font-black text-slate-800">{{ subjectList.to || 0 }}</span>
                        of
                        <span class="font-black text-slate-800">{{ subjectList.total || 0 }}</span>
                        subjects
                    </p>

                    <div class="flex flex-wrap gap-2">
                        <Link
                            v-for="link in subjectList.links"
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
                            <h2 class="text-xl font-black text-slate-950">Add New Subject</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Create subject, assign teacher, class and weekly academic workload.
                            </p>
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
                                <label class="text-sm font-semibold text-slate-600">Subject ID *</label>
                                <input
                                    v-model="form.subject_id"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'subject_id')"
                                    placeholder="SUB-001"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'subject_id')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'subject_id') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Subject Name *</label>
                                <input
                                    v-model="form.name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'name')"
                                    placeholder="Mathematics"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'name')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'name') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Subject Code *</label>
                                <input
                                    v-model="form.code"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'code')"
                                    placeholder="MATH-10"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'code')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'code') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Subject Type *</label>
                                <select
                                    v-model="form.type"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'type')"
                                >
                                    <option>Core</option>
                                    <option>Elective</option>
                                    <option>Optional</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Assigned Class *</label>
                                <select
                                    v-model="form.class_name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'class_name')"
                                >
                                    <option value="">Select class</option>
                                    <option v-for="className in classOptions" :key="className" :value="className">
                                        {{ className }}
                                    </option>
                                </select>
                                <p v-if="fieldError(frontendErrors, form, 'class_name')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'class_name') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Assigned Teacher</label>
                                <select
                                    v-model="form.teacher_name"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option value="">Select teacher</option>
                                    <option v-for="teacher in teacherOptions" :key="teacher" :value="teacher">
                                        {{ teacher }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Weekly Hours *</label>
                                <input
                                    v-model="form.weekly_hours"
                                    type="number"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'weekly_hours')"
                                    placeholder="5"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'weekly_hours')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'weekly_hours') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Credit / Weightage</label>
                                <input
                                    v-model="form.credit"
                                    type="number"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'credit')"
                                    placeholder="100"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'credit')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'credit') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status *</label>
                                <select
                                    v-model="form.status"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option>Active</option>
                                    <option>Inactive</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Description</label>
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'description')"
                                    placeholder="Optional subject description"
                                ></textarea>
                                <p v-if="fieldError(frontendErrors, form, 'description')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'description') }}
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
                            @click="submitSubject"
                        >
                            {{ form.processing ? 'Saving...' : 'Save Subject' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- View Modal -->
            <div
                v-if="showViewModal && selectedSubject"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
            >
                <div class="w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">{{ selectedSubject.name }}</h2>
                            <p class="mt-1 text-sm font-semibold text-slate-500">
                                {{ selectedSubject.subject_id }} · {{ selectedSubject.code }}
                            </p>
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
                            <div class="rounded-2xl bg-indigo-50 p-4 ring-1 ring-indigo-100">
                                <p class="text-xs font-bold uppercase text-indigo-500">Class</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedSubject.class_name }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Teacher</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedSubject.teacher_name || '-' }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Type</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedSubject.type }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Weekly Hours</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedSubject.weekly_hours }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Credit</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedSubject.credit || '-' }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Status</p>
                                <p class="mt-1">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                                        :class="
                                            selectedSubject.status === 'Active'
                                                ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                                : 'bg-rose-50 text-rose-700 ring-rose-100'
                                        "
                                    >
                                        {{ selectedSubject.status }}
                                    </span>
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100 sm:col-span-2">
                                <p class="text-xs font-bold uppercase text-slate-400">Description</p>
                                <p class="mt-1 text-sm font-bold leading-6 text-slate-800">
                                    {{ selectedSubject.description || '-' }}
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
                            @click="openEditModal(selectedSubject); showViewModal = false"
                        >
                            Edit Subject
                        </button>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div
                v-if="showEditModal && selectedSubject"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
            >
                <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">Edit Subject</h2>
                            <p class="mt-1 text-sm text-slate-500">Update subject, class, teacher and workload details.</p>
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
                                <label class="text-sm font-semibold text-slate-600">Subject ID *</label>
                                <input
                                    v-model="editForm.subject_id"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'subject_id')"
                                />
                                <p v-if="fieldError(editFrontendErrors, editForm, 'subject_id')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(editFrontendErrors, editForm, 'subject_id') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Subject Name *</label>
                                <input
                                    v-model="editForm.name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'name')"
                                />
                                <p v-if="fieldError(editFrontendErrors, editForm, 'name')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(editFrontendErrors, editForm, 'name') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Subject Code *</label>
                                <input
                                    v-model="editForm.code"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'code')"
                                />
                                <p v-if="fieldError(editFrontendErrors, editForm, 'code')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(editFrontendErrors, editForm, 'code') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Subject Type *</label>
                                <select
                                    v-model="editForm.type"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'type')"
                                >
                                    <option>Core</option>
                                    <option>Elective</option>
                                    <option>Optional</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Assigned Class *</label>
                                <select
                                    v-model="editForm.class_name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'class_name')"
                                >
                                    <option value="">Select class</option>
                                    <option v-for="className in classOptions" :key="className" :value="className">
                                        {{ className }}
                                    </option>
                                </select>
                                <p v-if="fieldError(editFrontendErrors, editForm, 'class_name')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(editFrontendErrors, editForm, 'class_name') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Assigned Teacher</label>
                                <select
                                    v-model="editForm.teacher_name"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option value="">Select teacher</option>
                                    <option v-for="teacher in teacherOptions" :key="teacher" :value="teacher">
                                        {{ teacher }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Weekly Hours *</label>
                                <input
                                    v-model="editForm.weekly_hours"
                                    type="number"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'weekly_hours')"
                                />
                                <p v-if="fieldError(editFrontendErrors, editForm, 'weekly_hours')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(editFrontendErrors, editForm, 'weekly_hours') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Credit / Weightage</label>
                                <input
                                    v-model="editForm.credit"
                                    type="number"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'credit')"
                                />
                                <p v-if="fieldError(editFrontendErrors, editForm, 'credit')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(editFrontendErrors, editForm, 'credit') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status *</label>
                                <select
                                    v-model="editForm.status"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option>Active</option>
                                    <option>Inactive</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Description</label>
                                <textarea
                                    v-model="editForm.description"
                                    rows="3"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'description')"
                                ></textarea>
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
                            @click="updateSubject"
                        >
                            {{ editForm.processing ? 'Updating...' : 'Update Subject' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>