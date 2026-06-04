<script setup>
import { computed, ref } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    admissions: {
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
            applied_class: '',
            status: '',
        }),
    },

    admissionStats: {
        type: Object,
        default: () => ({
            cards: [],
            overview: {},
            chart: {
                labels: [],
                data: [],
            },
        }),
    },
})

const showCreateModal = ref(false)
const showViewModal = ref(false)
const showEditModal = ref(false)
const selectedAdmission = ref(null)

const filterForm = useForm({
    search: props.filters.search || '',
    applied_class: props.filters.applied_class || '',
    status: props.filters.status || '',
})

const form = useForm({
    // application_no: '',
    student_name: '',
    email: '',
    phone: '',
    gender: '',
    date_of_birth: '',
    applied_class: '',
    previous_school: '',
    parent_name: '',
    parent_phone: '',
    parent_email: '',
    application_date: '',
    status: 'New',
    address: '',
    remarks: '',
})

const editForm = useForm({
    // application_no: '',
    student_name: '',
    email: '',
    phone: '',
    gender: '',
    date_of_birth: '',
    applied_class: '',
    previous_school: '',
    parent_name: '',
    parent_phone: '',
    parent_email: '',
    application_date: '',
    status: 'New',
    address: '',
    remarks: '',
})

const validateAdmission = (targetForm) => {
    const errors = {}

    // if (!targetForm.application_no.trim()) errors.application_no = 'Application number is required.'
    if (!targetForm.student_name.trim()) errors.student_name = 'Student name is required.'
    if (!targetForm.applied_class) errors.applied_class = 'Applied class is required.'
    if (!targetForm.parent_name.trim()) errors.parent_name = 'Parent name is required.'
    if (!targetForm.parent_phone.trim()) errors.parent_phone = 'Parent phone is required.'

    if (targetForm.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(targetForm.email)) {
        errors.email = 'Enter a valid student email.'
    }

    if (targetForm.parent_email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(targetForm.parent_email)) {
        errors.parent_email = 'Enter a valid parent email.'
    }

    if (targetForm.date_of_birth) {
        const dob = new Date(targetForm.date_of_birth)
        const today = new Date()
        today.setHours(0, 0, 0, 0)

        if (dob >= today) {
            errors.date_of_birth = 'Date of birth must be before today.'
        }
    }

    if (targetForm.phone && targetForm.phone.length > 30) {
        errors.phone = 'Phone number is too long.'
    }

    if (targetForm.parent_phone && targetForm.parent_phone.length > 30) {
        errors.parent_phone = 'Parent phone is too long.'
    }

    if (targetForm.address && targetForm.address.length > 1000) {
        errors.address = 'Address must not be greater than 1000 characters.'
    }

    if (targetForm.remarks && targetForm.remarks.length > 1000) {
        errors.remarks = 'Remarks must not be greater than 1000 characters.'
    }

    return errors
}

const frontendErrors = computed(() => validateAdmission(form))
const editFrontendErrors = computed(() => validateAdmission(editForm))

const canSubmit = computed(() => Object.keys(frontendErrors.value).length === 0)
const canUpdate = computed(() => Object.keys(editFrontendErrors.value).length === 0)

const applyFilters = () => {
    router.get(route('admissions'), {
        search: filterForm.search,
        applied_class: filterForm.applied_class,
        status: filterForm.status,
    }, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    })
}

const resetFilters = () => {
    filterForm.search = ''
    filterForm.applied_class = ''
    filterForm.status = ''

    router.get(route('admissions'), {}, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    })
}

const openCreateModal = () => {
    form.clearErrors()
    showCreateModal.value = true
}

const closeCreateModal = () => {
    form.clearErrors()
    showCreateModal.value = false
}

const resetForm = () => {
    form.reset()
    form.status = 'New'
    form.clearErrors()
}

const submitAdmission = () => {
    if (!canSubmit.value || form.processing) return

    form.post(route('admissions.store'), {
        preserveScroll: true,
        onSuccess: () => {
            resetForm()
            showCreateModal.value = false
        },
    })
}

