<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    notifications: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            type: '',
            audience: '',
            status: '',
        }),
    },
    notificationStats: {
        type: Object,
        default: () => ({
            overview: {},
            deliveryBars: [],
        }),
    },
})

const notificationList = computed(() => props.notifications || { data: [], links: [] })
const latestNotifications = computed(() => {
    return (notificationList.value.data || []).slice(0, 5)
})
const overview = computed(() => props.notificationStats?.overview || {})
const deliveryItems = computed(() => props.notificationStats?.deliveryBars || [])

const showCreateModal = ref(false)
const showViewModal = ref(false)
const showEditModal = ref(false)
const selectedNotification = ref(null)

const carouselRef = ref(null)

const scrollCarousel = (direction) => {
    if (!carouselRef.value) return

    carouselRef.value.scrollBy({
        left: direction === 'next' ? 360 : -360,
        behavior: 'smooth',
    })
}

const typeOptions = ['General', 'Fee', 'Exam', 'Meeting', 'Transport', 'Attendance']
const audienceOptions = ['Students', 'Parents', 'Teachers', 'Staffs', 'All Users']
const channelOptions = ['App', 'Email', 'SMS', 'Email / SMS', 'All Channels']
const statusOptions = ['Sent', 'Scheduled', 'Unread', 'Failed']

const filterForm = useForm({
    search: props.filters?.search || '',
    type: props.filters?.type || '',
    audience: props.filters?.audience || '',
    status: props.filters?.status || '',
})

const form = useForm({
    notification_no: '',
    title: '',
    message: '',
    type: 'General',
    audience: 'Students',
    channel: 'App',
    date: '',
    status: 'Scheduled',
})

const editForm = useForm({
    notification_no: '',
    title: '',
    message: '',
    type: 'General',
    audience: 'Students',
    channel: 'App',
    date: '',
    status: 'Scheduled',
})

const validateNotification = (targetForm) => {
    const errors = {}

    if (!targetForm.notification_no.trim()) errors.notification_no = 'Notification ID is required.'
    if (!targetForm.title.trim()) errors.title = 'Title is required.'
    if (!targetForm.message.trim()) errors.message = 'Message is required.'
    if (!targetForm.type.trim()) errors.type = 'Type is required.'
    if (!targetForm.audience.trim()) errors.audience = 'Audience is required.'
    if (!targetForm.channel.trim()) errors.channel = 'Channel is required.'
    if (!targetForm.date) errors.date = 'Date is required.'
    if (!targetForm.status.trim()) errors.status = 'Status is required.'

    if (targetForm.message && targetForm.message.length > 1000) {
        errors.message = 'Message must not be greater than 1000 characters.'
    }

    return errors
}

const frontendErrors = computed(() => validateNotification(form))
const editFrontendErrors = computed(() => validateNotification(editForm))

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
    form.type = 'General'
    form.audience = 'Students'
    form.channel = 'App'
    form.status = 'Scheduled'
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

const submitNotification = () => {
    if (!canSubmit.value || form.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    form.post(route('notifications.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Notification created successfully.')
            resetForm()
            showCreateModal.value = false
        },
        onError: (errors) => {
            if (errors.server) return toast.error(errors.server)
            showFirstError(errors)
        },
    })
}

const openViewModal = (notification) => {
    selectedNotification.value = notification
    showViewModal.value = true
}

const closeViewModal = () => {
    selectedNotification.value = null
    showViewModal.value = false
}

const openEditModal = (notification) => {
    selectedNotification.value = notification

    editForm.notification_no = notification.notification_no || ''
    editForm.title = notification.title || ''
    editForm.message = notification.message || ''
    editForm.type = notification.type || 'General'
    editForm.audience = notification.audience || 'Students'
    editForm.channel = notification.channel || 'App'
    editForm.date = notification.date || ''
    editForm.status = notification.status || 'Scheduled'

    editForm.clearErrors()
    showEditModal.value = true
}

const closeEditModal = () => {
    selectedNotification.value = null
    editForm.clearErrors()
    showEditModal.value = false
}

const updateNotification = () => {
    if (!selectedNotification.value || !canUpdate.value || editForm.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    editForm.put(route('notifications.update', selectedNotification.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Notification updated successfully.')
            closeEditModal()
        },
        onError: (errors) => {
            if (errors.edit_server) return toast.error(errors.edit_server)
            showFirstError(errors)
        },
    })
}

