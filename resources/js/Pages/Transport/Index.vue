<script setup>
import { onMounted, ref, watch, computed } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import AppLayout from '@/Layouts/AppLayout.vue'
import Chart from 'chart.js/auto'

const props = defineProps({
    transports: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            route: '',
            type: '',
            status: '',
        }),
    },
    transportStats: {
        type: Object,
        default: () => ({
            cards: [],
            overview: {},
            routeUsage: [],
        }),
    },
    routes: {
        type: Array,
        default: () => [],
    },
})

const transportList = computed(() => props.transports || { data: [], links: [] })
const stats = computed(() => props.transportStats?.cards || [])
const overview = computed(() => props.transportStats?.overview || {})
const routeUsage = computed(() => props.transportStats?.routeUsage || [])

const showCreateModal = ref(false)
const showViewModal = ref(false)
const showEditModal = ref(false)
const selectedTransport = ref(null)

const filterForm = useForm({
    search: props.filters?.search || '',
    route: props.filters?.route || '',
    type: props.filters?.type || '',
    status: props.filters?.status || '',
})

const form = useForm({
    vehicle_no: '',
    vehicle: '',
    type: 'Bus',
    route: '',
    driver: '',
    phone: '',
    plate: '',
    capacity: 0,
    students: 0,
    status: 'Active',
    notes: '',
})

const editForm = useForm({
    vehicle_no: '',
    vehicle: '',
    type: 'Bus',
    route: '',
    driver: '',
    phone: '',
    plate: '',
    capacity: 0,
    students: 0,
    status: 'Active',
    notes: '',
})

const validateTransport = (targetForm) => {
    const errors = {}

    if (!targetForm.vehicle_no.trim()) errors.vehicle_no = 'Vehicle ID is required.'
    if (!targetForm.vehicle.trim()) errors.vehicle = 'Vehicle name is required.'
    if (!targetForm.route.trim()) errors.route = 'Route is required.'
    if (!targetForm.plate.trim()) errors.plate = 'Plate number is required.'

    if (targetForm.capacity === '' || Number(targetForm.capacity) < 0) {
        errors.capacity = 'Valid capacity is required.'
    }

    if (targetForm.students === '' || Number(targetForm.students) < 0) {
        errors.students = 'Valid assigned students count is required.'
    }

    if (Number(targetForm.students || 0) > Number(targetForm.capacity || 0)) {
        errors.students = 'Assigned students cannot be greater than capacity.'
    }

    if (targetForm.notes && targetForm.notes.length > 1000) {
        errors.notes = 'Notes must not be greater than 1000 characters.'
    }

    return errors
}

const frontendErrors = computed(() => validateTransport(form))
const editFrontendErrors = computed(() => validateTransport(editForm))

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
    form.type = 'Bus'
    form.capacity = 0
    form.students = 0
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

const submitTransport = () => {
    if (!canSubmit.value || form.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    form.post(route('transport.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Transport vehicle created successfully.')
            resetForm()
            showCreateModal.value = false
        },
        onError: (errors) => {
            if (errors.server) return toast.error(errors.server)
            showFirstError(errors)
        },
    })
}

const openViewModal = (transport) => {
    selectedTransport.value = transport
    showViewModal.value = true
}

const closeViewModal = () => {
    selectedTransport.value = null
    showViewModal.value = false
}

const openEditModal = (transport) => {
    selectedTransport.value = transport

    editForm.vehicle_no = transport.vehicle_no || ''
    editForm.vehicle = transport.vehicle || ''
    editForm.type = transport.type || 'Bus'
    editForm.route = transport.route || ''
    editForm.driver = transport.driver || ''
    editForm.phone = transport.phone || ''
    editForm.plate = transport.plate || ''
    editForm.capacity = transport.capacity ?? 0
    editForm.students = transport.students ?? 0
    editForm.status = transport.status || 'Active'
    editForm.notes = transport.notes || ''

    editForm.clearErrors()
    showEditModal.value = true
}

const closeEditModal = () => {
    selectedTransport.value = null
    editForm.clearErrors()
    showEditModal.value = false
}

const updateTransport = () => {
    if (!selectedTransport.value || !canUpdate.value || editForm.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    editForm.put(route('transport.update', selectedTransport.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Transport vehicle updated successfully.')
            closeEditModal()
        },
        onError: (errors) => {
            if (errors.edit_server) return toast.error(errors.edit_server)
            showFirstError(errors)
        },
    })
}

