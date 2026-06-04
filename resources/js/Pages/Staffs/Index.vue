<script setup>
import { ref, computed } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    staffs: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            department: '',
            shift: '',
            status: '',
        }),
    },
    staffStats: {
        type: Object,
        default: () => ({
            cards: [],
            overview: {},
            departmentBars: [],
        }),
    },
    departments: {
        type: Array,
        default: () => [],
    },
    roles: {
        type: Array,
        default: () => [],
    },
})

const staffList = computed(() => props.staffs || { data: [], links: [] })
const statCards = computed(() => props.staffStats?.cards || [])
const overview = computed(() => props.staffStats?.overview || {})
const departmentBars = computed(() => props.staffStats?.departmentBars || [])

const defaultDepartments = ['Administration', 'Accounts', 'Security', 'Library', 'Transport', 'Maintenance']
const defaultRoles = ['Accountant', 'Receptionist', 'Security Guard', 'Librarian Assistant', 'Driver', 'Cleaner', 'Maintenance Staff']

const departmentOptions = computed(() => [...new Set([...(props.departments || []), ...defaultDepartments])])
const roleOptions = computed(() => [...new Set([...(props.roles || []), ...defaultRoles])])

const showCreateModal = ref(false)
const showViewModal = ref(false)
const showEditModal = ref(false)
const selectedStaff = ref(null)

const filterForm = useForm({
    search: props.filters?.search || '',
    department: props.filters?.department || '',
    shift: props.filters?.shift || '',
    status: props.filters?.status || '',
})

const form = useForm({
    staff_no: '',
    full_name: '',
    role: '',
    department: '',
    phone: '',
    email: '',
    shift: 'Morning',
    salary: 0,
    joining_date: '',
    status: 'Active',
    address: '',
})

const editForm = useForm({
    staff_no: '',
    full_name: '',
    role: '',
    department: '',
    phone: '',
    email: '',
    shift: 'Morning',
    salary: 0,
    joining_date: '',
    status: 'Active',
    address: '',
})

const money = (amount) => `AED ${Number(amount || 0).toLocaleString(undefined, {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
})}`

const validateStaff = (targetForm) => {
    const errors = {}

    if (!targetForm.staff_no.trim()) errors.staff_no = 'Staff ID is required.'
    if (!targetForm.full_name.trim()) errors.full_name = 'Full name is required.'
    if (!targetForm.role) errors.role = 'Role is required.'
    if (!targetForm.department) errors.department = 'Department is required.'
    if (!targetForm.phone.trim()) errors.phone = 'Phone number is required.'

    if (targetForm.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(targetForm.email)) {
        errors.email = 'Email must be valid.'
    }

    if (targetForm.salary === '' || Number(targetForm.salary) < 0) {
        errors.salary = 'Valid salary is required.'
    }

    if (targetForm.address && targetForm.address.length > 1000) {
        errors.address = 'Address must not be greater than 1000 characters.'
    }

    return errors
}

const frontendErrors = computed(() => validateStaff(form))
const editFrontendErrors = computed(() => validateStaff(editForm))

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
    form.shift = 'Morning'
    form.salary = 0
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

const showFirstError = (errors, fallback = 'Please check the form errors.') => {
    const firstError = Object.values(errors || {})[0]
    toast.error(firstError || fallback)
}

const submitStaff = () => {
    if (!canSubmit.value || form.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    form.post(route('staffs.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Staff created successfully.')
            resetForm()
            showCreateModal.value = false
        },
        onError: (errors) => {
            if (errors.server) return toast.error(errors.server)
            showFirstError(errors)
        },
    })
}

const openViewModal = (staff) => {
    selectedStaff.value = staff
    showViewModal.value = true
}

const closeViewModal = () => {
    selectedStaff.value = null
    showViewModal.value = false
}

const openEditModal = (staff) => {
    selectedStaff.value = staff

    editForm.staff_no = staff.staff_no || ''
    editForm.full_name = staff.full_name || ''
    editForm.role = staff.role || ''
    editForm.department = staff.department || ''
    editForm.phone = staff.phone || ''
    editForm.email = staff.email || ''
    editForm.shift = staff.shift || 'Morning'
    editForm.salary = staff.salary ?? 0
    editForm.joining_date = staff.joining_date || ''
    editForm.status = staff.status || 'Active'
    editForm.address = staff.address || ''

    editForm.clearErrors()
    showEditModal.value = true
}