const openViewModal = (admission) => {
    selectedAdmission.value = admission
    showViewModal.value = true
}

const closeViewModal = () => {
    selectedAdmission.value = null
    showViewModal.value = false
}

const openEditModal = (admission) => {
    selectedAdmission.value = admission

    // editForm.application_no = admission.application_no || ''
    editForm.student_name = admission.student_name || ''
    editForm.email = admission.email || ''
    editForm.phone = admission.phone || ''
    editForm.gender = admission.gender || ''
    editForm.date_of_birth = admission.date_of_birth || ''
    editForm.applied_class = admission.applied_class || ''
    editForm.previous_school = admission.previous_school || ''
    editForm.parent_name = admission.parent_name || ''
    editForm.parent_phone = admission.parent_phone || ''
    editForm.parent_email = admission.parent_email || ''
    editForm.application_date = admission.application_date || ''
    editForm.status = admission.status || 'New'
    editForm.address = admission.address || ''
    editForm.remarks = admission.remarks || ''

    editForm.clearErrors()
    showEditModal.value = true
}

const closeEditModal = () => {
    selectedAdmission.value = null
    editForm.clearErrors()
    showEditModal.value = false
}

const updateAdmission = () => {
    if (!selectedAdmission.value || !canUpdate.value || editForm.processing) return

    editForm.put(route('admissions.update', selectedAdmission.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeEditModal()
        },
    })
}
</script>