const deleteTransport = (transport) => {
    if (!confirm(`Delete vehicle ${transport.vehicle_no}?`)) return

    router.delete(route('transport.destroy', transport.id), {
        preserveScroll: true,
        onSuccess: () => toast.success('Transport vehicle deleted successfully.'),
        onError: () => toast.error('Unable to delete transport vehicle.'),
    })
}

const applyFilters = () => {
    router.get(route('transport.index'), {
        search: filterForm.search,
        route: filterForm.route,
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
    filterForm.route = ''
    filterForm.type = ''
    filterForm.status = ''

    router.get(route('transport.index'), {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}
//import
const importProgress = ref(0)
const isImporting = ref(false)

const importForm = useForm({
    file: null,
})

const submitImport = () => {
    if (!importForm.file || importForm.processing) {
        toast.error('Please select an Excel file.')
        return
    }

    isImporting.value = true
    importProgress.value = 0

    importForm.post(route('transport.import'), {
        preserveScroll: true,
        forceFormData: true,
        onProgress: (progress) => {
            if (progress?.percentage) importProgress.value = progress.percentage
        },
        onSuccess: () => {
            toast.success('Transport vehicles imported successfully.')
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
//charts
const chartRef = ref(null)
let chartInstance = null

const renderChart = () => {
    if (!chartRef.value) return

    if (chartInstance) {
        chartInstance.destroy()
    }

    chartInstance = new Chart(chartRef.value, {
        type: 'bar',
        data: {
            labels: routeUsage.value.map(item => item.label),
            datasets: [
                {
                    label: 'Usage %',
                    data: routeUsage.value.map(item => item.value),
                    borderRadius: 8,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => `${ctx.raw}%`,
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: (value) => value + '%',
                    },
                },
            },
        },
    })
}

onMounted(renderChart)

watch(routeUsage, () => {
    renderChart()
})
</script>

<template>
    <AppLayout title="Transport">
        <div class="space-y-6">
            <!-- Header -->
            <div
                class="rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 ring-1 ring-slate-200 sm:p-6 lg:p-8">
                <div v-if="isImporting" class="rounded-2xl bg-white p-3 shadow-sm ring-1 ring-slate-200">
                    <div class="h-2 overflow-hidden rounded-full bg-emerald-100">
                        <div class="h-full rounded-full bg-emerald-600 transition-all duration-300"
                            :style="{ width: `${importProgress}%` }"></div>
                    </div>
                </div>
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">Transport Management</p>
                        <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">Transport</h1>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                            Manage school buses, vans, routes, drivers, assigned students, vehicle capacity and
                            maintenance status.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <form
                            class="flex flex-col gap-3 rounded-3xl border border-emerald-100 bg-emerald-50/60 p-3 shadow-sm sm:flex-row sm:items-center"
                            @submit.prevent="submitImport">
                            <label
                                class="flex cursor-pointer items-center gap-3 rounded-2xl border border-dashed border-emerald-300 bg-white px-4 py-3 text-sm font-bold text-slate-700 transition hover:border-emerald-400 hover:bg-emerald-50">
                                <span
                                    class="flex size-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
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
                            Add Vehicle
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div v-for="(stat, index) in stats" :key="stat.label"
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



            <!-- Overview -->
            <div class="grid gap-6 xl:grid-cols-3">
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 xl:col-span-2">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Route Capacity Usage</h2>
                            <p class="mt-1 text-sm text-slate-500">Capacity usage percentage by route.</p>
                        </div>

                        <span
                            class="w-fit rounded-full bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                            {{ overview.usage_percentage || 0 }}% Used
                        </span>
                    </div>

                    <div class="mt-6 h-72">
                        <canvas ref="chartRef"></canvas>
                    </div>

                    <p v-if="routeUsage.length === 0"
                        class="mt-6 rounded-2xl bg-slate-50 p-5 text-center text-sm font-bold text-slate-400">
                        No route usage data available.
                    </p>
                </div>

                <div
                    class="rounded-3xl bg-gradient-to-br from-indigo-600 to-sky-500 p-6 text-white shadow-lg shadow-indigo-100">
                    <h3 class="text-lg font-black">Transport Summary</h3>
                    <p class="mt-1 text-sm text-white/75">Current month overview</p>

                    <div class="mt-6 space-y-4">
                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Total Capacity</p>
                            <p class="mt-1 text-xl font-black">{{ overview.capacity || 0 }} Seats</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Available Seats</p>
                            <p class="mt-1 text-xl font-black">{{ overview.available || 0 }} Seats</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Maintenance Due</p>
                            <p class="mt-1 text-xl font-black">{{ overview.maintenance || 0 }} Vehicles</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Filters -->
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <form class="grid gap-4 md:grid-cols-2 xl:grid-cols-6" @submit.prevent="applyFilters">
                    <div class="xl:col-span-2">
                        <label class="text-sm font-semibold text-slate-600">Search Transport</label>
                        <input v-model="filterForm.search" type="text"
                            placeholder="Search by vehicle, route, driver or plate"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Route</label>
                        <select v-model="filterForm.route"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option value="">All Routes</option>
                            <option v-for="route in routes" :key="route" :value="route">
                                {{ route }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Vehicle Type</label>
                        <select v-model="filterForm.type"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option value="">All Types</option>
                            <option>Bus</option>
                            <option>Van</option>
                            <option>Mini Bus</option>
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
            <!-- Table -->
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
                <div
                    class="flex flex-col gap-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">Transport Records</h2>
                        <p class="mt-1 text-sm text-slate-500">Complete vehicle and route records.</p>
                    </div>

                    <button type="button"
                        class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800"
                        @click="openCreateModal">
                        New Vehicle
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1100px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Vehicle</th>
                                <th class="px-6 py-4">Vehicle ID</th>
                                <th class="px-6 py-4">Type</th>
                                <th class="px-6 py-4">Route</th>
                                <th class="px-6 py-4">Driver</th>
                                <th class="px-6 py-4">Phone</th>
                                <th class="px-6 py-4">Plate</th>
                                <th class="px-6 py-4">Capacity</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="transport in transportList.data" :key="transport.id"
                                class="transition hover:bg-indigo-50/40">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex size-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 to-sky-100 text-sm font-black text-indigo-700">
                                            {{ transport.vehicle?.charAt(0) || 'T' }}
                                        </div>

                                        <div>
                                            <p class="font-bold text-slate-950">{{ transport.vehicle }}</p>
                                            <p class="text-xs text-slate-400">{{ transport.plate || '-' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-700">{{ transport.vehicle_no }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ transport.type }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ transport.route }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ transport.driver || '-' }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ transport.phone || '-' }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ transport.plate }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    {{ transport.students }}/{{ transport.capacity }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold ring-1" :class="transport.status === 'Active'
                                        ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                        : transport.status === 'Maintenance'
                                            ? 'bg-amber-50 text-amber-700 ring-amber-100'
                                            : 'bg-rose-50 text-rose-700 ring-rose-100'
                                        ">
                                        {{ transport.status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button type="button"
                                            class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200"
                                            @click="openViewModal(transport)">
                                            View
                                        </button>

                                        <button type="button"
                                            class="rounded-xl bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100"
                                            @click="openEditModal(transport)">
                                            Edit
                                        </button>

                                        <button type="button"
                                            class="rounded-xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100"
                                            @click="deleteTransport(transport)">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="transportList.data.length === 0">
                                <td colspan="10" class="px-6 py-10 text-center text-sm font-bold text-slate-400">
                                    No transport records found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="transportList.total > transportList.per_page"
                    class="flex flex-col gap-4 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm font-semibold text-slate-500">
                        Showing <span class="font-black text-slate-800">{{ transportList.from || 0 }}</span>
                        to <span class="font-black text-slate-800">{{ transportList.to || 0 }}</span>
                        of <span class="font-black text-slate-800">{{ transportList.total || 0 }}</span> vehicles
                    </p>

                    <div class="flex flex-wrap gap-2">
                        <Link v-for="(link, index) in transportList.links" :key="index" :href="link.url || '#'"
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
                            <h2 class="text-xl font-black text-slate-950">Add Transport Vehicle</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Add vehicle, route, driver, capacity and maintenance status.
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
                                <label class="text-sm font-semibold text-slate-600">Vehicle Name *</label>
                                <input v-model="form.vehicle"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'vehicle')" placeholder="School Bus 04" />
                                <p v-if="fieldError(frontendErrors, form, 'vehicle')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'vehicle') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Vehicle ID *</label>
                                <input v-model="form.vehicle_no"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'vehicle_no')" placeholder="BUS-1004" />
                                <p v-if="fieldError(frontendErrors, form, 'vehicle_no')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'vehicle_no') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Vehicle Type</label>
                                <select v-model="form.type"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>Bus</option>
                                    <option>Van</option>
                                    <option>Mini Bus</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Route *</label>
                                <input v-model="form.route"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'route')" placeholder="Route D - Deira" />
                                <p v-if="fieldError(frontendErrors, form, 'route')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'route') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Driver Name</label>
                                <input v-model="form.driver"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                    placeholder="Driver name" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Driver Phone</label>
                                <input v-model="form.phone"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                    placeholder="+971 50 123 4567" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Plate Number *</label>
                                <input v-model="form.plate"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'plate')" placeholder="DXB-12345" />
                                <p v-if="fieldError(frontendErrors, form, 'plate')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'plate') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Capacity *</label>
                                <input v-model="form.capacity" type="number" min="0"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'capacity')" placeholder="45" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Assigned Students *</label>
                                <input v-model="form.students" type="number" min="0"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'students')" placeholder="38" />
                                <p v-if="fieldError(frontendErrors, form, 'students')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'students') }}
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
                                <label class="text-sm font-semibold text-slate-600">Route Details</label>
                                <textarea v-model="form.notes" rows="3" placeholder="Pickup points, timing or notes"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'notes')"></textarea>
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
                            :disabled="form.processing || !canSubmit" @click="submitTransport">
                            {{ form.processing ? 'Saving...' : 'Save Vehicle' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- View Modal -->
            <div v-if="showViewModal && selectedTransport"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
                <div class="w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div
                        class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">{{ selectedTransport.vehicle }}</h2>
                            <p class="mt-1 text-sm font-semibold text-slate-500">
                                {{ selectedTransport.vehicle_no }} · {{ selectedTransport.type }}
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
                                <p class="text-xs font-bold uppercase text-indigo-500">Route</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedTransport.route }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Driver</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedTransport.driver || '-' }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Phone</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedTransport.phone || '-' }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Plate</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedTransport.plate }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Capacity</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedTransport.capacity }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Assigned Students</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedTransport.students }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Available Seats</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedTransport.available || 0 }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Usage</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">
                                    {{ selectedTransport.usage_percentage || 0 }}%
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100 sm:col-span-2">
                                <p class="text-xs font-bold uppercase text-slate-400">Notes</p>
                                <p class="mt-1 text-sm font-bold leading-6 text-slate-800">
                                    {{ selectedTransport.notes || '-' }}
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
                            @click="openEditModal(selectedTransport); showViewModal = false">
                            Edit Vehicle
                        </button>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div v-if="showEditModal && selectedTransport"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
                <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div
                        class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">Edit Transport Vehicle</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Update vehicle, route, driver, capacity and status.
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
                                <label class="text-sm font-semibold text-slate-600">Vehicle Name *</label>
                                <input v-model="editForm.vehicle"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'vehicle')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Vehicle ID *</label>
                                <input v-model="editForm.vehicle_no"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'vehicle_no')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Vehicle Type</label>
                                <select v-model="editForm.type"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>Bus</option>
                                    <option>Van</option>
                                    <option>Mini Bus</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Route *</label>
                                <input v-model="editForm.route"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'route')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Driver Name</label>
                                <input v-model="editForm.driver"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Driver Phone</label>
                                <input v-model="editForm.phone"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Plate Number *</label>
                                <input v-model="editForm.plate"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'plate')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Capacity *</label>
                                <input v-model="editForm.capacity" type="number" min="0"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'capacity')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Assigned Students *</label>
                                <input v-model="editForm.students" type="number" min="0"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'students')" />
                                <p v-if="fieldError(editFrontendErrors, editForm, 'students')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(editFrontendErrors, editForm, 'students') }}
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
                                <label class="text-sm font-semibold text-slate-600">Route Details</label>
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
                            :disabled="editForm.processing || !canUpdate" @click="updateTransport">
                            {{ editForm.processing ? 'Updating...' : 'Update Vehicle' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>