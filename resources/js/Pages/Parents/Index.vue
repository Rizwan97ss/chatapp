<script setup>
import { ref, computed } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    parents: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            relation: '',
            class_name: '',
            status: '',
        }),
    },
    parentStats: {
        type: Object,
        default: () => ({
            cards: [],
            overview: {},
            engagementBars: [],
        }),
    },
    classNames: {
        type: Array,
        default: () => [],
    },
})

const parentList = computed(() => props.parents || { data: [], links: [] })
const statCards = computed(() => props.parentStats?.cards || [])
const overview = computed(() => props.parentStats?.overview || {})
const engagementBars = computed(() => props.parentStats?.engagementBars || [])

const defaultClasses = ['Grade 6', 'Grade 8', 'Grade 10', 'Grade 11']
const classOptions = computed(() => [...new Set([...(props.classNames || []), ...defaultClasses])])

const showCreateModal = ref(false)
const showViewModal = ref(false)
const showEditModal = ref(false)
const selectedParent = ref(null)

const filterForm = useForm({
    search: props.filters?.search || '',
    relation: props.filters?.relation || '',
    class_name: props.filters?.class_name || '',
    status: props.filters?.status || '',
})

const importForm = useForm({
    file: null,
})

const importProgress = ref(0)
const isImporting = ref(false)

const form = useForm({
    parent_no: '',
    full_name: '',
    relation: 'Father',
    student_name: '',
    class_name: '',
    phone: '',
    email: '',
    occupation: '',
    status: 'Active',
    address: '',
})

const editForm = useForm({
    parent_no: '',
    full_name: '',
    relation: 'Father',
    student_name: '',
    class_name: '',
    phone: '',
    email: '',
    occupation: '',
    status: 'Active',
    address: '',
})

const validateParent = (targetForm) => {
    const errors = {}

    if (!targetForm.parent_no.trim()) errors.parent_no = 'Parent ID is required.'
    if (!targetForm.full_name.trim()) errors.full_name = 'Full name is required.'
    if (!targetForm.student_name.trim()) errors.student_name = 'Student name is required.'
    if (!targetForm.class_name) errors.class_name = 'Class is required.'
    if (!targetForm.phone.trim()) errors.phone = 'Phone number is required.'

    if (targetForm.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(targetForm.email)) {
        errors.email = 'Email must be valid.'
    }

    if (targetForm.address && targetForm.address.length > 1000) {
        errors.address = 'Address must not be greater than 1000 characters.'
    }

    return errors
}

const frontendErrors = computed(() => validateParent(form))
const editFrontendErrors = computed(() => validateParent(editForm))

const canSubmit = computed(() => Object.keys(frontendErrors.value).length === 0)
const canUpdate = computed(() => Object.keys(editFrontendErrors.value).length === 0)

const fieldError = (errors, targetForm, field) => errors[field] || targetForm.errors[field]

const inputClass = (errors, targetForm, field) => {
    return fieldError(errors, targetForm, field)
        ? 'border-rose-300 bg-rose-50 focus:border-rose-400 focus:ring-rose-100'
        : 'border-slate-200 bg-white focus:border-indigo-400 focus:ring-indigo-100'
}

const showFirstError = (errors, fallback = 'Please check the form errors.') => {
    const firstError = Object.values(errors || {})[0]
    toast.error(firstError || fallback)
}

const resetForm = () => {
    form.reset()
    form.relation = 'Father'
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

const submitParent = () => {
    if (!canSubmit.value || form.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    form.post(route('parents.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Parent created successfully.')
            resetForm()
            showCreateModal.value = false
        },
        onError: (errors) => {
            if (errors.server) return toast.error(errors.server)
            showFirstError(errors)
        },
    })
}

const openViewModal = (parent) => {
    selectedParent.value = parent
    showViewModal.value = true
}

const closeViewModal = () => {
    selectedParent.value = null
    showViewModal.value = false
}

