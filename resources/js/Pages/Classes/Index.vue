<script setup>
import { ref, computed } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    classes: {
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
            name: '',
            status: '',
        }),
    },
    classStats: {
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
})

const classList = computed(() => props.classes || { data: [], links: [] })
const statCards = computed(() => props.classStats?.cards || [])
const overview = computed(() => props.classStats?.overview || {})

const defaultClasses = [
    'Grade 1',
    'Grade 2',
    'Grade 3',
    'Grade 4',
    'Grade 5',
    'Grade 6',
    'Grade 7',
    'Grade 8',
    'Grade 9',
    'Grade 10',
    'Grade 11',
    'Grade 12',
]

const classOptions = computed(() => [...new Set([...(props.classNames || []), ...defaultClasses])])
const sectionOptions = ['A', 'B', 'C', 'D', 'E']

const showCreateModal = ref(false)
const showViewModal = ref(false)
const showEditModal = ref(false)
const selectedClass = ref(null)

const filterForm = useForm({
    search: props.filters?.search || '',
    name: props.filters?.name || '',
    status: props.filters?.status || '',
})

const form = useForm({
    class_id: '',
    name: '',
    section: '',
    teacher_name: '',
    room: '',
    capacity: '',
    students_count: 0,
    status: 'Active',
    description: '',
})

const editForm = useForm({
    class_id: '',
    name: '',
    section: '',
    teacher_name: '',
    room: '',
    capacity: '',
    students_count: 0,
    status: 'Active',
    description: '',
})

const validateClass = (targetForm) => {
    const errors = {}

    if (!targetForm.class_id.trim()) errors.class_id = 'Class ID is required.'
    if (!targetForm.name) errors.name = 'Class name is required.'
    if (!targetForm.section) errors.section = 'Section is required.'

    if (targetForm.capacity && Number(targetForm.capacity) < 1) {
        errors.capacity = 'Capacity must be at least 1.'
    }

    if (targetForm.students_count !== '' && Number(targetForm.students_count) < 0) {
        errors.students_count = 'Students count cannot be negative.'
    }

    if (targetForm.description && targetForm.description.length > 1000) {
        errors.description = 'Description must not be greater than 1000 characters.'
    }

    return errors
}

const frontendErrors = computed(() => validateClass(form))
const editFrontendErrors = computed(() => validateClass(editForm))

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
    form.students_count = 0
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

const submitClass = () => {
    if (!canSubmit.value || form.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    form.post(route('classes.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Class created successfully.')
            resetForm()
            showCreateModal.value = false
        },
        onError: () => toast.error('Please check the form errors.'),
    })
}

const openViewModal = (schoolClass) => {
    selectedClass.value = schoolClass
    showViewModal.value = true
}

const closeViewModal = () => {
    selectedClass.value = null
    showViewModal.value = false
}

const openEditModal = (schoolClass) => {
    selectedClass.value = schoolClass

    editForm.class_id = schoolClass.class_id || ''
    editForm.name = schoolClass.name || ''
    editForm.section = schoolClass.section || ''
    editForm.teacher_name = schoolClass.teacher_name || ''
    editForm.room = schoolClass.room || ''
    editForm.capacity = schoolClass.capacity || ''
    editForm.students_count = schoolClass.students_count ?? 0
    editForm.status = schoolClass.status || 'Active'
    editForm.description = schoolClass.description || ''

    editForm.clearErrors()
    showEditModal.value = true
}

const closeEditModal = () => {
    selectedClass.value = null
    editForm.clearErrors()
    showEditModal.value = false
}

const updateClass = () => {
    if (!selectedClass.value || !canUpdate.value || editForm.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    editForm.put(route('classes.update', selectedClass.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Class updated successfully.')
            closeEditModal()
        },
        onError: () => toast.error('Please check the form errors.'),
    })
}

const deleteClass = (schoolClass) => {
    if (!confirm(`Delete ${schoolClass.name} - ${schoolClass.section}?`)) return

    router.delete(route('classes.destroy', schoolClass.id), {
        preserveScroll: true,
        onSuccess: () => toast.success('Class deleted successfully.'),
        onError: () => toast.error('Unable to delete class.'),
    })
}

