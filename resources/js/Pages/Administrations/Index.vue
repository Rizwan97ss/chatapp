<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import AppLayout from '@/Layouts/AppLayout.vue'
import Chart from 'chart.js/auto'

const props = defineProps({
    management: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            role: '',
            department: '',
            status: '',
        }),
    },
    managementStats: {
        type: Object,
        default: () => ({
            cards: [],
            overview: {},
            departmentBars: [],
        }),
    },
    roles: {
        type: Array,
        default: () => [],
    },
    departments: {
        type: Array,
        default: () => [],
    },
})

const managementList = computed(() => props.management || { data: [], links: [] })
const stats = computed(() => props.managementStats?.cards || [])
const overview = computed(() => props.managementStats?.overview || {})
const departmentBars = computed(() => props.managementStats?.departmentBars || [])

const defaultRoles = [
    'Principal',
    'Vice Principal',
    'Academic Director',
    'Finance Director',
    'HR & Admin Head',
    'Board Member',
]

const defaultDepartments = [
    'Principal Office',
    'Academic Affairs',
    'Finance',
    'Human Resources',
    'Board',
    'Administration',
]

const roleOptions = computed(() => [...new Set([...(props.roles || []), ...defaultRoles])])
const departmentOptions = computed(() => [...new Set([...(props.departments || []), ...defaultDepartments])])

const showCreateModal = ref(false)
const showViewModal = ref(false)
const showEditModal = ref(false)
const selectedMember = ref(null)

const importProgress = ref(0)
const isImporting = ref(false)

const filterForm = useForm({
    search: props.filters?.search || '',
    role: props.filters?.role || '',
    department: props.filters?.department || '',
    status: props.filters?.status || '',
})

const importForm = useForm({
    file: null,
})

const form = useForm({
    member_no: '',
    name: '',
    role: 'Principal',
    department: 'Principal Office',
    email: '',
    phone: '',
    authority: '',
    status: 'Active',
    responsibilities: '',
})

const editForm = useForm({
    member_no: '',
    name: '',
    role: 'Principal',
    department: 'Principal Office',
    email: '',
    phone: '',
    authority: '',
    status: 'Active',
    responsibilities: '',
})

const validateMember = (targetForm) => {
    const errors = {}

    if (!targetForm.member_no.trim()) errors.member_no = 'Member ID is required.'
    if (!targetForm.name.trim()) errors.name = 'Full name is required.'
    if (!targetForm.role.trim()) errors.role = 'Role is required.'
    if (!targetForm.department.trim()) errors.department = 'Department is required.'
    if (!targetForm.email.trim()) errors.email = 'Email is required.'

    if (targetForm.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(targetForm.email)) {
        errors.email = 'Please enter a valid email address.'
    }

    if (targetForm.responsibilities && targetForm.responsibilities.length > 1000) {
        errors.responsibilities = 'Responsibilities must not be greater than 1000 characters.'
    }

    return errors
}

const frontendErrors = computed(() => validateMember(form))
const editFrontendErrors = computed(() => validateMember(editForm))

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
    form.role = 'Principal'
    form.department = 'Principal Office'
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

const submitMember = () => {
    if (!canSubmit.value || form.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    form.post(route('management.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Management member created successfully.')
            resetForm()
            showCreateModal.value = false
        },
        onError: (errors) => {
            if (errors.server) return toast.error(errors.server)
            showFirstError(errors)
        },
    })
}

const openViewModal = (member) => {
    selectedMember.value = member
    showViewModal.value = true
}

const closeViewModal = () => {
    selectedMember.value = null
    showViewModal.value = false
}

const openEditModal = (member) => {
    selectedMember.value = member

    editForm.member_no = member.member_no || ''
    editForm.name = member.name || ''
    editForm.role = member.role || 'Principal'
    editForm.department = member.department || 'Principal Office'
    editForm.email = member.email || ''
    editForm.phone = member.phone || ''
    editForm.authority = member.authority || ''
    editForm.status = member.status || 'Active'
    editForm.responsibilities = member.responsibilities || ''

    editForm.clearErrors()
    showEditModal.value = true
}