const closeEditModal = () => {
    selectedStaff.value = null
    editForm.clearErrors()
    showEditModal.value = false
}

const updateStaff = () => {
    if (!selectedStaff.value || !canUpdate.value || editForm.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    editForm.put(route('staffs.update', selectedStaff.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Staff updated successfully.')
            closeEditModal()
        },
        onError: (errors) => {
            if (errors.edit_server) return toast.error(errors.edit_server)
            showFirstError(errors)
        },
    })
}

const deleteStaff = (staff) => {
    if (!confirm(`Delete staff ${staff.staff_no}?`)) return

    router.delete(route('staffs.destroy', staff.id), {
        preserveScroll: true,
        onSuccess: () => toast.success('Staff deleted successfully.'),
        onError: () => toast.error('Unable to delete staff.'),
    })
}

const applyFilters = () => {
    router.get(route('staffs'), {
        search: filterForm.search,
        department: filterForm.department,
        shift: filterForm.shift,
        status: filterForm.status,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

const resetFilters = () => {
    filterForm.search = ''
    filterForm.department = ''
    filterForm.shift = ''
    filterForm.status = ''

    router.get(route('staffs'), {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

// Import from Excel
const importForm = useForm({
    file: null,
})

const importProgress = ref(0)
const isImporting = ref(false)

const submitImport = () => {
    if (!importForm.file || importForm.processing) {
        toast.error('Please select an Excel file.')
        return
    }

    isImporting.value = true
    importProgress.value = 0

    importForm.post(route('staffs.import'), {
        preserveScroll: true,
        forceFormData: true,

        onProgress: (progress) => {
            if (progress?.percentage) {
                importProgress.value = progress.percentage
            }
        },

        onSuccess: () => {
            toast.success('Staffs imported successfully.')
            importForm.reset()
            importProgress.value = 0
        },

        onError: (errors) => {
            if (errors.import) {
                toast.error(errors.import)
            } else {
                const firstError = Object.values(errors || {})[0]
                toast.error(firstError || 'Import failed. Please check your file.')
            }
        },

        onFinish: () => {
            isImporting.value = false
        },
    })
}
</script>

<template>
    <AppLayout title="Staffs">
        <div class="space-y-6">
            <!-- Header -->
            <div
                class="rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 ring-1 ring-slate-200 sm:p-6 lg:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">
                            Non-Teaching Staff Management
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                            Staffs
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                            Manage non-teaching staff, departments, shifts, contact details, salary records and status.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <!-- Import Form -->
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
                                        <span class="block text-xs text-slate-400">
                                            XLSX, XLS or CSV up to 5MB
                                        </span>
                                    </span>

                                    <input type="file" class="hidden"
                                        @change="importForm.file = $event.target.files[0]" />
                                </label>

                                <button
                                    type="submit"
                                    class="rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-emerald-100 transition hover:bg-emerald-700"
                                >
                                    {{ isImporting ? `Importing ${importProgress}%` : 'Import Excel' }}
                                </button>
                            </form>
                            <!-- Button -->
                            <button
                                type="button"
                                class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:bg-slate-800"
                                @click="openCreateModal"
                            >
                                Add Staff
                            </button>
                        </div>
                        <!-- Add progress bar here
                        <div v-if="isImporting" class="mt-1">
                            <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200">
                                <div class="h-full bg-emerald-600 transition-all duration-300"
                                    :style="{ width: `${importProgress}%` }"></div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div v-for="(stat, index) in statCards" :key="stat.label"
                    class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-500">
                                {{ stat.label }}
                            </p>

                            <h2 class="mt-3 text-2xl font-black text-slate-950 xl:text-3xl">
                                {{ stat.value }}
                            </h2>
                        </div>

                        <div class="flex size-12 items-center justify-center rounded-2xl text-white shadow-lg" :class="[
                            index === 0 ? 'bg-gradient-to-br from-indigo-500 to-sky-400' : '',
                            index === 1 ? 'bg-gradient-to-br from-emerald-500 to-teal-400' : '',
                            index === 2 ? 'bg-gradient-to-br from-amber-500 to-orange-400' : '',
                            index === 3 ? 'bg-gradient-to-br from-rose-500 to-pink-400' : '',
                        ]">
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
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-black text-slate-950">
                                Department Strength
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Staff distribution percentage by department.
                            </p>
                        </div>

                        <span
                            class="w-fit rounded-full bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                            {{ overview.active_percentage || 0 }}% Active
                        </span>
                    </div>

                    <div class="mt-8 space-y-5">
                        <div v-for="item in departmentBars" :key="item.label">
                            <div class="mb-2 flex items-center justify-between">
                                <p class="text-sm font-bold text-slate-700">
                                    {{ item.label }}
                                </p>
                                <p class="text-sm font-bold text-slate-500">
                                    {{ item.value }}%
                                </p>
                            </div>

                            <div class="h-3 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-sky-400"
                                    :style="{ width: `${item.value}%` }"></div>
                            </div>
                        </div>

                        <p v-if="departmentBars.length === 0"
                            class="rounded-2xl bg-slate-50 p-5 text-sm font-bold text-slate-400">
                            No department data available.
                        </p>
                    </div>
                </div>

                <div
                    class="rounded-3xl bg-gradient-to-br from-indigo-600 to-sky-500 p-6 text-white shadow-lg shadow-indigo-100">
                    <h3 class="text-lg font-black">
                        Staff Summary
                    </h3>

                    <p class="mt-1 text-sm text-white/75">
                        Current staff overview
                    </p>

                    <div class="mt-6 space-y-4">
                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Active</p>
                            <p class="mt-1 text-xl font-black">{{ overview.active || 0 }} Staffs</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Night Shift</p>
                            <p class="mt-1 text-xl font-black">{{ overview.night_shift || 0 }} Staffs</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">On Leave</p>
                            <p class="mt-1 text-xl font-black">{{ overview.on_leave || 0 }} Staffs</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Staff Cards -->
            <!-- <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <div v-for="staff in staffList.data" :key="staff.id"
                    class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                {{ staff.staff_no }}
                            </p>

                            <h2 class="mt-2 text-lg font-black text-slate-950">
                                {{ staff.full_name }}
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                {{ staff.role || '-' }} · {{ staff.department || '-' }}
                            </p>
                        </div>

                        <span class="rounded-full px-3 py-1 text-xs font-bold ring-1" :class="staff.status === 'Active'
                                ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                : staff.status === 'On Leave'
                                    ? 'bg-amber-50 text-amber-700 ring-amber-100'
                                    : 'bg-rose-50 text-rose-700 ring-rose-100'
                            ">
                            {{ staff.status }}
                        </span>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-indigo-50 p-4">
                            <p class="text-xs font-semibold text-indigo-500">Phone</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ staff.phone || '-' }}</p>
                        </div>

                        <div class="rounded-2xl bg-sky-50 p-4">
                            <p class="text-xs font-semibold text-sky-500">Shift</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ staff.shift || '-' }}</p>
                        </div>

                        <div class="rounded-2xl bg-violet-50 p-4">
                            <p class="text-xs font-semibold text-violet-500">Department</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ staff.department || '-' }}</p>
                        </div>

                        <div class="rounded-2xl bg-amber-50 p-4">
                            <p class="text-xs font-semibold text-amber-500">Salary</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ money(staff.salary) }}</p>
                        </div>
                    </div>

                    <div class="mt-5 flex gap-2">
                        <button type="button"
                            class="flex-1 rounded-2xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                            @click="openViewModal(staff)">
                            View
                        </button>

                        <button type="button"
                            class="flex-1 rounded-2xl bg-indigo-50 px-4 py-2.5 text-sm font-bold text-indigo-700 transition hover:bg-indigo-100"
                            @click="openEditModal(staff)">
                            Edit
                        </button>
                    </div>
                </div>

                <div v-if="staffList.data.length === 0"
                    class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 md:col-span-2 xl:col-span-4">
                    <p class="text-sm font-bold text-slate-900">No staffs found</p>
                    <p class="mt-1 text-xs text-slate-500">Add staff to see staff records.</p>
                </div>
            </div> -->


            <!-- Filters -->
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <form class="grid gap-4 md:grid-cols-2 xl:grid-cols-6" @submit.prevent="applyFilters">
                    <div class="xl:col-span-2">
                        <label class="text-sm font-semibold text-slate-600">Search Staff</label>
                        <input v-model="filterForm.search" type="text"
                            placeholder="Search by name, role, department, phone or email"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
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
                        <label class="text-sm font-semibold text-slate-600">Shift</label>
                        <select v-model="filterForm.shift"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option value="">All Shifts</option>
                            <option>Morning</option>
                            <option>Evening</option>
                            <option>Night</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Status</label>
                        <select v-model="filterForm.status"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option value="">All Status</option>
                            <option>Active</option>
                            <option>On Leave</option>
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

            <!-- Staff Table -->
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
                <div
                    class="flex flex-col gap-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">
                            Non-Teaching Staff Records
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Complete non-teaching staff records.
                        </p>
                    </div>

                    <button type="button"
                        class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800"
                        @click="openCreateModal">
                        New Staff
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1100px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Staff</th>
                                <th class="px-6 py-4">Staff ID</th>
                                <th class="px-6 py-4">Role</th>
                                <th class="px-6 py-4">Department</th>
                                <th class="px-6 py-4">Phone</th>
                                <th class="px-6 py-4">Shift</th>
                                <th class="px-6 py-4">Salary</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="staff in staffList.data" :key="staff.id"
                                class="transition hover:bg-indigo-50/40">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex size-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 to-sky-100 text-sm font-black text-indigo-700">
                                            {{ staff.full_name?.charAt(0) || 'S' }}
                                        </div>

                                        <div>
                                            <p class="font-bold text-slate-950">
                                                {{ staff.full_name }}
                                            </p>
                                            <p class="text-xs text-slate-400">
                                                {{ staff.email || '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-700">
                                    {{ staff.staff_no }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ staff.role || '-' }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ staff.department || '-' }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ staff.phone || '-' }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ staff.shift || '-' }}
                                </td>

                                <td class="px-6 py-4 font-bold text-slate-900">
                                    {{ money(staff.salary) }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold ring-1" :class="staff.status === 'Active'
                                        ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                        : staff.status === 'On Leave'
                                            ? 'bg-amber-50 text-amber-700 ring-amber-100'
                                            : 'bg-rose-50 text-rose-700 ring-rose-100'
                                        ">
                                        {{ staff.status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button type="button"
                                            class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200"
                                            @click="openViewModal(staff)">
                                            View
                                        </button>

                                        <button type="button"
                                            class="rounded-xl bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100"
                                            @click="openEditModal(staff)">
                                            Edit
                                        </button>

                                        <button type="button"
                                            class="rounded-xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100"
                                            @click="deleteStaff(staff)">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="staffList.data.length === 0">
                                <td colspan="9" class="px-6 py-10 text-center text-sm font-bold text-slate-400">
                                    No staffs found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- Pagination -->
                    <div v-if="staffList.total > staffList.per_page"
                        class="flex flex-col gap-4 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm font-semibold text-slate-500">
                            Showing
                            <span class="font-black text-slate-800">{{ staffList.from || 0 }}</span>
                            to
                            <span class="font-black text-slate-800">{{ staffList.to || 0 }}</span>
                            of
                            <span class="font-black text-slate-800">{{ staffList.total || 0 }}</span>
                            staffs
                        </p>

                        <div class="flex flex-wrap gap-2">
                            <Link v-for="(link, index) in staffList.links" :key="index" :href="link.url || '#'"
                                preserve-scroll preserve-state class="rounded-xl px-4 py-2 text-sm font-bold transition"
                                :class="[
                                    link.active
                                        ? 'bg-slate-950 text-white'
                                        : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-100',
                                    !link.url ? 'pointer-events-none cursor-not-allowed opacity-40' : ''
                                ]" v-html="link.label" />
                        </div>
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
                            <h2 class="text-xl font-black text-slate-950">
                                Add Non-Teaching Staff
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Add staff personal details, department, shift, salary and status.
                            </p>
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
                                <input v-model="form.full_name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'full_name')" placeholder="Imran Ali" />
                                <p v-if="fieldError(frontendErrors, form, 'full_name')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'full_name') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Staff ID *</label>
                                <input v-model="form.staff_no"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'staff_no')" placeholder="STF-1005" />
                                <p v-if="fieldError(frontendErrors, form, 'staff_no')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'staff_no') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Role / Designation *</label>
                                <select v-model="form.role"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'role')">
                                    <option value="">Select role</option>
                                    <option v-for="role in roleOptions" :key="role" :value="role">
                                        {{ role }}
                                    </option>
                                </select>
                                <p v-if="fieldError(frontendErrors, form, 'role')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'role') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Department *</label>
                                <select v-model="form.department"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'department')">
                                    <option value="">Select department</option>
                                    <option v-for="department in departmentOptions" :key="department"
                                        :value="department">
                                        {{ department }}
                                    </option>
                                </select>
                                <p v-if="fieldError(frontendErrors, form, 'department')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'department') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Phone *</label>
                                <input v-model="form.phone"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'phone')" placeholder="+971 50 123 4567" />
                                <p v-if="fieldError(frontendErrors, form, 'phone')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'phone') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Email</label>
                                <input v-model="form.email" type="email"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'email')"
                                    placeholder="staff@example.com" />
                                <p v-if="fieldError(frontendErrors, form, 'email')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'email') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Shift</label>
                                <select v-model="form.shift"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>Morning</option>
                                    <option>Evening</option>
                                    <option>Night</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Monthly Salary *</label>
                                <input v-model="form.salary" type="number"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'salary')" placeholder="4500" />
                                <p v-if="fieldError(frontendErrors, form, 'salary')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'salary') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Joining Date</label>
                                <input v-model="form.joining_date" type="date"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status</label>
                                <select v-model="form.status"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>Active</option>
                                    <option>On Leave</option>
                                    <option>Inactive</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Address</label>
                                <textarea v-model="form.address" rows="3" placeholder="Staff address"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'address')"></textarea>
                                <p v-if="fieldError(frontendErrors, form, 'address')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'address') }}
                                </p>
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
                            :disabled="form.processing || !canSubmit" @click="submitStaff">
                            {{ form.processing ? 'Saving...' : 'Save Staff' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- View Modal -->
            <div v-if="showViewModal && selectedStaff"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
                <div class="w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div
                        class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">
                                {{ selectedStaff.full_name }}
                            </h2>
                            <p class="mt-1 text-sm font-semibold text-slate-500">
                                {{ selectedStaff.staff_no }} · {{ selectedStaff.role || '-' }}
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
                                <p class="text-xs font-bold uppercase text-indigo-500">Department</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedStaff.department || '-' }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Phone</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedStaff.phone || '-' }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Email</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedStaff.email || '-' }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Shift</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedStaff.shift || '-' }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Salary</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ money(selectedStaff.salary) }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Joining Date</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedStaff.joining_date || '-' }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Status</p>
                                <p class="mt-1">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold ring-1" :class="selectedStaff.status === 'Active'
                                        ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                        : selectedStaff.status === 'On Leave'
                                            ? 'bg-amber-50 text-amber-700 ring-amber-100'
                                            : 'bg-rose-50 text-rose-700 ring-rose-100'
                                        ">
                                        {{ selectedStaff.status }}
                                    </span>
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100 sm:col-span-2">
                                <p class="text-xs font-bold uppercase text-slate-400">Address</p>
                                <p class="mt-1 text-sm font-bold leading-6 text-slate-800">
                                    {{ selectedStaff.address || '-' }}
                                </p>
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
                            @click="openEditModal(selectedStaff); showViewModal = false">
                            Edit Staff
                        </button>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div v-if="showEditModal && selectedStaff"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
                <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div
                        class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">
                                Edit Staff
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Update staff personal details, department, shift, salary and status.
                            </p>
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
                                <input v-model="editForm.full_name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'full_name')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Staff ID *</label>
                                <input v-model="editForm.staff_no"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'staff_no')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Role / Designation *</label>
                                <select v-model="editForm.role"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'role')">
                                    <option value="">Select role</option>
                                    <option v-for="role in roleOptions" :key="role" :value="role">
                                        {{ role }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Department *</label>
                                <select v-model="editForm.department"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'department')">
                                    <option value="">Select department</option>
                                    <option v-for="department in departmentOptions" :key="department"
                                        :value="department">
                                        {{ department }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Phone *</label>
                                <input v-model="editForm.phone"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'phone')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Email</label>
                                <input v-model="editForm.email" type="email"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'email')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Shift</label>
                                <select v-model="editForm.shift"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>Morning</option>
                                    <option>Evening</option>
                                    <option>Night</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Monthly Salary *</label>
                                <input v-model="editForm.salary" type="number"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'salary')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Joining Date</label>
                                <input v-model="editForm.joining_date" type="date"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status</label>
                                <select v-model="editForm.status"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>Active</option>
                                    <option>On Leave</option>
                                    <option>Inactive</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Address</label>
                                <textarea v-model="editForm.address" rows="3"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'address')"></textarea>
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
                            :disabled="editForm.processing || !canUpdate" @click="updateStaff">
                            {{ editForm.processing ? 'Updating...' : 'Update Staff' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>