const applyFilters = () => {
    router.get(route('classes'), {
        search: filterForm.search,
        name: filterForm.name,
        status: filterForm.status,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

const resetFilters = () => {
    filterForm.search = ''
    filterForm.name = ''
    filterForm.status = ''

    router.get(route('classes'), {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}
</script>

<template>
    <AppLayout title="Classes">
        <div class="space-y-6">
            <div class="rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 ring-1 ring-slate-200 sm:p-6 lg:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">Academic Structure</p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                            Classes & Sections
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                            Manage classes, sections, assigned teachers, classrooms and student capacity.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800"
                        @click="openCreateModal"
                    >
                        Add Class
                    </button>
                </div>
            </div>

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

                    <p class="mt-4 text-xs font-semibold text-slate-500">{{ stat.change }}</p>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-3">
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 xl:col-span-2">
                    <h2 class="text-lg font-black text-slate-950">Class Overview</h2>
                    <p class="mt-1 text-sm text-slate-500">Live summary from class database.</p>

                    <div class="mt-6 grid gap-4 sm:grid-cols-4">
                        <div class="rounded-2xl bg-indigo-50 p-5">
                            <p class="text-xs font-semibold text-indigo-600">Active</p>
                            <p class="mt-2 text-2xl font-black text-slate-950">{{ overview.active || 0 }}</p>
                            <p class="mt-1 text-xs text-slate-500">Running classes</p>
                        </div>

                        <div class="rounded-2xl bg-rose-50 p-5">
                            <p class="text-xs font-semibold text-rose-600">Inactive</p>
                            <p class="mt-2 text-2xl font-black text-slate-950">{{ overview.inactive || 0 }}</p>
                            <p class="mt-1 text-xs text-slate-500">Disabled classes</p>
                        </div>

                        <div class="rounded-2xl bg-emerald-50 p-5">
                            <p class="text-xs font-semibold text-emerald-600">Capacity</p>
                            <p class="mt-2 text-2xl font-black text-slate-950">{{ overview.capacity || 0 }}</p>
                            <p class="mt-1 text-xs text-slate-500">Total seats</p>
                        </div>

                        <div class="rounded-2xl bg-amber-50 p-5">
                            <p class="text-xs font-semibold text-amber-600">Students</p>
                            <p class="mt-2 text-2xl font-black text-slate-950">{{ overview.students || 0 }}</p>
                            <p class="mt-1 text-xs text-slate-500">Enrolled count</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-gradient-to-br from-indigo-600 to-sky-500 p-6 text-white shadow-lg shadow-indigo-100">
                    <h3 class="text-lg font-black">Academic Snapshot</h3>
                    <p class="text-sm text-white/75">Capacity and enrollment</p>

                    <div class="mt-8 space-y-4">
                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Total Capacity</p>
                            <p class="mt-1 font-bold">{{ overview.capacity || 0 }} seats</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Total Students</p>
                            <p class="mt-1 font-bold">{{ overview.students || 0 }} enrolled</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Available Seats</p>
                            <p class="mt-1 font-bold">{{ Math.max((overview.capacity || 0) - (overview.students || 0), 0) }} seats</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <form class="grid gap-4 md:grid-cols-2 xl:grid-cols-5" @submit.prevent="applyFilters">
                    <div class="xl:col-span-2">
                        <label class="text-sm font-semibold text-slate-600">Search Class</label>
                        <input
                            v-model="filterForm.search"
                            type="text"
                            placeholder="Search by class, section, teacher or room"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        />
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Grade</label>
                        <select
                            v-model="filterForm.name"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        >
                            <option value="">All Grades</option>
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

            <!-- <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                <div
                    v-for="item in classList.data"
                    :key="item.id"
                    class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-lg"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ item.class_id }}</p>

                            <h2 class="mt-2 text-xl font-black text-slate-950">
                                {{ item.name }} - {{ item.section }}
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">{{ item.room || 'No room assigned' }}</p>
                        </div>

                        <span
                            class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                            :class="
                                item.status === 'Active'
                                    ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                    : 'bg-rose-50 text-rose-700 ring-rose-100'
                            "
                        >
                            {{ item.status }}
                        </span>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-indigo-50 p-4">
                            <p class="text-xs font-semibold text-indigo-500">Class Teacher</p>
                            <p class="mt-1 truncate text-sm font-bold text-slate-900">{{ item.teacher_name || '-' }}</p>
                        </div>

                        <div class="rounded-2xl bg-sky-50 p-4">
                            <p class="text-xs font-semibold text-sky-500">Students</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">
                                {{ item.students_count || 0 }} / {{ item.capacity || 0 }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 flex gap-2">
                        <button
                            type="button"
                            class="flex-1 rounded-2xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                            @click="openViewModal(item)"
                        >
                            View
                        </button>

                        <button
                            type="button"
                            class="flex-1 rounded-2xl bg-indigo-50 px-4 py-2.5 text-sm font-bold text-indigo-700 transition hover:bg-indigo-100"
                            @click="openEditModal(item)"
                        >
                            Edit
                        </button>
                    </div>
                </div>
            </div> -->

            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">Class Records</h2>
                        <p class="mt-1 text-sm text-slate-500">Overview of all class sections.</p>
                    </div>

                    <button
                        type="button"
                        class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white hover:bg-slate-800"
                        @click="openCreateModal"
                    >
                        Add New
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1000px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Class</th>
                                <th class="px-6 py-4">Class ID</th>
                                <th class="px-6 py-4">Teacher</th>
                                <th class="px-6 py-4">Room</th>
                                <th class="px-6 py-4">Students</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="item in classList.data"
                                :key="item.id"
                                class="transition hover:bg-indigo-50/40"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 to-sky-100 text-sm font-black text-indigo-700">
                                            {{ item.name?.replace('Grade ', '') || 'C' }}
                                        </div>

                                        <div>
                                            <p class="font-bold text-slate-950">{{ item.name }}</p>
                                            <p class="text-xs text-slate-400">Section {{ item.section }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-700">{{ item.class_id }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ item.teacher_name || '-' }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ item.room || '-' }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ item.students_count || 0 }} / {{ item.capacity || 0 }}</td>

                                <td class="px-6 py-4">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                                        :class="
                                            item.status === 'Active'
                                                ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                                : 'bg-rose-50 text-rose-700 ring-rose-100'
                                        "
                                    >
                                        {{ item.status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            type="button"
                                            class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200"
                                            @click="openViewModal(item)"
                                        >
                                            View
                                        </button>

                                        <button
                                            type="button"
                                            class="rounded-xl bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100"
                                            @click="openEditModal(item)"
                                        >
                                            Edit
                                        </button>

                                        <button
                                            type="button"
                                            class="rounded-xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100"
                                            @click="deleteClass(item)"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="classList.data.length === 0">
                                <td colspan="7" class="px-6 py-10 text-center text-sm font-bold text-slate-400">
                                    No classes found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="classList.links && classList.links.length > 3"
                    class="flex flex-col gap-4 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <p class="text-sm font-semibold text-slate-500">
                        Showing
                        <span class="font-black text-slate-800">{{ classList.from || 0 }}</span>
                        to
                        <span class="font-black text-slate-800">{{ classList.to || 0 }}</span>
                        of
                        <span class="font-black text-slate-800">{{ classList.total || 0 }}</span>
                        classes
                    </p>

                    <div class="flex flex-wrap gap-2">
                        <Link
                            v-for="link in classList.links"
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

            <!-- Use same modal style for create/edit/view -->
            <!-- Create Modal -->
            <div
                v-if="showCreateModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
            >
                <div class="w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">Add New Class</h2>
                            <p class="mt-1 text-sm text-slate-500">Create class section, room, teacher and capacity.</p>
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
                                <label class="text-sm font-semibold text-slate-600">Class ID *</label>
                                <input
                                    v-model="form.class_id"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'class_id')"
                                    placeholder="CLS-001"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'class_id')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'class_id') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Class Name *</label>
                                <select
                                    v-model="form.name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'name')"
                                >
                                    <option value="">Select class</option>
                                    <option v-for="className in classOptions" :key="className" :value="className">
                                        {{ className }}
                                    </option>
                                </select>
                                <p v-if="fieldError(frontendErrors, form, 'name')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'name') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Section *</label>
                                <select
                                    v-model="form.section"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'section')"
                                >
                                    <option value="">Select section</option>
                                    <option v-for="section in sectionOptions" :key="section" :value="section">
                                        {{ section }}
                                    </option>
                                </select>
                                <p v-if="fieldError(frontendErrors, form, 'section')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'section') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Class Teacher</label>
                                <input
                                    v-model="form.teacher_name"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                    placeholder="Teacher name"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Room Number</label>
                                <input
                                    v-model="form.room"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                    placeholder="Room 101"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Student Capacity</label>
                                <input
                                    v-model="form.capacity"
                                    type="number"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'capacity')"
                                    placeholder="40"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'capacity')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'capacity') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Students Count</label>
                                <input
                                    v-model="form.students_count"
                                    type="number"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'students_count')"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'students_count')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'students_count') }}
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
                                    placeholder="Optional class notes"
                                ></textarea>
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
                            @click="submitClass"
                        >
                            {{ form.processing ? 'Saving...' : 'Save Class' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- View Modal -->
<div
    v-if="showViewModal && selectedClass"
    class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
>
    <div class="w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
        <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
            <div>
                <h2 class="text-xl font-black text-slate-950">
                    {{ selectedClass.name }} - {{ selectedClass.section }}
                </h2>
                <p class="mt-1 text-sm font-semibold text-slate-500">
                    {{ selectedClass.class_id }}
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
                    <p class="mt-1 text-sm font-bold text-slate-800">
                        {{ selectedClass.name }} - {{ selectedClass.section }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                    <p class="text-xs font-bold uppercase text-slate-400">Status</p>
                    <p class="mt-1">
                        <span
                            class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                            :class="selectedClass.status === 'Active'
                                ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                : 'bg-rose-50 text-rose-700 ring-rose-100'"
                        >
                            {{ selectedClass.status }}
                        </span>
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                    <p class="text-xs font-bold uppercase text-slate-400">Class Teacher</p>
                    <p class="mt-1 text-sm font-bold text-slate-800">
                        {{ selectedClass.teacher_name || '-' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                    <p class="text-xs font-bold uppercase text-slate-400">Room</p>
                    <p class="mt-1 text-sm font-bold text-slate-800">
                        {{ selectedClass.room || '-' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                    <p class="text-xs font-bold uppercase text-slate-400">Capacity</p>
                    <p class="mt-1 text-sm font-bold text-slate-800">
                        {{ selectedClass.capacity || 0 }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                    <p class="text-xs font-bold uppercase text-slate-400">Students Count</p>
                    <p class="mt-1 text-sm font-bold text-slate-800">
                        {{ selectedClass.students_count || 0 }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100 sm:col-span-2">
                    <p class="text-xs font-bold uppercase text-slate-400">Description</p>
                    <p class="mt-1 text-sm font-bold leading-6 text-slate-800">
                        {{ selectedClass.description || '-' }}
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
                @click="openEditModal(selectedClass); showViewModal = false"
            >
                Edit Class
            </button>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div
    v-if="showEditModal && selectedClass"
    class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
>
    <div class="w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
        <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
            <div>
                <h2 class="text-xl font-black text-slate-950">Edit Class</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Update class section, teacher, room and capacity.
                </p>
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
                    <label class="text-sm font-semibold text-slate-600">Class ID *</label>
                    <input
                        v-model="editForm.class_id"
                        class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                        :class="inputClass(editFrontendErrors, editForm, 'class_id')"
                    />
                    <p v-if="fieldError(editFrontendErrors, editForm, 'class_id')" class="mt-1 text-xs font-bold text-rose-600">
                        {{ fieldError(editFrontendErrors, editForm, 'class_id') }}
                    </p>
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-600">Class Name *</label>
                    <select
                        v-model="editForm.name"
                        class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                        :class="inputClass(editFrontendErrors, editForm, 'name')"
                    >
                        <option value="">Select class</option>
                        <option v-for="className in classOptions" :key="className" :value="className">
                            {{ className }}
                        </option>
                    </select>
                    <p v-if="fieldError(editFrontendErrors, editForm, 'name')" class="mt-1 text-xs font-bold text-rose-600">
                        {{ fieldError(editFrontendErrors, editForm, 'name') }}
                    </p>
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-600">Section *</label>
                    <select
                        v-model="editForm.section"
                        class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                        :class="inputClass(editFrontendErrors, editForm, 'section')"
                    >
                        <option value="">Select section</option>
                        <option v-for="section in sectionOptions" :key="section" :value="section">
                            {{ section }}
                        </option>
                    </select>
                    <p v-if="fieldError(editFrontendErrors, editForm, 'section')" class="mt-1 text-xs font-bold text-rose-600">
                        {{ fieldError(editFrontendErrors, editForm, 'section') }}
                    </p>
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-600">Class Teacher</label>
                    <input
                        v-model="editForm.teacher_name"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                    />
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-600">Room Number</label>
                    <input
                        v-model="editForm.room"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                    />
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-600">Student Capacity</label>
                    <input
                        v-model="editForm.capacity"
                        type="number"
                        class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                        :class="inputClass(editFrontendErrors, editForm, 'capacity')"
                    />
                    <p v-if="fieldError(editFrontendErrors, editForm, 'capacity')" class="mt-1 text-xs font-bold text-rose-600">
                        {{ fieldError(editFrontendErrors, editForm, 'capacity') }}
                    </p>
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-600">Students Count</label>
                    <input
                        v-model="editForm.students_count"
                        type="number"
                        class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                        :class="inputClass(editFrontendErrors, editForm, 'students_count')"
                    />
                    <p v-if="fieldError(editFrontendErrors, editForm, 'students_count')" class="mt-1 text-xs font-bold text-rose-600">
                        {{ fieldError(editFrontendErrors, editForm, 'students_count') }}
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
                @click="updateClass"
            >
                {{ editForm.processing ? 'Updating...' : 'Update Class' }}
            </button>
        </div>
    </div>
</div>
        </div>
    </AppLayout>
</template>