const deleteNotification = (notification) => {
    if (!confirm(`Delete notification ${notification.notification_no}?`)) return

    router.delete(route('notifications.destroy', notification.id), {
        preserveScroll: true,
        onSuccess: () => toast.success('Notification deleted successfully.'),
        onError: () => toast.error('Unable to delete notification.'),
    })
}

const resendNotification = (notification) => {
    toast.info(`Resend action for ${notification.notification_no}`)
}

const applyFilters = () => {
    router.get(route('notifications'), {
        search: filterForm.search,
        type: filterForm.type,
        audience: filterForm.audience,
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
    filterForm.audience = ''
    filterForm.status = ''

    router.get(route('notifications'), {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

const statusClass = (status) => {
    return status === 'Sent'
        ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
        : status === 'Scheduled'
            ? 'bg-indigo-50 text-indigo-700 ring-indigo-100'
            : status === 'Unread'
                ? 'bg-amber-50 text-amber-700 ring-amber-100'
                : 'bg-rose-50 text-rose-700 ring-rose-100'
}

//impport function to handle file import
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

    importForm.post(route('notifications.import'), {
        preserveScroll: true,
        forceFormData: true,
        onProgress: (progress) => {
            if (progress?.percentage) importProgress.value = progress.percentage
        },
        onSuccess: () => {
            toast.success('Notifications imported successfully.')
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
const deliveryChartRef = ref(null)
let deliveryChartInstance = null

const renderDeliveryChart = () => {
    if (!deliveryChartRef.value) return

    if (deliveryChartInstance) {
        deliveryChartInstance.destroy()
    }

    deliveryChartInstance = new Chart(deliveryChartRef.value, {
        type: 'bar',
        data: {
            labels: deliveryItems.value.map(item => item.label),
            datasets: [
                {
                    label: 'Delivery Share',
                    data: deliveryItems.value.map(item => item.value),
                    borderRadius: 12,
                    borderWidth: 0,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: value => `${value}%`,
                    },
                },
            },
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    callbacks: {
                        label: ctx => `${ctx.raw}% delivery share`,
                    },
                },
            },
        },
    })
}

onMounted(renderDeliveryChart)

watch(deliveryItems, renderDeliveryChart, {
    deep: true,
})
</script>

<template>
    <AppLayout title="Notifications">
        <div class="space-y-6">
            <div v-if="isImporting" class="rounded-2xl bg-white p-3 shadow-sm ring-1 ring-slate-200">
                <div class="h-2 overflow-hidden rounded-full bg-emerald-100">
                    <div class="h-full rounded-full bg-emerald-600 transition-all duration-300"
                        :style="{ width: `${importProgress}%` }"></div>
                </div>
            </div>
            <!-- Header -->
            <div
                class="rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 ring-1 ring-slate-200 sm:p-6 lg:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">Notification Center</p>
                        <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">Notifications
                        </h1>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                            Manage school alerts, parent messages, fee reminders, exam notices and delivery channels.
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
                            Send Notification
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-semibold text-slate-500">Total Notifications</p>
                    <h2 class="mt-3 text-3xl font-black text-slate-950">{{ overview.total || 0 }}</h2>
                    <p class="mt-3 text-xs font-bold text-indigo-600">All notification records</p>
                </div>

                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-semibold text-slate-500">Unread</p>
                    <h2 class="mt-3 text-3xl font-black text-slate-950">{{ overview.unread || 0 }}</h2>
                    <p class="mt-3 text-xs font-bold text-amber-600">Pending attention</p>
                </div>

                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-semibold text-slate-500">Sent Today</p>
                    <h2 class="mt-3 text-3xl font-black text-slate-950">{{ overview.sent_today || 0 }}</h2>
                    <p class="mt-3 text-xs font-bold text-emerald-600">Today’s activity</p>
                </div>

                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-semibold text-slate-500">Failed</p>
                    <h2 class="mt-3 text-3xl font-black text-slate-950">{{ overview.failed || 0 }}</h2>
                    <p class="mt-3 text-xs font-bold text-rose-600">Needs retry</p>
                </div>
            </div>





            <!-- Notification Carousel -->
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">Recent Notifications</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Swipe or use arrows to browse notification cards.
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <button type="button"
                            class="rounded-2xl bg-slate-100 px-4 py-2 text-sm font-black text-slate-700 transition hover:bg-slate-200"
                            @click="scrollCarousel('prev')">
                            ←
                        </button>

                        <button type="button"
                            class="rounded-2xl bg-slate-950 px-4 py-2 text-sm font-black text-white transition hover:bg-slate-800"
                            @click="scrollCarousel('next')">
                            →
                        </button>
                    </div>
                </div>

                <div ref="carouselRef" class="mt-5 flex gap-5 overflow-x-auto scroll-smooth pb-3">
                    <div v-for="notification in latestNotifications" :key="notification.id"
                        class="m-2 min-w-[310px] max-w-[310px] rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-lg sm:min-w-[360px] sm:max-w-[360px]">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                    {{ notification.notification_no }}
                                </p>

                                <h2 class="mt-2 text-lg font-black text-slate-950">
                                    {{ notification.title }}
                                </h2>

                                <p class="mt-1 text-sm text-slate-500">
                                    {{ notification.type }} · {{ notification.audience }}
                                </p>
                            </div>

                            <span class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                                :class="statusClass(notification.status)">
                                {{ notification.status }}
                            </span>
                        </div>

                        <p class="mt-4 line-clamp-2 text-sm leading-6 text-slate-500">
                            {{ notification.message }}
                        </p>

                        <div class="mt-6 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl bg-indigo-50 p-4">
                                <p class="text-xs font-semibold text-indigo-500">Channel</p>
                                <p class="mt-1 text-sm font-bold text-slate-900">{{ notification.channel }}</p>
                            </div>

                            <div class="rounded-2xl bg-sky-50 p-4">
                                <p class="text-xs font-semibold text-sky-500">Date</p>
                                <p class="mt-1 text-sm font-bold text-slate-900">{{ notification.date }}</p>
                            </div>
                        </div>

                        <div class="mt-5 flex gap-2">
                            <button type="button"
                                class="flex-1 rounded-2xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                                @click="openViewModal(notification)">
                                View
                            </button>

                            <button type="button"
                                class="flex-1 rounded-2xl bg-indigo-50 px-4 py-2.5 text-sm font-bold text-indigo-700 transition hover:bg-indigo-100"
                                @click="resendNotification(notification)">
                                Resend
                            </button>
                        </div>
                    </div>

                    <div v-if="notificationList.data.length === 0"
                        class="min-w-full rounded-3xl bg-slate-50 p-10 text-center ring-1 ring-slate-100">
                        <p class="text-sm font-bold text-slate-400">No notifications found.</p>
                    </div>
                </div>
            </div>
            <!-- Filters -->
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <form class="grid gap-4 md:grid-cols-2 xl:grid-cols-6" @submit.prevent="applyFilters">
                    <div class="xl:col-span-2">
                        <label class="text-sm font-semibold text-slate-600">Search Notification</label>
                        <input v-model="filterForm.search" type="text"
                            placeholder="Search by title, type, audience or channel"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Type</label>
                        <select v-model="filterForm.type"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option value="">All Types</option>
                            <option v-for="type in typeOptions" :key="type" :value="type">{{ type }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Audience</label>
                        <select v-model="filterForm.audience"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option value="">All Audience</option>
                            <option v-for="audience in audienceOptions" :key="audience" :value="audience">{{ audience }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Status</label>
                        <select v-model="filterForm.status"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option value="">All Status</option>
                            <option v-for="status in statusOptions" :key="status" :value="status">{{ status }}</option>
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
                        <h2 class="text-lg font-black text-slate-950">Notification Records</h2>
                        <p class="mt-1 text-sm text-slate-500">Complete notification records.</p>
                    </div>

                    <button type="button"
                        class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800"
                        @click="openCreateModal">
                        New Notification
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1100px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Notification</th>
                                <th class="px-6 py-4">ID</th>
                                <th class="px-6 py-4">Type</th>
                                <th class="px-6 py-4">Audience</th>
                                <th class="px-6 py-4">Channel</th>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="notification in notificationList.data" :key="notification.id"
                                class="transition hover:bg-indigo-50/40">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex size-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 to-sky-100 text-sm font-black text-indigo-700">
                                            {{ notification.title?.charAt(0) || 'N' }}
                                        </div>

                                        <div>
                                            <p class="font-bold text-slate-950">{{ notification.title }}</p>
                                            <p class="line-clamp-1 text-xs text-slate-400">{{ notification.message }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-700">{{ notification.notification_no }}
                                </td>
                                <td class="px-6 py-4 text-slate-500">{{ notification.type }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ notification.audience }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ notification.channel }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ notification.date }}</td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                                        :class="statusClass(notification.status)">
                                        {{ notification.status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button type="button"
                                            class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200"
                                            @click="openViewModal(notification)">
                                            View
                                        </button>

                                        <button type="button"
                                            class="rounded-xl bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100"
                                            @click="openEditModal(notification)">
                                            Edit
                                        </button>

                                        <button type="button"
                                            class="rounded-xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100"
                                            @click="deleteNotification(notification)">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="notificationList.data.length === 0">
                                <td colspan="8" class="px-6 py-10 text-center text-sm font-bold text-slate-400">
                                    No notifications found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="notificationList.total > notificationList.per_page"
                    class="flex flex-col gap-4 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm font-semibold text-slate-500">
                        Showing <span class="font-black text-slate-800">{{ notificationList.from || 0 }}</span>
                        to <span class="font-black text-slate-800">{{ notificationList.to || 0 }}</span>
                        of <span class="font-black text-slate-800">{{ notificationList.total || 0 }}</span>
                        notifications
                    </p>

                    <div class="flex flex-wrap gap-2">
                        <Link v-for="(link, index) in notificationList.links" :key="index" :href="link.url || '#'"
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
                            <h2 class="text-xl font-black text-slate-950">Send Notification</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Create a school alert for students, parents, teachers or staffs.
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
                                <label class="text-sm font-semibold text-slate-600">Title *</label>
                                <input v-model="form.title"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'title')"
                                    placeholder="Fee Payment Reminder" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Notification ID *</label>
                                <input v-model="form.notification_no"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'notification_no')"
                                    placeholder="NTF-1005" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Type *</label>
                                <select v-model="form.type"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option v-for="type in typeOptions" :key="type">{{ type }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Audience *</label>
                                <select v-model="form.audience"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option v-for="audience in audienceOptions" :key="audience">{{ audience }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Channel *</label>
                                <select v-model="form.channel"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option v-for="channel in channelOptions" :key="channel">{{ channel }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Date *</label>
                                <input v-model="form.date" type="date"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'date')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status *</label>
                                <select v-model="form.status"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option v-for="status in statusOptions" :key="status">{{ status }}</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Message *</label>
                                <textarea v-model="form.message" rows="4" placeholder="Write notification message..."
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'message')"></textarea>
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
                            :disabled="form.processing || !canSubmit" @click="submitNotification">
                            {{ form.processing ? 'Saving...' : 'Save Notification' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- View Modal -->
            <div v-if="showViewModal && selectedNotification"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
                <div class="w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div
                        class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">{{ selectedNotification.title }}</h2>
                            <p class="mt-1 text-sm font-semibold text-slate-500">
                                {{ selectedNotification.notification_no }} · {{ selectedNotification.type }}
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
                                <p class="text-xs font-bold uppercase text-indigo-500">Type</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedNotification.type }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Audience</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedNotification.audience }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Channel</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedNotification.channel }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Date</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedNotification.date }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Status</p>
                                <span class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1"
                                    :class="statusClass(selectedNotification.status)">
                                    {{ selectedNotification.status }}
                                </span>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100 sm:col-span-2">
                                <p class="text-xs font-bold uppercase text-slate-400">Message</p>
                                <p class="mt-1 text-sm font-bold leading-6 text-slate-800">
                                    {{ selectedNotification.message }}
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
                            @click="openEditModal(selectedNotification); showViewModal = false">
                            Edit Notification
                        </button>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div v-if="showEditModal && selectedNotification"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
                <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div
                        class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">Edit Notification</h2>
                            <p class="mt-1 text-sm text-slate-500">Update notification details.</p>
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
                                <label class="text-sm font-semibold text-slate-600">Title *</label>
                                <input v-model="editForm.title"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'title')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Notification ID *</label>
                                <input v-model="editForm.notification_no"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'notification_no')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Type *</label>
                                <select v-model="editForm.type"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option v-for="type in typeOptions" :key="type">{{ type }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Audience *</label>
                                <select v-model="editForm.audience"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option v-for="audience in audienceOptions" :key="audience">{{ audience }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Channel *</label>
                                <select v-model="editForm.channel"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option v-for="channel in channelOptions" :key="channel">{{ channel }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Date *</label>
                                <input v-model="editForm.date" type="date"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'date')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status *</label>
                                <select v-model="editForm.status"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option v-for="status in statusOptions" :key="status">{{ status }}</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Message *</label>
                                <textarea v-model="editForm.message" rows="4"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'message')"></textarea>
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
                            :disabled="editForm.processing || !canUpdate" @click="updateNotification">
                            {{ editForm.processing ? 'Updating...' : 'Update Notification' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>