<template>
    <AppLayout title="Admissions">
        <div class="space-y-6">
            <!-- Header -->
            <div class="rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 ring-1 ring-slate-200 sm:p-6 lg:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">Admission Management</p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                            Admissions
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                            Manage student applications, parent information, review status, class requests and admission decisions.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800"
                        @click="openCreateModal"
                    >
                        New Application
                    </button>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div
                    v-for="stat in admissionStats.cards"
                    :key="stat.label"
                    class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                >
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-500">{{ stat.label }}</p>
                            <h2 class="mt-3 text-3xl font-black text-slate-950">{{ stat.value }}</h2>
                        </div>

                        <div :class="`flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br ${stat.color} text-white shadow-lg`">
                            <span class="text-lg font-black">{{ stat.icon }}</span>
                        </div>
                    </div>

                    <p class="mt-4 text-xs font-semibold text-slate-500">
                        {{ stat.note }}
                    </p>
                </div>
            </div>

            <!-- Filters -->
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <form class="grid gap-4 md:grid-cols-2 xl:grid-cols-5" @submit.prevent="applyFilters">
                    <div class="xl:col-span-2">
                        <label class="text-sm font-semibold text-slate-600">Search Application</label>
                        <input
                            v-model="filterForm.search"
                            type="text"
                            placeholder="Search by name, application no, email or phone"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        />
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Applied Class</label>
                        <select
                            v-model="filterForm.applied_class"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        >
                            <option value="">All Classes</option>
                            <option>Grade 5</option>
                            <option>Grade 6</option>
                            <option>Grade 7</option>
                            <option>Grade 8</option>
                            <option>Grade 9</option>
                            <option>Grade 10</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Status</label>
                        <select
                            v-model="filterForm.status"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        >
                            <option value="">All Status</option>
                            <option>New</option>
                            <option>Under Review</option>
                            <option>Approved</option>
                            <option>Rejected</option>
                            <option>Waitlisted</option>
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
                    <h2 class="text-lg font-black text-slate-950">Application Overview</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Live admission application summary from database.
                    </p>

                    <div class="mt-6 grid gap-4 sm:grid-cols-4">
                        <div class="rounded-2xl bg-indigo-50 p-5">
                            <p class="text-xs font-semibold text-indigo-600">New</p>
                            <p class="mt-2 text-2xl font-black text-slate-950">
                                {{ admissionStats.overview.new_applications || 0 }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">Fresh applications</p>
                        </div>

                        <div class="rounded-2xl bg-amber-50 p-5">
                            <p class="text-xs font-semibold text-amber-600">Waitlisted</p>
                            <p class="mt-2 text-2xl font-black text-slate-950">
                                {{ admissionStats.overview.waitlisted || 0 }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">Pending seats</p>
                        </div>

                        <div class="rounded-2xl bg-emerald-50 p-5">
                            <p class="text-xs font-semibold text-emerald-600">Parent Email</p>
                            <p class="mt-2 text-2xl font-black text-slate-950">
                                {{ admissionStats.overview.with_parent_email || 0 }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">Contact available</p>
                        </div>

                        <div class="rounded-2xl bg-rose-50 p-5">
                            <p class="text-xs font-semibold text-rose-600">Missing Email</p>
                            <p class="mt-2 text-2xl font-black text-slate-950">
                                {{ admissionStats.overview.missing_parent_email || 0 }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">Need follow-up</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-gradient-to-br from-indigo-600 to-sky-500 p-6 text-white shadow-lg shadow-indigo-100">
                    <h3 class="text-lg font-black">Application Status</h3>
                    <p class="text-sm text-white/75">Quick status distribution</p>

                    <div class="mt-8 space-y-4">
                        <div
                            v-for="(label, index) in admissionStats.chart.labels"
                            :key="label"
                            class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20"
                        >
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-bold">{{ label }}</p>
                                <p class="text-sm font-black">{{ admissionStats.chart.data[index] || 0 }}</p>
                            </div>

                            <div class="mt-3 h-2 overflow-hidden rounded-full bg-white/20">
                                <div
                                    class="h-full rounded-full bg-white"
                                    :style="{
                                        width: `${Math.min(((admissionStats.chart.data[index] || 0) / Math.max(...admissionStats.chart.data, 1)) * 100, 100)}%`
                                    }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">Admission Applications</h2>
                        <p class="mt-1 text-sm text-slate-500">Application list from database</p>
                    </div>

                    <button
                        type="button"
                        class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white hover:bg-slate-800"
                        @click="openCreateModal"
                    >
                        New Application
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1050px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Applicant</th>
                                <th class="px-6 py-4">Application No</th>
                                <th class="px-6 py-4">Applied Class</th>
                                <th class="px-6 py-4">Parent</th>
                                <th class="px-6 py-4">Phone</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="admission in admissions.data"
                                :key="admission.id"
                                class="transition hover:bg-indigo-50/40"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 to-sky-100 text-sm font-black text-indigo-700">
                                            {{ admission.student_name?.charAt(0) || 'A' }}
                                        </div>

                                        <div>
                                            <p class="font-bold text-slate-950">{{ admission.student_name }}</p>
                                            <p class="text-xs text-slate-400">{{ admission.email || 'No email' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-700">
                                    {{ admission.application_no }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ admission.applied_class }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ admission.parent_name }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ admission.parent_phone }}
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                                        :class="
                                            admission.status === 'Approved'
                                                ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                                : admission.status === 'Rejected'
                                                    ? 'bg-rose-50 text-rose-700 ring-rose-100'
                                                    : admission.status === 'Under Review'
                                                        ? 'bg-amber-50 text-amber-700 ring-amber-100'
                                                        : admission.status === 'Waitlisted'
                                                            ? 'bg-violet-50 text-violet-700 ring-violet-100'
                                                            : 'bg-sky-50 text-sky-700 ring-sky-100'
                                        "
                                    >
                                        {{ admission.status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            type="button"
                                            class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200"
                                            @click="openViewModal(admission)"
                                        >
                                            View
                                        </button>

                                        <button
                                            type="button"
                                            class="rounded-xl bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100"
                                            @click="openEditModal(admission)"
                                        >
                                            Edit
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="admissions.data.length === 0">
                                <td colspan="7" class="px-6 py-10 text-center text-sm font-bold text-slate-400">
                                    No admission applications found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="admissions.links && admissions.links.length > 3"
                    class="flex flex-col gap-4 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <p class="text-sm font-semibold text-slate-500">
                        Showing
                        <span class="font-black text-slate-800">{{ admissions.from || 0 }}</span>
                        to
                        <span class="font-black text-slate-800">{{ admissions.to || 0 }}</span>
                        of
                        <span class="font-black text-slate-800">{{ admissions.total || 0 }}</span>
                        applications
                    </p>

                    <div class="flex flex-wrap gap-2">
                        <Link
                            v-for="link in admissions.links"
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
                            <h2 class="text-xl font-black text-slate-950">New Admission Application</h2>
                            <p class="mt-1 text-sm text-slate-500">Add applicant, parent details, class request and review status.</p>
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
                            <!-- <div>
                                <label class="text-sm font-semibold text-slate-600">Application No *</label>
                                <input
                                    v-model="form.application_no"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="frontendErrors.application_no || form.errors.application_no ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                    placeholder="APP-1001"
                                />
                                <p v-if="frontendErrors.application_no || form.errors.application_no" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ frontendErrors.application_no || form.errors.application_no }}
                                </p>
                            </div> -->

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Student Name *</label>
                                <input
                                    v-model="form.student_name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="frontendErrors.student_name || form.errors.student_name ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                    placeholder="Student full name"
                                />
                                <p v-if="frontendErrors.student_name || form.errors.student_name" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ frontendErrors.student_name || form.errors.student_name }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Student Email</label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="frontendErrors.email || form.errors.email ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                    placeholder="student@example.com"
                                />
                                <p v-if="frontendErrors.email || form.errors.email" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ frontendErrors.email || form.errors.email }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Student Phone</label>
                                <input
                                    v-model="form.phone"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="frontendErrors.phone || form.errors.phone ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                    placeholder="+971 50 000 0000"
                                />
                                <p v-if="frontendErrors.phone || form.errors.phone" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ frontendErrors.phone || form.errors.phone }}
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
                                    :class="frontendErrors.date_of_birth || form.errors.date_of_birth ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                />
                                <p v-if="frontendErrors.date_of_birth || form.errors.date_of_birth" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ frontendErrors.date_of_birth || form.errors.date_of_birth }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Applied Class *</label>
                                <select
                                    v-model="form.applied_class"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="frontendErrors.applied_class || form.errors.applied_class ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                >
                                    <option value="">Select class</option>
                                    <option>Grade 5</option>
                                    <option>Grade 6</option>
                                    <option>Grade 7</option>
                                    <option>Grade 8</option>
                                    <option>Grade 9</option>
                                    <option>Grade 10</option>
                                </select>
                                <p v-if="frontendErrors.applied_class || form.errors.applied_class" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ frontendErrors.applied_class || form.errors.applied_class }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Previous School</label>
                                <input
                                    v-model="form.previous_school"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                    placeholder="Previous school name"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Parent Name *</label>
                                <input
                                    v-model="form.parent_name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="frontendErrors.parent_name || form.errors.parent_name ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                    placeholder="Parent / guardian name"
                                />
                                <p v-if="frontendErrors.parent_name || form.errors.parent_name" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ frontendErrors.parent_name || form.errors.parent_name }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Parent Phone *</label>
                                <input
                                    v-model="form.parent_phone"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="frontendErrors.parent_phone || form.errors.parent_phone ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                    placeholder="+971 50 000 0000"
                                />
                                <p v-if="frontendErrors.parent_phone || form.errors.parent_phone" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ frontendErrors.parent_phone || form.errors.parent_phone }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Parent Email</label>
                                <input
                                    v-model="form.parent_email"
                                    type="email"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="frontendErrors.parent_email || form.errors.parent_email ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                    placeholder="parent@example.com"
                                />
                                <p v-if="frontendErrors.parent_email || form.errors.parent_email" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ frontendErrors.parent_email || form.errors.parent_email }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Application Date</label>
                                <input
                                    v-model="form.application_date"
                                    type="date"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                />
                            </div>

                            <!-- <div>
                                <label class="text-sm font-semibold text-slate-600">Status *</label>
                                <select
                                    v-model="form.status"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option>New</option>
                                    <option>Under Review</option>
                                    <option>Approved</option>
                                    <option>Rejected</option>
                                    <option>Waitlisted</option>
                                </select>
                            </div> -->

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Address</label>
                                <textarea
                                    v-model="form.address"
                                    rows="3"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="frontendErrors.address || form.errors.address ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                    placeholder="Address"
                                ></textarea>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Remarks</label>
                                <textarea
                                    v-model="form.remarks"
                                    rows="3"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="frontendErrors.remarks || form.errors.remarks ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                    placeholder="Review notes or remarks"
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
                            @click="submitAdmission"
                        >
                            {{ form.processing ? 'Saving...' : 'Save Application' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- View Modal -->
            <div
                v-if="showViewModal && selectedAdmission"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
            >
                <div class="w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">{{ selectedAdmission.student_name }}</h2>
                            <p class="mt-1 text-sm font-semibold text-slate-500">{{ selectedAdmission.application_no }}</p>
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
                                <p class="text-xs font-bold uppercase text-slate-400">Applied Class</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedAdmission.applied_class }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Status</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedAdmission.status }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Student Phone</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedAdmission.phone || '-' }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Student Email</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedAdmission.email || '-' }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Parent Name</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedAdmission.parent_name }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Parent Phone</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedAdmission.parent_phone }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100 sm:col-span-2">
                                <p class="text-xs font-bold uppercase text-slate-400">Remarks</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedAdmission.remarks || '-' }}</p>
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
                            @click="openEditModal(selectedAdmission); showViewModal = false"
                        >
                            Edit Application
                        </button>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div
                v-if="showEditModal && selectedAdmission"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
            >
                <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">Edit Admission Application</h2>
                            <p class="mt-1 text-sm text-slate-500">Update applicant details and admission review status.</p>
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
                            <!-- <div>
                                <label class="text-sm font-semibold text-slate-600">Application No *</label>
                                <input
                                    v-model="editForm.application_no"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="editFrontendErrors.application_no || editForm.errors.application_no ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                />
                            </div> -->

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Student Name *</label>
                                <input
                                    v-model="editForm.student_name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="editFrontendErrors.student_name || editForm.errors.student_name ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Student Email</label>
                                <input
                                    v-model="editForm.email"
                                    type="email"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="editFrontendErrors.email || editForm.errors.email ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Student Phone</label>
                                <input
                                    v-model="editForm.phone"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="editFrontendErrors.phone || editForm.errors.phone ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Applied Class *</label>
                                <select
                                    v-model="editForm.applied_class"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="editFrontendErrors.applied_class || editForm.errors.applied_class ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                >
                                    <option value="">Select class</option>
                                    <option>Grade 5</option>
                                    <option>Grade 6</option>
                                    <option>Grade 7</option>
                                    <option>Grade 8</option>
                                    <option>Grade 9</option>
                                    <option>Grade 10</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status *</label>
                                <select
                                    v-model="editForm.status"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option>New</option>
                                    <option>Under Review</option>
                                    <option>Approved</option>
                                    <option>Rejected</option>
                                    <option>Waitlisted</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Parent Name *</label>
                                <input
                                    v-model="editForm.parent_name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="editFrontendErrors.parent_name || editForm.errors.parent_name ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Parent Phone *</label>
                                <input
                                    v-model="editForm.parent_phone"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="editFrontendErrors.parent_phone || editForm.errors.parent_phone ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                />
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Remarks</label>
                                <textarea
                                    v-model="editForm.remarks"
                                    rows="3"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="editFrontendErrors.remarks || editForm.errors.remarks ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
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
                            @click="updateAdmission"
                        >
                            {{ editForm.processing ? 'Updating...' : 'Update Application' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>