const closeEditModal = () => {
    selectedMember.value = null
    editForm.clearErrors()
    showEditModal.value = false
}

const updateMember = () => {
    if (!selectedMember.value || !canUpdate.value || editForm.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    editForm.put(route('management.update', selectedMember.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Management member updated successfully.')
            closeEditModal()
        },
        onError: (errors) => {
            if (errors.edit_server) return toast.error(errors.edit_server)
            showFirstError(errors)
        },
    })
}

const deleteMember = (member) => {
    if (!confirm(`Delete member ${member.member_no}?`)) return

    router.delete(route('management.destroy', member.id), {
        preserveScroll: true,
        onSuccess: () => toast.success('Management member deleted successfully.'),
        onError: () => toast.error('Unable to delete management member.'),
    })
}

const applyFilters = () => {
    router.get(route('management'), {
        search: filterForm.search,
        role: filterForm.role,
        department: filterForm.department,
        status: filterForm.status,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

const resetFilters = () => {
    filterForm.search = ''
    filterForm.role = ''
    filterForm.department = ''
    filterForm.status = ''

    router.get(route('management'), {}, {
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

    importForm.post(route('management.import'), {
        preserveScroll: true,
        forceFormData: true,
        onProgress: (progress) => {
            if (progress?.percentage) importProgress.value = progress.percentage
        },
        onSuccess: () => {
            toast.success('Management members imported successfully.')
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

//define roleChart
const roleChart = computed(() => props.roleChart || []);
const roleChartRef = ref(null)
let roleChartInstance = null

const renderRoleChart = () => {
    if (!roleChartRef.value) return

    if (roleChartInstance) {
        roleChartInstance.destroy()
    }

    roleChartInstance = new Chart(roleChartRef.value, {
        type: 'doughnut',
        data: {
            labels: roleChart.value.map(i => i.label),
            datasets: [
                {
                    data: roleChart.value.map(i => i.value),
                    borderWidth: 0,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: (ctx) => `${ctx.label}: ${ctx.raw}`,
                    },
                },
            },
        },
    })
}

onMounted(renderRoleChart)
watch(roleChart, renderRoleChart)
</script>

<template>
    <AppLayout title="Administration">
        <div class="space-y-6">
            <div
                class="rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 ring-1 ring-slate-200 sm:p-6 lg:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">Higher Management</p>
                        <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">Administration
                        </h1>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                            Manage school leadership, board members, administrative heads, approvals, policies and
                            strategic decisions.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <form
                            class="flex flex-col gap-3 rounded-3xl border border-emerald-100 bg-emerald-50/60 p-3 shadow-sm sm:flex-row sm:items-center"
                            @submit.prevent="submitImport">
                            <label
                                class="flex cursor-pointer items-center gap-3 rounded-2xl border border-dashed border-emerald-300 bg-white px-4 py-3 text-sm font-bold text-slate-700 transition hover:border-emerald-400 hover:bg-emerald-50">
                                <span
                                    class="flex size-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">📄</span>
                                <span class="min-w-0">
                                    <span class="block truncate">
                                        {{ importForm.file ? importForm.file.name : 'Choose Excel file' }}
                                    </span>
                                    <span class="block text-xs font-semibold text-slate-400">
                                        XLSX, XLS or CSV up to 5MB
                                    </span>
                                </span>
                                <input type="file" accept=".xlsx,.xls,.csv" class="hidden"
                                    @change="importForm.file = $event.target.files[0]" />
                            </label>

                            <button type="submit"
                                class="rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-emerald-100 transition hover:-translate-y-0.5 hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                                :disabled="importForm.processing || isImporting">
                                {{ isImporting ? `Importing ${importProgress}%` : 'Import Excel' }}
                            </button>
                        </form>

                        <button type="button"
                            class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800"
                            @click="openCreateModal">
                            Add Member
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="isImporting" class="rounded-2xl bg-white p-3 shadow-sm ring-1 ring-slate-200">
                <div class="h-2 overflow-hidden rounded-full bg-emerald-100">
                    <div class="h-full rounded-full bg-emerald-600 transition-all duration-300"
                        :style="{ width: `${importProgress}%` }"></div>
                </div>
            </div>

  


            <div class="grid gap-6 xl:grid-cols-3">
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 xl:col-span-2">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Management Overview</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Quick summary of leadership structure and member activity.
                            </p>
                        </div>

                        <span
                            class="w-fit rounded-full bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                            {{ overview.active_percentage || 0 }}% Active
                        </span>
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-3xl bg-indigo-50 p-5 ring-1 ring-indigo-100">
                            <p class="text-xs font-bold uppercase tracking-wide text-indigo-500">
                                Total Members
                            </p>
                            <h3 class="mt-3 text-3xl font-black text-slate-950">
                                {{ overview.total || 0 }}
                            </h3>
                            <p class="mt-2 text-xs font-semibold text-slate-500">
                                Registered leadership members
                            </p>
                        </div>

                        <div class="rounded-3xl bg-emerald-50 p-5 ring-1 ring-emerald-100">
                            <p class="text-xs font-bold uppercase tracking-wide text-emerald-500">
                                Active Members
                            </p>
                            <h3 class="mt-3 text-3xl font-black text-slate-950">
                                {{ overview.active || 0 }}
                            </h3>
                            <p class="mt-2 text-xs font-semibold text-slate-500">
                                Currently active members
                            </p>
                        </div>

                        <div class="rounded-3xl bg-rose-50 p-5 ring-1 ring-rose-100">
                            <p class="text-xs font-bold uppercase tracking-wide text-rose-500">
                                Inactive Members
                            </p>
                            <h3 class="mt-3 text-3xl font-black text-slate-950">
                                {{ overview.inactive || 0 }}
                            </h3>
                            <p class="mt-2 text-xs font-semibold text-slate-500">
                                Temporarily inactive records
                            </p>
                        </div>

                        <div class="rounded-3xl bg-amber-50 p-5 ring-1 ring-amber-100">
                            <p class="text-xs font-bold uppercase tracking-wide text-amber-500">
                                Departments
                            </p>
                            <h3 class="mt-3 text-3xl font-black text-slate-950">
                                {{ overview.departments || 0 }}
                            </h3>
                            <p class="mt-2 text-xs font-semibold text-slate-500">
                                Departments represented
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4 lg:grid-cols-2">
                        <div class="rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-black text-slate-900">
                                        Leadership Health
                                    </h3>
                                    <p class="mt-1 text-xs font-semibold text-slate-500">
                                        Active vs total management members
                                    </p>
                                </div>

                                <span
                                    class="rounded-full bg-white px-3 py-1 text-xs font-black text-slate-700 ring-1 ring-slate-200">
                                    {{ overview.active_percentage || 0 }}%
                                </span>
                            </div>

                            <div class="mt-5 h-3 overflow-hidden rounded-full bg-slate-200">
                                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-700"
                                    :style="{ width: `${overview.active_percentage || 0}%` }"></div>
                            </div>

                            <p class="mt-3 text-xs font-semibold text-slate-500">
                                {{ overview.active || 0 }} of {{ overview.total || 0 }} members are active.
                            </p>
                        </div>

                        <div class="rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-100">
                            <h3 class="text-sm font-black text-slate-900">
                                Quick Actions
                            </h3>

                            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                <button type="button"
                                    class="rounded-2xl bg-white px-4 py-3 text-left text-sm font-bold text-slate-700 ring-1 ring-slate-200 transition hover:bg-slate-100"
                                    @click="openCreateModal">
                                    + Add Member
                                </button>

                                <button type="button"
                                    class="rounded-2xl bg-white px-4 py-3 text-left text-sm font-bold text-slate-700 ring-1 ring-slate-200 transition hover:bg-slate-100"
                                    @click="filterForm.status = 'Active'; applyFilters()">
                                    View Active
                                </button>

                                <button type="button"
                                    class="rounded-2xl bg-white px-4 py-3 text-left text-sm font-bold text-slate-700 ring-1 ring-slate-200 transition hover:bg-slate-100"
                                    @click="filterForm.status = 'Inactive'; applyFilters()">
                                    View Inactive
                                </button>

                                <button type="button"
                                    class="rounded-2xl bg-white px-4 py-3 text-left text-sm font-bold text-slate-700 ring-1 ring-slate-200 transition hover:bg-slate-100"
                                    @click="resetFilters">
                                    Clear Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="rounded-3xl bg-gradient-to-br from-indigo-600 to-sky-500 p-6 text-white shadow-lg shadow-indigo-100">
                    <h3 class="text-lg font-black">Management Summary</h3>
                    <p class="mt-1 text-sm text-white/75">Current overview</p>

                    <div class="mt-6 space-y-4">
                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Active Members</p>
                            <p class="mt-1 text-xl font-black">{{ overview.active || 0 }} Members</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Inactive Members</p>
                            <p class="mt-1 text-xl font-black">{{ overview.inactive || 0 }} Members</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Departments</p>
                            <p class="mt-1 text-xl font-black">{{ overview.departments || 0 }} Departments</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <form class="grid gap-4 md:grid-cols-2 xl:grid-cols-6" @submit.prevent="applyFilters">
                    <div class="xl:col-span-2">
                        <label class="text-sm font-semibold text-slate-600">Search Member</label>
                        <input v-model="filterForm.search" type="text"
                            placeholder="Search by name, role, department or authority"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Role</label>
                        <select v-model="filterForm.role"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option value="">All Roles</option>
                            <option v-for="role in roleOptions" :key="role" :value="role">{{ role }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Department</label>
                        <select v-model="filterForm.department"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option value="">All Departments</option>
                            <option v-for="department in departmentOptions" :key="department" :value="department">
                                {{ department }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Status</label>
                        <select v-model="filterForm.status"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option value="">All Status</option>
                            <option>Active</option>
                            <option>Inactive</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="w-full rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-100 transition hover:bg-indigo-700">
                            Filter
                        </button>
                        <button type="button"
                            class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50"
                            @click="resetFilters">
                            Reset
                        </button>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
                <div
                    class="flex flex-col gap-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">Higher Management Records</h2>
                        <p class="mt-1 text-sm text-slate-500">Complete school leadership and administration records.
                        </p>
                    </div>

                    <button type="button"
                        class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800"
                        @click="openCreateModal">
                        New Member
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1150px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Member</th>
                                <th class="px-6 py-4">ID</th>
                                <th class="px-6 py-4">Role</th>
                                <th class="px-6 py-4">Department</th>
                                <th class="px-6 py-4">Email</th>
                                <th class="px-6 py-4">Phone</th>
                                <th class="px-6 py-4">Authority</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="member in managementList.data" :key="member.id"
                                class="transition hover:bg-indigo-50/40">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex size-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 to-sky-100 text-sm font-black text-indigo-700">
                                            {{ member.name?.charAt(0) || 'M' }}
                                        </div>

                                        <div>
                                            <p class="font-bold text-slate-950">{{ member.name }}</p>
                                            <p class="text-xs text-slate-400">{{ member.role }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-700">{{ member.member_no }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ member.role }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ member.department }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ member.email }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ member.phone || '-' }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-800">{{ member.authority || '-' }}</td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold ring-1" :class="member.status === 'Active'
                                        ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                        : 'bg-rose-50 text-rose-700 ring-rose-100'">
                                        {{ member.status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button type="button"
                                            class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200"
                                            @click="openViewModal(member)">
                                            View
                                        </button>
                                        <button type="button"
                                            class="rounded-xl bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100"
                                            @click="openEditModal(member)">
                                            Edit
                                        </button>
                                        <button type="button"
                                            class="rounded-xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100"
                                            @click="deleteMember(member)">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="managementList.data.length === 0">
                                <td colspan="9" class="px-6 py-10 text-center text-sm font-bold text-slate-400">
                                    No management members found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="managementList.total > managementList.per_page"
                    class="flex flex-col gap-4 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm font-semibold text-slate-500">
                        Showing <span class="font-black text-slate-800">{{ managementList.from || 0 }}</span>
                        to <span class="font-black text-slate-800">{{ managementList.to || 0 }}</span>
                        of <span class="font-black text-slate-800">{{ managementList.total || 0 }}</span> members
                    </p>

                    <div class="flex flex-wrap gap-2">
                        <Link v-for="(link, index) in managementList.links" :key="index" :href="link.url || '#'"
                            preserve-scroll preserve-state class="rounded-xl px-4 py-2 text-sm font-bold transition"
                            :class="[
                                link.active ? 'bg-slate-950 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-100',
                                !link.url ? 'pointer-events-none cursor-not-allowed opacity-40' : '',
                            ]" v-html="link.label" />
                    </div>
                </div>
            </div>

            <!-- Create Modal -->
            <div v-if="showCreateModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
                <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div
                        class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">Add Management Member</h2>
                            <p class="mt-1 text-sm text-slate-500">Add school leadership member, department, authority
                                and status.</p>
                        </div>

                        <button type="button"
                            class="rounded-2xl bg-white p-2 text-slate-400 shadow-sm ring-1 ring-slate-200 transition hover:text-slate-700"
                            @click="closeCreateModal">
                            ✕
                        </button>
                    </div>

                    <div class="max-h-[75vh] overflow-y-auto p-5 sm:p-6">
                        <p v-if="form.errors.server"
                            class="mb-5 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-bold text-rose-600">
                            {{ form.errors.server }}
                        </p>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="text-sm font-semibold text-slate-600">Full Name *</label>
                                <input v-model="form.name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'name')" placeholder="Dr. Salman Ahmed" />
                                <p v-if="fieldError(frontendErrors, form, 'name')"
                                    class="mt-1 text-xs font-bold text-rose-600">{{ fieldError(frontendErrors, form,
                                        'name') }}</p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Member ID *</label>
                                <input v-model="form.member_no"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'member_no')" placeholder="ADM-1005" />
                                <p v-if="fieldError(frontendErrors, form, 'member_no')"
                                    class="mt-1 text-xs font-bold text-rose-600">{{ fieldError(frontendErrors, form,
                                        'member_no') }}</p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Role *</label>
                                <select v-model="form.role"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option v-for="role in defaultRoles" :key="role">{{ role }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Department *</label>
                                <select v-model="form.department"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option v-for="department in defaultDepartments" :key="department">{{ department }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Email *</label>
                                <input v-model="form.email" type="email"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'email')"
                                    placeholder="member@example.com" />
                                <p v-if="fieldError(frontendErrors, form, 'email')"
                                    class="mt-1 text-xs font-bold text-rose-600">{{ fieldError(frontendErrors, form,
                                        'email') }}</p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Phone</label>
                                <input v-model="form.phone"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                    placeholder="+971 50 123 4567" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Authority Level</label>
                                <input v-model="form.authority"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                    placeholder="Full Academic & Admin Control" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status</label>
                                <select v-model="form.status"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>Active</option>
                                    <option>Inactive</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Responsibilities</label>
                                <textarea v-model="form.responsibilities" rows="3"
                                    placeholder="Leadership responsibilities, approval rights, policy control..."
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'responsibilities')"></textarea>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 p-5 sm:flex-row sm:justify-end sm:p-6">
                        <button type="button"
                            class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                            @click="closeCreateModal">
                            Cancel
                        </button>
                        <button type="button"
                            class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="form.processing || !canSubmit" @click="submitMember">
                            {{ form.processing ? 'Saving...' : 'Save Member' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- View Modal -->
            <div v-if="showViewModal && selectedMember"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
                <div class="w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div
                        class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">{{ selectedMember.name }}</h2>
                            <p class="mt-1 text-sm font-semibold text-slate-500">
                                {{ selectedMember.member_no }} · {{ selectedMember.role }}
                            </p>
                        </div>

                        <button type="button"
                            class="rounded-2xl bg-white p-2 text-slate-400 shadow-sm ring-1 ring-slate-200 transition hover:text-slate-700"
                            @click="closeViewModal">
                            ✕
                        </button>
                    </div>

                    <div class="max-h-[75vh] overflow-y-auto p-5 sm:p-6">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl bg-indigo-50 p-4 ring-1 ring-indigo-100">
                                <p class="text-xs font-bold uppercase text-indigo-500">Role</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedMember.role }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Department</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedMember.department }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Email</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedMember.email }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Phone</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedMember.phone || '-' }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100 sm:col-span-2">
                                <p class="text-xs font-bold uppercase text-slate-400">Authority</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedMember.authority || '-' }}
                                </p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100 sm:col-span-2">
                                <p class="text-xs font-bold uppercase text-slate-400">Responsibilities</p>
                                <p class="mt-1 text-sm font-bold leading-6 text-slate-800">{{
                                    selectedMember.responsibilities || '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 p-5 sm:p-6">
                        <button type="button"
                            class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                            @click="closeViewModal">
                            Close
                        </button>
                        <button type="button"
                            class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800"
                            @click="openEditModal(selectedMember); showViewModal = false">
                            Edit Member
                        </button>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div v-if="showEditModal && selectedMember"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
                <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div
                        class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">Edit Management Member</h2>
                            <p class="mt-1 text-sm text-slate-500">Update leadership member details.</p>
                        </div>

                        <button type="button"
                            class="rounded-2xl bg-white p-2 text-slate-400 shadow-sm ring-1 ring-slate-200 transition hover:text-slate-700"
                            @click="closeEditModal">
                            ✕
                        </button>
                    </div>

                    <div class="max-h-[75vh] overflow-y-auto p-5 sm:p-6">
                        <p v-if="editForm.errors.edit_server"
                            class="mb-5 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-bold text-rose-600">
                            {{ editForm.errors.edit_server }}
                        </p>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="text-sm font-semibold text-slate-600">Full Name *</label>
                                <input v-model="editForm.name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'name')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Member ID *</label>
                                <input v-model="editForm.member_no"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'member_no')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Role *</label>
                                <select v-model="editForm.role"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option v-for="role in defaultRoles" :key="role">{{ role }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Department *</label>
                                <select v-model="editForm.department"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option v-for="department in defaultDepartments" :key="department">{{ department }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Email *</label>
                                <input v-model="editForm.email" type="email"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'email')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Phone</label>
                                <input v-model="editForm.phone"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Authority Level</label>
                                <input v-model="editForm.authority"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status</label>
                                <select v-model="editForm.status"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>Active</option>
                                    <option>Inactive</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Responsibilities</label>
                                <textarea v-model="editForm.responsibilities" rows="3"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'responsibilities')"></textarea>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 p-5 sm:flex-row sm:justify-end sm:p-6">
                        <button type="button"
                            class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                            @click="closeEditModal">
                            Cancel
                        </button>
                        <button type="button"
                            class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="editForm.processing || !canUpdate" @click="updateMember">
                            {{ editForm.processing ? 'Updating...' : 'Update Member' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>