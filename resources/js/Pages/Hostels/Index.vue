<script setup>
import { ref, computed } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    hostels: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            type: '',
            location: '',
            status: '',
        }),
    },
    hostelStats: {
        type: Object,
        default: () => ({
            cards: [],
            overview: {},
            occupancyBars: [],
        }),
    },
    locations: {
        type: Array,
        default: () => [],
    },
})

const hostelList = computed(() => props.hostels || { data: [], links: [] })
const statCards = computed(() => props.hostelStats?.cards || [])
const overview = computed(() => props.hostelStats?.overview || {})
const occupancyBars = computed(() => props.hostelStats?.occupancyBars || [])

const defaultLocations = ['North Block', 'East Block', 'West Block', 'Admin Block']
const locationOptions = computed(() => [...new Set([...(props.locations || []), ...defaultLocations])])

const showCreateModal = ref(false)
const showViewModal = ref(false)
const showEditModal = ref(false)
const selectedHostel = ref(null)

const importProgress = ref(0)
const isImporting = ref(false)

const filterForm = useForm({
    search: props.filters?.search || '',
    type: props.filters?.type || '',
    location: props.filters?.location || '',
    status: props.filters?.status || '',
})

const importForm = useForm({
    file: null,
})

const form = useForm({
    hostel_no: '',
    name: '',
    type: 'Boys',
    warden: '',
    location: '',
    rooms: 0,
    capacity: 0,
    occupied: 0,
    status: 'Active',
    notes: '',
})

const editForm = useForm({
    hostel_no: '',
    name: '',
    type: 'Boys',
    warden: '',
    location: '',
    rooms: 0,
    capacity: 0,
    occupied: 0,
    status: 'Active',
    notes: '',
})

const validateHostel = (targetForm) => {
    const errors = {}

    if (!targetForm.hostel_no.trim()) errors.hostel_no = 'Hostel ID is required.'
    if (!targetForm.name.trim()) errors.name = 'Hostel name is required.'

    if (targetForm.rooms === '' || Number(targetForm.rooms) < 0) {
        errors.rooms = 'Valid room count is required.'
    }

    if (targetForm.capacity === '' || Number(targetForm.capacity) < 0) {
        errors.capacity = 'Valid capacity is required.'
    }

    if (targetForm.occupied === '' || Number(targetForm.occupied) < 0) {
        errors.occupied = 'Valid occupied beds count is required.'
    }

    if (Number(targetForm.occupied || 0) > Number(targetForm.capacity || 0)) {
        errors.occupied = 'Occupied beds cannot be greater than capacity.'
    }

    if (targetForm.notes && targetForm.notes.length > 1000) {
        errors.notes = 'Notes must not be greater than 1000 characters.'
    }

    return errors
}

const frontendErrors = computed(() => validateHostel(form))
const editFrontendErrors = computed(() => validateHostel(editForm))

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
    form.type = 'Boys'
    form.rooms = 0
    form.capacity = 0
    form.occupied = 0
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

const submitHostel = () => {
    if (!canSubmit.value || form.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    form.post(route('hostels.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Hostel created successfully.')
            resetForm()
            showCreateModal.value = false
        },
        onError: (errors) => {
            if (errors.server) return toast.error(errors.server)
            showFirstError(errors)
        },
    })
}

const openViewModal = (hostel) => {
    selectedHostel.value = hostel
    showViewModal.value = true
}

const closeViewModal = () => {
    selectedHostel.value = null
    showViewModal.value = false
}

const openEditModal = (hostel) => {
    selectedHostel.value = hostel

    editForm.hostel_no = hostel.hostel_no || ''
    editForm.name = hostel.name || ''
    editForm.type = hostel.type || 'Boys'
    editForm.warden = hostel.warden || ''
    editForm.location = hostel.location || ''
    editForm.rooms = hostel.rooms ?? 0
    editForm.capacity = hostel.capacity ?? 0
    editForm.occupied = hostel.occupied ?? 0
    editForm.status = hostel.status || 'Active'
    editForm.notes = hostel.notes || ''

    editForm.clearErrors()
    showEditModal.value = true
}

const closeEditModal = () => {
    selectedHostel.value = null
    editForm.clearErrors()
    showEditModal.value = false
}

const updateHostel = () => {
    if (!selectedHostel.value || !canUpdate.value || editForm.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    editForm.put(route('hostels.update', selectedHostel.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Hostel updated successfully.')
            closeEditModal()
        },
        onError: (errors) => {
            if (errors.edit_server) return toast.error(errors.edit_server)
            showFirstError(errors)
        },
    })
}

const deleteHostel = (hostel) => {
    if (!confirm(`Delete hostel ${hostel.hostel_no}?`)) return

    router.delete(route('hostels.destroy', hostel.id), {
        preserveScroll: true,
        onSuccess: () => toast.success('Hostel deleted successfully.'),
        onError: () => toast.error('Unable to delete hostel.'),
    })
}

