<script setup>
import { ref, computed } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    activities: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            type: '',
            class_name: '',
            status: '',
        }),
    },
    activityStats: {
        type: Object,
        default: () => ({
            cards: [],
            overview: {},
            participationBars: [],
        }),
    },
    types: {
        type: Array,
        default: () => [],
    },
    classNames: {
        type: Array,
        default: () => [],
    },
})

const activityList = computed(() => props.activities || { data: [], links: [] })
const statCards = computed(() => props.activityStats?.cards || [])
const overview = computed(() => props.activityStats?.overview || {})
const participationBars = computed(() => props.activityStats?.participationBars || [])

const defaultTypes = ['Sports', 'Academic', 'Creative', 'Outdoor', 'Cultural']
const defaultClasses = ['All Classes', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 10', 'Grade 11']

const typeOptions = computed(() => [...new Set([...(props.types || []), ...defaultTypes])])
const classOptions = computed(() => [...new Set([...(props.classNames || []), ...defaultClasses])])

const showCreateModal = ref(false)
const showViewModal = ref(false)
const showEditModal = ref(false)
const selectedActivity = ref(null)

const filterForm = useForm({
    search: props.filters?.search || '',
    type: props.filters?.type || '',
    class_name: props.filters?.class_name || '',
    status: props.filters?.status || '',
})

const form = useForm({
    activity_no: '',
    title: '',
    type: '',
    class_name: '',
    activity_date: '',
    activity_time: '',
    venue: '',
    organizer: '',
    status: 'Upcoming',
    description: '',
})

const editForm = useForm({
    activity_no: '',
    title: '',
    type: '',
    class_name: '',
    activity_date: '',
    activity_time: '',
    venue: '',
    organizer: '',
    status: 'Upcoming',
    description: '',
})

const validateActivity = (targetForm) => {
    const errors = {}

    if (!targetForm.activity_no.trim()) errors.activity_no = 'Activity ID is required.'
    if (!targetForm.title.trim()) errors.title = 'Activity title is required.'

    if (targetForm.description && targetForm.description.length > 1000) {
        errors.description = 'Description must not be greater than 1000 characters.'
    }

    return errors
}

const frontendErrors = computed(() => validateActivity(form))
const editFrontendErrors = computed(() => validateActivity(editForm))

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
    form.status = 'Upcoming'
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

const submitActivity = () => {
    if (!canSubmit.value || form.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    form.post(route('activities.store'), {
        preserveScroll: true,

        onSuccess: () => {
            toast.success('Activity created successfully.')
            resetForm()
            showCreateModal.value = false
        },

        onError: (errors) => {
            if (errors.server) {
                toast.error(errors.server)
                return
            }

            // Show first validation error
            const firstError = Object.values(errors)[0]
            if (firstError) toast.error(firstError)

            // OR show all errors (optional)
            // Object.values(errors).forEach(err => toast.error(err))
        },
    })
}

const openViewModal = (activity) => {
    selectedActivity.value = activity
    showViewModal.value = true
}

const closeViewModal = () => {
    selectedActivity.value = null
    showViewModal.value = false
}

const openEditModal = (activity) => {
    selectedActivity.value = activity

    editForm.activity_no = activity.activity_no || ''
    editForm.title = activity.title || ''
    editForm.type = activity.type || ''
    editForm.class_name = activity.class_name || ''
    editForm.activity_date = activity.activity_date || ''
    editForm.activity_time = activity.activity_time || ''
    editForm.venue = activity.venue || ''
    editForm.organizer = activity.organizer || ''
    editForm.status = activity.status || 'Upcoming'
    editForm.description = activity.description || ''

    editForm.clearErrors()
    showEditModal.value = true
}

const closeEditModal = () => {
    selectedActivity.value = null
    editForm.clearErrors()
    showEditModal.value = false
}

const updateActivity = () => {
    if (!selectedActivity.value || !canUpdate.value || editForm.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    editForm.put(route('activities.update', selectedActivity.value.id), {
        preserveScroll: true,

        onSuccess: () => {
            toast.success('Activity updated successfully.')
            closeEditModal()
        },

        onError: (errors) => {
            if (errors.edit_server) {
                toast.error(errors.edit_server)
                return
            }

            const firstError = Object.values(errors)[0]
            if (firstError) toast.error(firstError)

            // OR show all errors
            // Object.values(errors).forEach(err => toast.error(err))
        },
    })
}

const deleteActivity = (activity) => {
    if (!confirm(`Delete activity ${activity.activity_no}?`)) return

    router.delete(route('activities.destroy', activity.id), {
        preserveScroll: true,
        onSuccess: () => toast.success('Activity deleted successfully.'),
        onError: () => toast.error('Unable to delete activity.'),
    })
}

const applyFilters = () => {
    router.get(route('activities'), {
        search: filterForm.search,
        type: filterForm.type,
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
    filterForm.type = ''
    filterForm.class_name = ''
    filterForm.status = ''

    router.get(route('activities'), {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}
</script>

<template>
    <AppLayout title="Activities">
        <div class="space-y-6">
            <!-- Header -->
            <div class="rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 ring-1 ring-slate-200 sm:p-6 lg:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">
                            School Activities
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                            Activities
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                            Plan school activities, manage events, venues, organizers, participation and activity schedules.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button
                            type="button"
                            class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50"
                        >
                            Export Activities
                        </button>

                        <button
                            type="button"
                            class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800"
                            @click="openCreateModal"
                        >
                            Add Activity
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
                                index === 1 ? 'bg-gradient-to-br from-amber-500 to-orange-400' : '',
                                index === 2 ? 'bg-gradient-to-br from-emerald-500 to-teal-400' : '',
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
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-black text-slate-950">
                                Participation Overview
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Activity distribution percentage by type.
                            </p>
                        </div>

                        <span class="w-fit rounded-full bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                            {{ overview.total || 0 }} Total Activities
                        </span>
                    </div>

                    <div class="mt-8 space-y-5">
                        <div v-for="item in participationBars" :key="item.label">
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

                        <p
                            v-if="participationBars.length === 0"
                            class="rounded-2xl bg-slate-50 p-5 text-sm font-bold text-slate-400"
                        >
                            No activity type data available.
                        </p>
                    </div>
                </div>

                <div class="rounded-3xl bg-gradient-to-br from-indigo-600 to-sky-500 p-6 text-white shadow-lg shadow-indigo-100">
                    <h3 class="text-lg font-black">
                        Activity Summary
                    </h3>

                    <p class="mt-1 text-sm text-white/75">
                        Current academic term overview
                    </p>

                    <div class="mt-6 space-y-4">
                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Upcoming</p>
                            <p class="mt-1 text-xl font-black">{{ overview.upcoming || 0 }} Events</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Completed</p>
                            <p class="mt-1 text-xl font-black">{{ overview.completed || 0 }} Events</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Cancelled</p>
                            <p class="mt-1 text-xl font-black">{{ overview.cancelled || 0 }} Events</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Cards -->
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <div
                    v-for="activity in activityList.data"
                    :key="activity.id"
                    class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-lg"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                {{ activity.activity_no }}
                            </p>

                            <h2 class="mt-2 text-lg font-black text-slate-950">
                                {{ activity.title }}
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                {{ activity.type || '-' }} · {{ activity.class_name || '-' }}
                            </p>
                        </div>

                        <span
                            class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                            :class="
                                activity.status === 'Upcoming'
                                    ? 'bg-indigo-50 text-indigo-700 ring-indigo-100'
                                    : activity.status === 'Completed'
                                        ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                        : 'bg-rose-50 text-rose-700 ring-rose-100'
                            "
                        >
                            {{ activity.status }}
                        </span>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-indigo-50 p-4">
                            <p class="text-xs font-semibold text-indigo-500">Date</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ activity.activity_date || '-' }}</p>
                        </div>

                        <div class="rounded-2xl bg-sky-50 p-4">
                            <p class="text-xs font-semibold text-sky-500">Time</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ activity.activity_time || '-' }}</p>
                        </div>

                        <div class="rounded-2xl bg-violet-50 p-4">
                            <p class="text-xs font-semibold text-violet-500">Venue</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ activity.venue || '-' }}</p>
                        </div>

                        <div class="rounded-2xl bg-amber-50 p-4">
                            <p class="text-xs font-semibold text-amber-500">Organizer</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ activity.organizer || '-' }}</p>
                        </div>
                    </div>

                    <div class="mt-5 flex gap-2">
                        <button
                            type="button"
                            class="flex-1 rounded-2xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                            @click="openViewModal(activity)"
                        >
                            View
                        </button>

                        <button
                            type="button"
                            class="flex-1 rounded-2xl bg-indigo-50 px-4 py-2.5 text-sm font-bold text-indigo-700 transition hover:bg-indigo-100"
                            @click="openEditModal(activity)"
                        >
                            Edit
                        </button>
                    </div>
                </div>

                <div
                    v-if="activityList.data.length === 0"
                    class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 md:col-span-2 xl:col-span-4"
                >
                    <p class="text-sm font-bold text-slate-900">No activities found</p>
                    <p class="mt-1 text-xs text-slate-500">Create activities to see schedule records.</p>
                </div>
            </div>
            <!-- Filters -->
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <form class="grid gap-4 md:grid-cols-2 xl:grid-cols-6" @submit.prevent="applyFilters">
                    <div class="xl:col-span-2">
                        <label class="text-sm font-semibold text-slate-600">Search Activity</label>
                        <input
                            v-model="filterForm.search"
                            type="text"
                            placeholder="Search by title, type, venue or organizer"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        />
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Type</label>
                        <select
                            v-model="filterForm.type"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        >
                            <option value="">All Types</option>
                            <option v-for="type in typeOptions" :key="type" :value="type">
                                {{ type }}
                            </option>
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
                            <option>Upcoming</option>
                            <option>Completed</option>
                            <option>Cancelled</option>
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
            <!-- Activity Table -->
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">
                            Activity Schedule
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Complete school activity records.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800"
                        @click="openCreateModal"
                    >
                        New Activity
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1050px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Activity</th>
                                <th class="px-6 py-4">ID</th>
                                <th class="px-6 py-4">Type</th>
                                <th class="px-6 py-4">Class</th>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Venue</th>
                                <th class="px-6 py-4">Organizer</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="activity in activityList.data"
                                :key="activity.id"
                                class="transition hover:bg-indigo-50/40"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 to-sky-100 text-sm font-black text-indigo-700">
                                            {{ activity.title?.charAt(0) || 'A' }}
                                        </div>

                                        <div>
                                            <p class="font-bold text-slate-950">
                                                {{ activity.title }}
                                            </p>

                                            <p class="text-xs text-slate-400">
                                                {{ activity.activity_time || '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-700">
                                    {{ activity.activity_no }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ activity.type || '-' }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ activity.class_name || '-' }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ activity.activity_date || '-' }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ activity.venue || '-' }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ activity.organizer || '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                                        :class="
                                            activity.status === 'Upcoming'
                                                ? 'bg-indigo-50 text-indigo-700 ring-indigo-100'
                                                : activity.status === 'Completed'
                                                    ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                                    : 'bg-rose-50 text-rose-700 ring-rose-100'
                                        "
                                    >
                                        {{ activity.status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            type="button"
                                            class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200"
                                            @click="openViewModal(activity)"
                                        >
                                            View
                                        </button>

                                        <button
                                            type="button"
                                            class="rounded-xl bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100"
                                            @click="openEditModal(activity)"
                                        >
                                            Edit
                                        </button>

                                        <button
                                            type="button"
                                            class="rounded-xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100"
                                            @click="deleteActivity(activity)"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="activityList.data.length === 0">
                                <td colspan="9" class="px-6 py-10 text-center text-sm font-bold text-slate-400">
                                    No activities found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    v-if="activityList.links && activityList.links.length > 3"
                    class="flex flex-col gap-4 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <p class="text-sm font-semibold text-slate-500">
                        Showing
                        <span class="font-black text-slate-800">{{ activityList.from || 0 }}</span>
                        to
                        <span class="font-black text-slate-800">{{ activityList.to || 0 }}</span>
                        of
                        <span class="font-black text-slate-800">{{ activityList.total || 0 }}</span>
                        activities
                    </p>

                    <div class="flex flex-wrap gap-2">
                        <Link
                            v-for="link in activityList.links"
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
                            <h2 class="text-xl font-black text-slate-950">
                                Add Activity
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Add activity details, date, venue, organizer and status.
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
                                <label class="text-sm font-semibold text-slate-600">Activity Title *</label>
                                <input
                                    v-model="form.title"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'title')"
                                    placeholder="Annual Sports Day"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'title')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'title') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Activity ID *</label>
                                <input
                                    v-model="form.activity_no"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'activity_no')"
                                    placeholder="ACT-1005"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'activity_no')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'activity_no') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Type</label>
                                <select
                                    v-model="form.type"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option value="">Select type</option>
                                    <option v-for="type in typeOptions" :key="type" :value="type">
                                        {{ type }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Class</label>
                                <select
                                    v-model="form.class_name"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option value="">Select class</option>
                                    <option v-for="className in classOptions" :key="className" :value="className">
                                        {{ className }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Date</label>
                                <input
                                    v-model="form.activity_date"
                                    type="date"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Time</label>
                                <input
                                    v-model="form.activity_time"
                                    type="time"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Venue</label>
                                <input
                                    v-model="form.venue"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                    placeholder="Main Ground"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Organizer</label>
                                <input
                                    v-model="form.organizer"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                    placeholder="Sports Department"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status</label>
                                <select
                                    v-model="form.status"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option>Upcoming</option>
                                    <option>Completed</option>
                                    <option>Cancelled</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Description</label>
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                    placeholder="Optional activity description"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'description')"
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
                            @click="submitActivity"
                        >
                            {{ form.processing ? 'Saving...' : 'Save Activity' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- View Modal -->
            <div
                v-if="showViewModal && selectedActivity"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
            >
                <div class="w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">
                                {{ selectedActivity.title }}
                            </h2>

                            <p class="mt-1 text-sm font-semibold text-slate-500">
                                {{ selectedActivity.activity_no }} · {{ selectedActivity.type || '-' }}
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
                                    {{ selectedActivity.class_name || '-' }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Date</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">
                                    {{ selectedActivity.activity_date || '-' }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Time</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">
                                    {{ selectedActivity.activity_time || '-' }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Venue</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">
                                    {{ selectedActivity.venue || '-' }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Organizer</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">
                                    {{ selectedActivity.organizer || '-' }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Status</p>
                                <p class="mt-1">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                                        :class="
                                            selectedActivity.status === 'Upcoming'
                                                ? 'bg-indigo-50 text-indigo-700 ring-indigo-100'
                                                : selectedActivity.status === 'Completed'
                                                    ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                                    : 'bg-rose-50 text-rose-700 ring-rose-100'
                                        "
                                    >
                                        {{ selectedActivity.status }}
                                    </span>
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100 sm:col-span-2">
                                <p class="text-xs font-bold uppercase text-slate-400">Description</p>
                                <p class="mt-1 text-sm font-bold leading-6 text-slate-800">
                                    {{ selectedActivity.description || '-' }}
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
                            @click="openEditModal(selectedActivity); showViewModal = false"
                        >
                            Edit Activity
                        </button>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div
                v-if="showEditModal && selectedActivity"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
            >
                <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">
                                Edit Activity
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Update activity details, date, venue, organizer and status.
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
                                <label class="text-sm font-semibold text-slate-600">Activity Title *</label>
                                <input
                                    v-model="editForm.title"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'title')"
                                />
                                <p v-if="fieldError(editFrontendErrors, editForm, 'title')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(editFrontendErrors, editForm, 'title') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Activity ID *</label>
                                <input
                                    v-model="editForm.activity_no"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'activity_no')"
                                />
                                <p v-if="fieldError(editFrontendErrors, editForm, 'activity_no')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(editFrontendErrors, editForm, 'activity_no') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Type</label>
                                <select
                                    v-model="editForm.type"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option value="">Select type</option>
                                    <option v-for="type in typeOptions" :key="type" :value="type">
                                        {{ type }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Class</label>
                                <select
                                    v-model="editForm.class_name"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option value="">Select class</option>
                                    <option v-for="className in classOptions" :key="className" :value="className">
                                        {{ className }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Date</label>
                                <input
                                    v-model="editForm.activity_date"
                                    type="date"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Time</label>
                                <input
                                    v-model="editForm.activity_time"
                                    type="time"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Venue</label>
                                <input
                                    v-model="editForm.venue"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Organizer</label>
                                <input
                                    v-model="editForm.organizer"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status</label>
                                <select
                                    v-model="editForm.status"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option>Upcoming</option>
                                    <option>Completed</option>
                                    <option>Cancelled</option>
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
                                <p v-if="fieldError(editFrontendErrors, editForm, 'description')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(editFrontendErrors, editForm, 'description') }}
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
                            @click="updateActivity"
                        >
                            {{ editForm.processing ? 'Updating...' : 'Update Activity' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>