const openEditModal = (parent) => {
    selectedParent.value = parent

    editForm.parent_no = parent.parent_no || ''
    editForm.full_name = parent.full_name || ''
    editForm.relation = parent.relation || 'Father'
    editForm.student_name = parent.student_name || ''
    editForm.class_name = parent.class_name || ''
    editForm.phone = parent.phone || ''
    editForm.email = parent.email || ''
    editForm.occupation = parent.occupation || ''
    editForm.status = parent.status || 'Active'
    editForm.address = parent.address || ''

    editForm.clearErrors()
    showEditModal.value = true
}

const closeEditModal = () => {
    selectedParent.value = null
    editForm.clearErrors()
    showEditModal.value = false
}

const updateParent = () => {
    if (!selectedParent.value || !canUpdate.value || editForm.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    editForm.put(route('parents.update', selectedParent.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Parent updated successfully.')
            closeEditModal()
        },
        onError: (errors) => {
            if (errors.edit_server) return toast.error(errors.edit_server)
            showFirstError(errors)
        },
    })
}

const deleteParent = (parent) => {
    if (!confirm(`Delete parent ${parent.parent_no}?`)) return

    router.delete(route('parents.destroy', parent.id), {
        preserveScroll: true,
        onSuccess: () => toast.success('Parent deleted successfully.'),
        onError: () => toast.error('Unable to delete parent.'),
    })
}