const applyFilters = () => {
    router.get(route('hostels'), {
        search: filterForm.search,
        type: filterForm.type,
        location: filterForm.location,
        status: filterForm.status,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

const resetFilters = () => {
    filterForm.search = ''
    filterForm.type = ''
    filterForm.location = ''
    filterForm.status = ''

    router.get(route('hostels'), {}, {
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

    importForm.post(route('hostels.import'), {
        preserveScroll: true,
        forceFormData: true,
        onProgress: (progress) => {
            if (progress?.percentage) importProgress.value = progress.percentage
        },
        onSuccess: () => {
            toast.success('Hostels imported successfully.')
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
    <AppLayout title="Hostels">
        <div class="space-y-6">
            <div
                class="rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 ring-1 ring-slate-200 sm:p-6 lg:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">Hostel Management</p>
                        <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">Hostels</h1>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                            Manage hostels, rooms, wardens, student occupancy, capacity, vacant beds and maintenance
                            status.
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
                            Add Hostel
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

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div v-for="(stat, index) in statCards" :key="stat.label"
                    class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-500">{{ stat.label }}</p>
                            <h2 class="mt-3 text-2xl font-black text-slate-950 xl:text-3xl">{{ stat.value }}</h2>
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
                    <p class="mt-4 text-xs font-semibold text-slate-500">{{ stat.change }}</p>
                </div>
            </div>


            <div class="grid gap-6 xl:grid-cols-3">
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 xl:col-span-2">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Hostel Occupancy Overview</h2>
                            <p class="mt-1 text-sm text-slate-500">Occupancy percentage by hostel.</p>
                        </div>
                        <span
                            class="w-fit rounded-full bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                            {{ overview.occupancy_percentage || 0 }}% Occupied
                        </span>
                    </div>

                    <div class="mt-8 space-y-5">
                        <div v-for="item in occupancyBars" :key="item.label">
                            <div class="mb-2 flex items-center justify-between">
                                <p class="text-sm font-bold text-slate-700">{{ item.label }}</p>
                                <p class="text-sm font-bold text-slate-500">{{ item.value }}%</p>
                            </div>
                            <div class="h-3 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-sky-400"
                                    :style="{ width: `${item.value}%` }"></div>
                            </div>
                        </div>

                        <p v-if="occupancyBars.length === 0"
                            class="rounded-2xl bg-slate-50 p-5 text-sm font-bold text-slate-400">
                            No occupancy data available.
                        </p>
                    </div>
                </div>

                <div
                    class="rounded-3xl bg-gradient-to-br from-indigo-600 to-sky-500 p-6 text-white shadow-lg shadow-indigo-100">
                    <h3 class="text-lg font-black">Hostel Summary</h3>
                    <p class="mt-1 text-sm text-white/75">Current academic term overview</p>

                    <div class="mt-6 space-y-4">
                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Capacity</p>
                            <p class="mt-1 text-xl font-black">{{ overview.capacity || 0 }} Beds</p>
                        </div>
                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Vacant Beds</p>
                            <p class="mt-1 text-xl font-black">{{ overview.vacant || 0 }} Beds</p>
                        </div>
                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Maintenance</p>
                            <p class="mt-1 text-xl font-black">{{ overview.maintenance || 0 }} Hostel</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <div v-for="hostel in hostelList.data" :key="hostel.id" class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ hostel.hostel_no }}</p>
                            <h2 class="mt-2 text-lg font-black text-slate-950">{{ hostel.name }}</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ hostel.type }} · {{ hostel.location || '-' }}</p>
                        </div>

                        <span
                            class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                            :class="
                                hostel.status === 'Active'
                                    ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                    : hostel.status === 'Maintenance'
                                        ? 'bg-amber-50 text-amber-700 ring-amber-100'
                                        : 'bg-rose-50 text-rose-700 ring-rose-100'
                            "
                        >
                            {{ hostel.status }}
                        </span>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-indigo-50 p-4">
                            <p class="text-xs font-semibold text-indigo-500">Warden</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ hostel.warden || '-' }}</p>
                        </div>
                        <div class="rounded-2xl bg-sky-50 p-4">
                            <p class="text-xs font-semibold text-sky-500">Rooms</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ hostel.rooms }}</p>
                        </div>
                        <div class="rounded-2xl bg-violet-50 p-4">
                            <p class="text-xs font-semibold text-violet-500">Capacity</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ hostel.capacity }}</p>
                        </div>
                        <div class="rounded-2xl bg-amber-50 p-4">
                            <p class="text-xs font-semibold text-amber-500">Occupied</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ hostel.occupied }}</p>
                        </div>
                    </div>

                    <div class="mt-5 flex gap-2">
                        <button type="button" class="flex-1 rounded-2xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-200" @click="openViewModal(hostel)">
                            View
                        </button>
                        <button type="button" class="flex-1 rounded-2xl bg-indigo-50 px-4 py-2.5 text-sm font-bold text-indigo-700 transition hover:bg-indigo-100" @click="openEditModal(hostel)">
                            Edit
                        </button>
                    </div>
                </div>
            </div> -->

            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <form class="grid gap-4 md:grid-cols-2 xl:grid-cols-6" @submit.prevent="applyFilters">
                    <div class="xl:col-span-2">
                        <label class="text-sm font-semibold text-slate-600">Search Hostel</label>
                        <input v-model="filterForm.search" type="text"
                            placeholder="Search by hostel, warden, type or location"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Type</label>
                        <select v-model="filterForm.type"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option value="">All Types</option>
                            <option>Boys</option>
                            <option>Girls</option>
                            <option>Staff / Guest</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Block</label>
                        <select v-model="filterForm.location"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option value="">All Blocks</option>
                            <option v-for="location in locationOptions" :key="location" :value="location">
                                {{ location }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Status</label>
                        <select v-model="filterForm.status"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option value="">All Status</option>
                            <option>Active</option>
                            <option>Maintenance</option>
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
                        <h2 class="text-lg font-black text-slate-950">Hostel Records</h2>
                        <p class="mt-1 text-sm text-slate-500">Complete hostel and accommodation records.</p>
                    </div>
                    <button type="button"
                        class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800"
                        @click="openCreateModal">
                        New Hostel
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1100px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Hostel</th>
                                <th class="px-6 py-4">Hostel ID</th>
                                <th class="px-6 py-4">Type</th>
                                <th class="px-6 py-4">Warden</th>
                                <th class="px-6 py-4">Location</th>
                                <th class="px-6 py-4">Rooms</th>
                                <th class="px-6 py-4">Occupancy</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="hostel in hostelList.data" :key="hostel.id"
                                class="transition hover:bg-indigo-50/40">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex size-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 to-sky-100 text-sm font-black text-indigo-700">
                                            {{ hostel.name?.charAt(0) || 'H' }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-950">{{ hostel.name }}</p>
                                            <p class="text-xs text-slate-400">{{ hostel.location || '-' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-700">{{ hostel.hostel_no }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ hostel.type }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ hostel.warden || '-' }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ hostel.location || '-' }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900">{{ hostel.rooms }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900">{{ hostel.occupied }}/{{ hostel.capacity
                                    }}</td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold ring-1" :class="hostel.status === 'Active'
                                            ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                            : hostel.status === 'Maintenance'
                                                ? 'bg-amber-50 text-amber-700 ring-amber-100'
                                                : 'bg-rose-50 text-rose-700 ring-rose-100'
                                        ">
                                        {{ hostel.status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button type="button"
                                            class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200"
                                            @click="openViewModal(hostel)">View</button>
                                        <button type="button"
                                            class="rounded-xl bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100"
                                            @click="openEditModal(hostel)">Edit</button>
                                        <button type="button"
                                            class="rounded-xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100"
                                            @click="deleteHostel(hostel)">Delete</button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="hostelList.data.length === 0">
                                <td colspan="9" class="px-6 py-10 text-center text-sm font-bold text-slate-400">
                                    No hostels found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="hostelList.total > hostelList.per_page"
                    class="flex flex-col gap-4 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm font-semibold text-slate-500">
                        Showing <span class="font-black text-slate-800">{{ hostelList.from || 0 }}</span>
                        to <span class="font-black text-slate-800">{{ hostelList.to || 0 }}</span>
                        of <span class="font-black text-slate-800">{{ hostelList.total || 0 }}</span> hostels
                    </p>

                    <div class="flex flex-wrap gap-2">
                        <Link v-for="(link, index) in hostelList.links" :key="index" :href="link.url || '#'"
                            preserve-scroll preserve-state class="rounded-xl px-4 py-2 text-sm font-bold transition"
                            :class="[
                                link.active ? 'bg-slate-950 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-100',
                                !link.url ? 'pointer-events-none cursor-not-allowed opacity-40' : ''
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
                            <h2 class="text-xl font-black text-slate-950">Add Hostel</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Add hostel details, warden, rooms, capacity and occupancy status.
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
                                <label class="text-sm font-semibold text-slate-600">Hostel Name *</label>
                                <input v-model="form.name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'name')" placeholder="Boys Hostel A" />
                                <p v-if="fieldError(frontendErrors, form, 'name')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'name') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Hostel ID *</label>
                                <input v-model="form.hostel_no"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'hostel_no')" placeholder="HST-1005" />
                                <p v-if="fieldError(frontendErrors, form, 'hostel_no')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'hostel_no') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Hostel Type</label>
                                <select v-model="form.type"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>Boys</option>
                                    <option>Girls</option>
                                    <option>Staff / Guest</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Warden</label>
                                <input v-model="form.warden"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                    placeholder="Mr. Imran Ali" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Location / Block</label>
                                <input v-model="form.location"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                    placeholder="North Block" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Total Rooms *</label>
                                <input v-model="form.rooms" type="number" min="0"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'rooms')" placeholder="32" />
                                <p v-if="fieldError(frontendErrors, form, 'rooms')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'rooms') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Total Capacity *</label>
                                <input v-model="form.capacity" type="number" min="0"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'capacity')" placeholder="120" />
                                <p v-if="fieldError(frontendErrors, form, 'capacity')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'capacity') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Occupied Beds *</label>
                                <input v-model="form.occupied" type="number" min="0"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'occupied')" placeholder="104" />
                                <p v-if="fieldError(frontendErrors, form, 'occupied')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'occupied') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status</label>
                                <select v-model="form.status"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>Active</option>
                                    <option>Maintenance</option>
                                    <option>Inactive</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Notes</label>
                                <textarea v-model="form.notes" rows="3"
                                    placeholder="Hostel facilities, rules, room notes or maintenance details..."
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'notes')"></textarea>
                                <p v-if="fieldError(frontendErrors, form, 'notes')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'notes') }}
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
                            :disabled="form.processing || !canSubmit" @click="submitHostel">
                            {{ form.processing ? 'Saving...' : 'Save Hostel' }}
                        </button>
                    </div>
                </div>
            </div>
            <!-- View Modal -->
            <div v-if="showViewModal && selectedHostel"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
                <div class="w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div
                        class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">{{ selectedHostel.name }}</h2>
                            <p class="mt-1 text-sm font-semibold text-slate-500">
                                {{ selectedHostel.hostel_no }} · {{ selectedHostel.type }}
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
                                <p class="text-xs font-bold uppercase text-indigo-500">Warden</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedHostel.warden || '-' }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Location</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedHostel.location || '-' }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Rooms</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedHostel.rooms }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Capacity</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedHostel.capacity }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Occupied Beds</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedHostel.occupied }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Vacant Beds</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedHostel.vacant || 0 }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Occupancy</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">
                                    {{ selectedHostel.occupancy_percentage || 0 }}%
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Status</p>
                                <p class="mt-1">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold ring-1" :class="selectedHostel.status === 'Active'
                                            ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                            : selectedHostel.status === 'Maintenance'
                                                ? 'bg-amber-50 text-amber-700 ring-amber-100'
                                                : 'bg-rose-50 text-rose-700 ring-rose-100'
                                        ">
                                        {{ selectedHostel.status }}
                                    </span>
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100 sm:col-span-2">
                                <p class="text-xs font-bold uppercase text-slate-400">Notes</p>
                                <p class="mt-1 text-sm font-bold leading-6 text-slate-800">
                                    {{ selectedHostel.notes || '-' }}
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
                            @click="openEditModal(selectedHostel); showViewModal = false">
                            Edit Hostel
                        </button>
                    </div>
                </div>
            </div>
            <!-- Edit Modal -->
            <div v-if="showEditModal && selectedHostel"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
                <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div
                        class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">Edit Hostel</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Update hostel details, warden, capacity and occupancy status.
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
                                <label class="text-sm font-semibold text-slate-600">Hostel Name *</label>
                                <input v-model="editForm.name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'name')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Hostel ID *</label>
                                <input v-model="editForm.hostel_no"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'hostel_no')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Hostel Type</label>
                                <select v-model="editForm.type"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>Boys</option>
                                    <option>Girls</option>
                                    <option>Staff / Guest</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Warden</label>
                                <input v-model="editForm.warden"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Location / Block</label>
                                <input v-model="editForm.location"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Total Rooms *</label>
                                <input v-model="editForm.rooms" type="number" min="0"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'rooms')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Total Capacity *</label>
                                <input v-model="editForm.capacity" type="number" min="0"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'capacity')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Occupied Beds *</label>
                                <input v-model="editForm.occupied" type="number" min="0"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'occupied')" />
                                <p v-if="fieldError(editFrontendErrors, editForm, 'occupied')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(editFrontendErrors, editForm, 'occupied') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status</label>
                                <select v-model="editForm.status"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>Active</option>
                                    <option>Maintenance</option>
                                    <option>Inactive</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Notes</label>
                                <textarea v-model="editForm.notes" rows="3"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'notes')"></textarea>
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
                            :disabled="editForm.processing || !canUpdate" @click="updateHostel">
                            {{ editForm.processing ? 'Updating...' : 'Update Hostel' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>