const applyFilters = () => {
    router.get(route('parents'), {
        search: filterForm.search,
        relation: filterForm.relation,
        class_name: filterForm.class_name,
        status: filterForm.status,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

const resetFilters = () => {
    filterForm.search = ''
    filterForm.relation = ''
    filterForm.class_name = ''
    filterForm.status = ''

    router.get(route('parents'), {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

const submitImport = () => {
    if (!importForm.file || importForm.processing) {
        toast.error('Please select an Excel file.')
        return
    }

    isImporting.value = true
    importProgress.value = 0

    importForm.post(route('parents.import'), {
        preserveScroll: true,
        forceFormData: true,

        onProgress: (progress) => {
            if (progress?.percentage) importProgress.value = progress.percentage
        },

        onSuccess: () => {
            toast.success('Parents imported successfully.')
            importForm.reset()
            importProgress.value = 0
        },

        onError: (errors) => {
            if (errors.import) return toast.error(errors.import)
            showFirstError(errors, 'Import failed. Please check your file.')
        },

        onFinish: () => {
            isImporting.value = false
        },
    })
}
</script>
<template>
    <AppLayout title="Parents">
        <div class="space-y-6">
            <!-- Header -->
            <div class="rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 ring-1 ring-slate-200 sm:p-6 lg:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">Parent Management</p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                            Parents
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                            Manage parent and guardian profiles, linked students, contact details, occupations and account status.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <form
                            class="flex flex-col gap-3 rounded-3xl border border-emerald-100 bg-emerald-50/60 p-3 shadow-sm sm:flex-row sm:items-center"
                            @submit.prevent="submitImport"
                        >
                            <label
                                class="flex cursor-pointer items-center gap-3 rounded-2xl border border-dashed border-emerald-300 bg-white px-4 py-3 text-sm font-bold text-slate-700 transition hover:border-emerald-400 hover:bg-emerald-50"
                            >
                                <span class="flex size-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                                    📄
                                </span>

                                <span class="min-w-0">
                                    <span class="block truncate">
                                        {{ importForm.file ? importForm.file.name : 'Choose Excel file' }}
                                    </span>
                                    <span class="block text-xs font-semibold text-slate-400">
                                        XLSX, XLS or CSV up to 5MB
                                    </span>
                                </span>

                                <input
                                    type="file"
                                    accept=".xlsx,.xls,.csv"
                                    class="hidden"
                                    @change="importForm.file = $event.target.files[0]"
                                />
                            </label>

                            <button
                                type="submit"
                                class="rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-emerald-100 transition hover:-translate-y-0.5 hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                                :disabled="importForm.processing || isImporting"
                            >
                                {{ isImporting ? `Importing ${importProgress}%` : 'Import Excel' }}
                            </button>

                            <div v-if="isImporting" class="sm:w-40">
                                <div class="h-2 overflow-hidden rounded-full bg-emerald-100">
                                    <div
                                        class="h-full rounded-full bg-emerald-600 transition-all duration-300"
                                        :style="{ width: `${importProgress}%` }"
                                    ></div>
                                </div>
                            </div>
                        </form>

                        <button
                            type="button"
                            class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800"
                            @click="openCreateModal"
                        >
                            Add Parent
                        </button>
                    </div>
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
                            <p class="text-sm font-semibold text-slate-500">
                                {{ stat.label }}
                            </p>

                            <h2 class="mt-3 text-2xl font-black text-slate-950 xl:text-3xl">
                                {{ stat.value }}
                            </h2>
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

            <!-- Filters -->
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <form class="grid gap-4 md:grid-cols-2 xl:grid-cols-6" @submit.prevent="applyFilters">
                    <div class="xl:col-span-2">
                        <label class="text-sm font-semibold text-slate-600">Search Parent</label>
                        <input
                            v-model="filterForm.search"
                            type="text"
                            placeholder="Search by parent, student, phone or email"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        />
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Relation</label>
                        <select
                            v-model="filterForm.relation"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        >
                            <option value="">All Relations</option>
                            <option>Father</option>
                            <option>Mother</option>
                            <option>Guardian</option>
                        </select>
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
                        <label class="text-sm font-semibold text-slate-600">Status</label>
                        <select
                            v-model="filterForm.status"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        >
                            <option value="">All Status</option>
                            <option>Active</option>
                            <option>Pending</option>
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

            <!-- Overview -->
            <div class="grid gap-6 xl:grid-cols-3">
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 xl:col-span-2">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Parent Engagement Overview</h2>
                            <p class="mt-1 text-sm text-slate-500">Parent distribution percentage by class.</p>
                        </div>

                        <span class="w-fit rounded-full bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                            {{ overview.active_percentage || 0 }}% Active
                        </span>
                    </div>

                    <div class="mt-8 space-y-5">
                        <div v-for="item in engagementBars" :key="item.label">
                            <div class="mb-2 flex items-center justify-between">
                                <p class="text-sm font-bold text-slate-700">
                                    {{ item.label }}
                                </p>
                                <p class="text-sm font-bold text-slate-500">
                                    {{ item.value }}%
                                </p>
                            </div>

                            <div class="h-3 overflow-hidden rounded-full bg-slate-100">
                                <div
                                    class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-sky-400"
                                    :style="{ width: `${item.value}%` }"
                                ></div>
                            </div>
                        </div>

                        <p v-if="engagementBars.length === 0" class="rounded-2xl bg-slate-50 p-5 text-sm font-bold text-slate-400">
                            No class engagement data available.
                        </p>
                    </div>
                </div>

                <div class="rounded-3xl bg-gradient-to-br from-indigo-600 to-sky-500 p-6 text-white shadow-lg shadow-indigo-100">
                    <h3 class="text-lg font-black">Parent Summary</h3>

                    <p class="mt-1 text-sm text-white/75">Current overview</p>

                    <div class="mt-6 space-y-4">
                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Active Accounts</p>
                            <p class="mt-1 text-xl font-black">{{ overview.active || 0 }} Parents</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Pending Profiles</p>
                            <p class="mt-1 text-xl font-black">{{ overview.pending || 0 }} Profiles</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Inactive</p>
                            <p class="mt-1 text-xl font-black">{{ overview.inactive || 0 }} Parents</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Parent Cards -->
            <!-- <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <div
                    v-for="parent in parentList.data"
                    :key="parent.id"
                    class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-lg"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                {{ parent.parent_no }}
                            </p>

                            <h2 class="mt-2 text-lg font-black text-slate-950">
                                {{ parent.full_name }}
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                {{ parent.relation }} of {{ parent.student_name }}
                            </p>
                        </div>

                        <span
                            class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                            :class="
                                parent.status === 'Active'
                                    ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                    : parent.status === 'Pending'
                                        ? 'bg-amber-50 text-amber-700 ring-amber-100'
                                        : 'bg-rose-50 text-rose-700 ring-rose-100'
                            "
                        >
                            {{ parent.status }}
                        </span>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-indigo-50 p-4">
                            <p class="text-xs font-semibold text-indigo-500">Student</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ parent.student_name }}</p>
                        </div>

                        <div class="rounded-2xl bg-sky-50 p-4">
                            <p class="text-xs font-semibold text-sky-500">Class</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ parent.class_name }}</p>
                        </div>

                        <div class="rounded-2xl bg-violet-50 p-4">
                            <p class="text-xs font-semibold text-violet-500">Phone</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ parent.phone }}</p>
                        </div>

                        <div class="rounded-2xl bg-amber-50 p-4">
                            <p class="text-xs font-semibold text-amber-500">Occupation</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ parent.occupation || '-' }}</p>
                        </div>
                    </div>

                    <div class="mt-5 flex gap-2">
                        <button
                            type="button"
                            class="flex-1 rounded-2xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                            @click="openViewModal(parent)"
                        >
                            View
                        </button>

                        <button
                            type="button"
                            class="flex-1 rounded-2xl bg-indigo-50 px-4 py-2.5 text-sm font-bold text-indigo-700 transition hover:bg-indigo-100"
                            @click="openEditModal(parent)"
                        >
                            Edit
                        </button>
                    </div>
                </div>

                <div
                    v-if="parentList.data.length === 0"
                    class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 md:col-span-2 xl:col-span-4"
                >
                    <p class="text-sm font-bold text-slate-900">No parents found</p>
                    <p class="mt-1 text-xs text-slate-500">Add parent profiles to see records.</p>
                </div>
            </div> -->

            <!-- Parents Table -->
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">Parent Records</h2>
                        <p class="mt-1 text-sm text-slate-500">Complete parent and guardian records.</p>
                    </div>

                    <button
                        type="button"
                        class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800"
                        @click="openCreateModal"
                    >
                        New Parent
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1100px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Parent</th>
                                <th class="px-6 py-4">Parent ID</th>
                                <th class="px-6 py-4">Relation</th>
                                <th class="px-6 py-4">Student</th>
                                <th class="px-6 py-4">Class</th>
                                <th class="px-6 py-4">Phone</th>
                                <th class="px-6 py-4">Occupation</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="parent in parentList.data"
                                :key="parent.id"
                                class="transition hover:bg-indigo-50/40"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 to-sky-100 text-sm font-black text-indigo-700">
                                            {{ parent.full_name?.charAt(0) || 'P' }}
                                        </div>

                                        <div>
                                            <p class="font-bold text-slate-950">
                                                {{ parent.full_name }}
                                            </p>
                                            <p class="text-xs text-slate-400">
                                                {{ parent.email || '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-700">{{ parent.parent_no }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ parent.relation }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ parent.student_name }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ parent.class_name }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ parent.phone }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ parent.occupation || '-' }}</td>

                                <td class="px-6 py-4">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                                        :class="
                                            parent.status === 'Active'
                                                ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                                : parent.status === 'Pending'
                                                    ? 'bg-amber-50 text-amber-700 ring-amber-100'
                                                    : 'bg-rose-50 text-rose-700 ring-rose-100'
                                        "
                                    >
                                        {{ parent.status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            type="button"
                                            class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200"
                                            @click="openViewModal(parent)"
                                        >
                                            View
                                        </button>

                                        <button
                                            type="button"
                                            class="rounded-xl bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100"
                                            @click="openEditModal(parent)"
                                        >
                                            Edit
                                        </button>

                                        <button
                                            type="button"
                                            class="rounded-xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100"
                                            @click="deleteParent(parent)"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="parentList.data.length === 0">
                                <td colspan="9" class="px-6 py-10 text-center text-sm font-bold text-slate-400">
                                    No parents found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    v-if="parentList.total > parentList.per_page"
                    class="flex flex-col gap-4 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <p class="text-sm font-semibold text-slate-500">
                        Showing
                        <span class="font-black text-slate-800">{{ parentList.from || 0 }}</span>
                        to
                        <span class="font-black text-slate-800">{{ parentList.to || 0 }}</span>
                        of
                        <span class="font-black text-slate-800">{{ parentList.total || 0 }}</span>
                        parents
                    </p>

                    <div class="flex flex-wrap gap-2">
                        <Link
                            v-for="(link, index) in parentList.links"
                            :key="index"
                            :href="link.url || '#'"
                            preserve-scroll
                            preserve-state
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
                            <h2 class="text-xl font-black text-slate-950">Add Parent / Guardian</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Add parent contact details, linked student, relation and profile status.
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
                                <label class="text-sm font-semibold text-slate-600">Full Name *</label>
                                <input
                                    v-model="form.full_name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'full_name')"
                                    placeholder="Mohammed Ali"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'full_name')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'full_name') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Parent ID *</label>
                                <input
                                    v-model="form.parent_no"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'parent_no')"
                                    placeholder="PAR-1005"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'parent_no')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'parent_no') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Relation</label>
                                <select
                                    v-model="form.relation"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option>Father</option>
                                    <option>Mother</option>
                                    <option>Guardian</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Linked Student *</label>
                                <input
                                    v-model="form.student_name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'student_name')"
                                    placeholder="Ahmed Ali"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'student_name')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'student_name') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Class *</label>
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
                                <label class="text-sm font-semibold text-slate-600">Phone *</label>
                                <input
                                    v-model="form.phone"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'phone')"
                                    placeholder="+971 50 123 4567"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'phone')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'phone') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Email</label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'email')"
                                    placeholder="parent@example.com"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'email')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'email') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Occupation</label>
                                <input
                                    v-model="form.occupation"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                    placeholder="Engineer"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status</label>
                                <select
                                    v-model="form.status"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option>Active</option>
                                    <option>Pending</option>
                                    <option>Inactive</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Address</label>
                                <textarea
                                    v-model="form.address"
                                    rows="3"
                                    placeholder="Parent address"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'address')"
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
                            @click="submitParent"
                        >
                            {{ form.processing ? 'Saving...' : 'Save Parent' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- View Modal -->
            <div
                v-if="showViewModal && selectedParent"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
            >
                <div class="w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">{{ selectedParent.full_name }}</h2>
                            <p class="mt-1 text-sm font-semibold text-slate-500">
                                {{ selectedParent.parent_no }} · {{ selectedParent.relation }}
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
                                <p class="text-xs font-bold uppercase text-indigo-500">Student</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedParent.student_name }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Class</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedParent.class_name }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Phone</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedParent.phone }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Email</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedParent.email || '-' }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Occupation</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedParent.occupation || '-' }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Status</p>
                                <p class="mt-1">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                                        :class="
                                            selectedParent.status === 'Active'
                                                ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                                : selectedParent.status === 'Pending'
                                                    ? 'bg-amber-50 text-amber-700 ring-amber-100'
                                                    : 'bg-rose-50 text-rose-700 ring-rose-100'
                                        "
                                    >
                                        {{ selectedParent.status }}
                                    </span>
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100 sm:col-span-2">
                                <p class="text-xs font-bold uppercase text-slate-400">Address</p>
                                <p class="mt-1 text-sm font-bold leading-6 text-slate-800">
                                    {{ selectedParent.address || '-' }}
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
                            @click="openEditModal(selectedParent); showViewModal = false"
                        >
                            Edit Parent
                        </button>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div
                v-if="showEditModal && selectedParent"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
            >
                <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">Edit Parent / Guardian</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Update parent contact details, linked student, relation and profile status.
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
                                <label class="text-sm font-semibold text-slate-600">Full Name *</label>
                                <input
                                    v-model="editForm.full_name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'full_name')"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Parent ID *</label>
                                <input
                                    v-model="editForm.parent_no"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'parent_no')"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Relation</label>
                                <select
                                    v-model="editForm.relation"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option>Father</option>
                                    <option>Mother</option>
                                    <option>Guardian</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Linked Student *</label>
                                <input
                                    v-model="editForm.student_name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'student_name')"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Class *</label>
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
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Phone *</label>
                                <input
                                    v-model="editForm.phone"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'phone')"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Email</label>
                                <input
                                    v-model="editForm.email"
                                    type="email"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'email')"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Occupation</label>
                                <input
                                    v-model="editForm.occupation"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status</label>
                                <select
                                    v-model="editForm.status"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option>Active</option>
                                    <option>Pending</option>
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
                            @click="updateParent"
                        >
                            {{ editForm.processing ? 'Updating...' : 'Update Parent' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>