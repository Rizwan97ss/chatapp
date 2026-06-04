<script setup>
import { ref, computed } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    students: {
        type: Object,
        default: () => ({
            data: [],
        }),
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            class_name: '',
            status: '',
        }),
    },
    
})

const showCreateModal = ref(false)

const stats = [
    { label: 'Total Students', value: '1,248', change: '+12%', color: 'from-indigo-500 to-sky-400' },
    { label: 'Active Students', value: '1,186', change: '+8%', color: 'from-emerald-500 to-teal-400' },
    { label: 'New Admissions', value: '64', change: '+18%', color: 'from-violet-500 to-fuchsia-400' },
    { label: 'Pending Documents', value: '23', change: '-4%', color: 'from-amber-500 to-orange-400' },
]

const form = useForm({
    admission_no: '',
    full_name: '',
    email: '',
    phone: '',
    gender: '',
    date_of_birth: '',
    class_name: '',
    section: '',
    parent_name: '',
    parent_phone: '',
    admission_date: '',
    status: 'Active',
    address: '',
})

const frontendErrors = computed(() => {
    const errors = {}

    if (!form.full_name.trim()) errors.full_name = 'Full name is required.'
    if (!form.admission_no.trim()) errors.admission_no = 'Admission number is required.'
    if (!form.class_name) errors.class_name = 'Class is required.'
    if (!form.parent_name.trim()) errors.parent_name = 'Parent name is required.'
    if (!form.parent_phone.trim()) errors.parent_phone = 'Parent phone is required.'

    if (form.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.email = 'Enter a valid email address.'
    }

    if (form.date_of_birth) {
        const dob = new Date(form.date_of_birth)
        const today = new Date()
        today.setHours(0, 0, 0, 0)

        if (dob >= today) {
            errors.date_of_birth = 'Date of birth must be before today.'
        }
    }

    if (form.phone && form.phone.length > 30) {
        errors.phone = 'Phone number is too long.'
    }

    if (form.parent_phone && form.parent_phone.length > 30) {
        errors.parent_phone = 'Parent phone number is too long.'
    }

    if (form.address && form.address.length > 1000) {
        errors.address = 'Address must not be greater than 1000 characters.'
    }

    return errors
})

const canSubmit = computed(() => Object.keys(frontendErrors.value).length === 0)

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
    form.status = 'Active'
    form.clearErrors()
}

const submitStudent = () => {
    if (!canSubmit.value || form.processing) return

    form.post(route('students.store'), {
        preserveScroll: true,
        onSuccess: () => {
            resetForm()
            showCreateModal.value = false
        },
    })
}

//import
const importFile = ref(null)
const importError = ref('')

const allowedImportTypes = [
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/vnd.ms-excel',
    'text/csv',
]

const importForm = useForm({
    file: null,
})

const handleImportFile = (event) => {
    const file = event.target.files[0]

    importError.value = ''
    importFile.value = null
    importForm.file = null

    if (!file) return

    if (!allowedImportTypes.includes(file.type) && !file.name.match(/\.(xlsx|xls|csv)$/i)) {
        importError.value = 'Only Excel or CSV files are allowed.'
        return
    }

    if (file.size > 5 * 1024 * 1024) {
        importError.value = 'File size must not be greater than 5MB.'
        return
    }

    importFile.value = file
    importForm.file = file
}

const submitImport = () => {
    if (!importForm.file || importForm.processing) {
        importError.value = 'Please select a valid Excel or CSV file.'
        return
    }

    importForm.post(route('students.import'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            importForm.reset()
            importFile.value = null
            importError.value = ''
        },
    })
}
//updates
const showViewModal = ref(false)
const showEditModal = ref(false)
const selectedStudent = ref(null)

const editForm = useForm({
    admission_no: '',
    full_name: '',
    email: '',
    phone: '',
    gender: '',
    date_of_birth: '',
    class_name: '',
    section: '',
    parent_name: '',
    parent_phone: '',
    admission_date: '',
    status: 'Active',
    address: '',
})

const editFrontendErrors = computed(() => {
    const errors = {}

    if (!editForm.full_name.trim()) errors.full_name = 'Full name is required.'
    if (!editForm.admission_no.trim()) errors.admission_no = 'Admission number is required.'
    if (!editForm.class_name) errors.class_name = 'Class is required.'
    if (!editForm.parent_name.trim()) errors.parent_name = 'Parent name is required.'
    if (!editForm.parent_phone.trim()) errors.parent_phone = 'Parent phone is required.'

    if (editForm.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(editForm.email)) {
        errors.email = 'Enter a valid email address.'
    }

    if (editForm.date_of_birth) {
        const dob = new Date(editForm.date_of_birth)
        const today = new Date()
        today.setHours(0, 0, 0, 0)

        if (dob >= today) {
            errors.date_of_birth = 'Date of birth must be before today.'
        }
    }

    if (editForm.phone && editForm.phone.length > 30) {
        errors.phone = 'Phone number is too long.'
    }

    if (editForm.parent_phone && editForm.parent_phone.length > 30) {
        errors.parent_phone = 'Parent phone number is too long.'
    }

    if (editForm.address && editForm.address.length > 1000) {
        errors.address = 'Address must not be greater than 1000 characters.'
    }

    return errors
})

const canUpdate = computed(() => Object.keys(editFrontendErrors.value).length === 0)

const openViewModal = (student) => {
    selectedStudent.value = student
    showViewModal.value = true
}

const closeViewModal = () => {
    selectedStudent.value = null
    showViewModal.value = false
}

const openEditModal = (student) => {
    selectedStudent.value = student

    editForm.admission_no = student.admission_no || ''
    editForm.full_name = student.full_name || ''
    editForm.email = student.email || ''
    editForm.phone = student.phone || ''
    editForm.gender = student.gender || ''
    editForm.date_of_birth = student.date_of_birth || ''
    editForm.class_name = student.class_name || ''
    editForm.section = student.section || ''
    editForm.parent_name = student.parent_name || ''
    editForm.parent_phone = student.parent_phone || ''
    editForm.admission_date = student.admission_date || ''
    editForm.status = student.status || 'Active'
    editForm.address = student.address || ''

    editForm.clearErrors()
    showEditModal.value = true
}

const closeEditModal = () => {
    selectedStudent.value = null
    editForm.clearErrors()
    showEditModal.value = false
}

const updateStudent = () => {
    if (!selectedStudent.value || !canUpdate.value || editForm.processing) return

    editForm.put(route('students.update', selectedStudent.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeEditModal()
        },
    })
}

const filterForm = useForm({
    search: props.filters.search || '',
    class_name: props.filters.class_name || '',
    status: props.filters.status || '',
})

const applyFilters = () => {
    router.get(route('students'), {
        search: filterForm.search,
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
    filterForm.class_name = ''
    filterForm.status = ''

    router.get(route('students'), {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}
</script>

<template>
    <AppLayout title="Students">
        <div class="space-y-6">
            <!-- Header -->
            <div
                class="rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 ring-1 ring-slate-200 sm:p-6 lg:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">Student Management</p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                            Students
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                            Manage student admissions, academic records, parent details, class assignments and document
                            status.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button
                            class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                            Export
                        </button>

                        <button type="button"
                            class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800"
                            @click="openCreateModal">
                            Add Student
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div v-for="stat in stats" :key="stat.label"
                    class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-500">{{ stat.label }}</p>
                            <h2 class="mt-3 text-3xl font-black text-slate-950">{{ stat.value }}</h2>
                        </div>

                        <div
                            :class="`flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br ${stat.color} text-white shadow-lg`">
                            <span class="text-lg font-black">+</span>
                        </div>
                    </div>

                    <p class="mt-4 text-xs font-semibold text-emerald-600">
                        {{ stat.change }} from last month
                    </p>
                </div>
            </div>



            <!-- Student Summary -->
            <div class="grid gap-6 xl:grid-cols-3">
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 xl:col-span-2">
                    <h2 class="text-lg font-black text-slate-950">Admission Overview</h2>
                    <p class="mt-1 text-sm text-slate-500">Frontend overview. Backend stats can connect later.</p>

                    <div class="mt-6 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl bg-indigo-50 p-5">
                            <p class="text-xs font-semibold text-indigo-600">This Month</p>
                            <p class="mt-2 text-2xl font-black text-slate-950">64</p>
                            <p class="mt-1 text-xs text-slate-500">New admissions</p>
                        </div>

                        <div class="rounded-2xl bg-emerald-50 p-5">
                            <p class="text-xs font-semibold text-emerald-600">Attendance</p>
                            <p class="mt-2 text-2xl font-black text-slate-950">94%</p>
                            <p class="mt-1 text-xs text-slate-500">Average today</p>
                        </div>

                        <div class="rounded-2xl bg-amber-50 p-5">
                            <p class="text-xs font-semibold text-amber-600">Documents</p>
                            <p class="mt-2 text-2xl font-black text-slate-950">23</p>
                            <p class="mt-1 text-xs text-slate-500">Pending review</p>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-3xl bg-gradient-to-br from-indigo-600 to-sky-500 p-6 text-white shadow-lg shadow-indigo-100">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex size-16 items-center justify-center rounded-3xl bg-white/20 text-2xl font-black ring-1 ring-white/30">
                            A
                        </div>

                        <div>
                            <h3 class="text-lg font-black">Student Profile</h3>
                            <p class="text-sm text-white/75">Quick profile preview</p>
                        </div>
                    </div>

                    <div class="mt-8 space-y-4">
                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Current Class</p>
                            <p class="mt-1 font-bold">Grade 8 - Section A</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Attendance</p>
                            <p class="mt-1 font-bold">94% this month</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Fee Status</p>
                            <p class="mt-1 font-bold">Paid / No dues</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Filters -->
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <form class="grid gap-4 md:grid-cols-2 xl:grid-cols-5" @submit.prevent="applyFilters">
                    <div class="xl:col-span-2">
                        <label class="text-sm font-semibold text-slate-600">Search Student</label>
                        <input v-model="filterForm.search" type="text" placeholder="Search by name, ID, email or phone"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Class</label>
                        <select v-model="filterForm.class_name"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option value="">All Classes</option>
                            <option>Grade 6</option>
                            <option>Grade 8</option>
                            <option>Grade 10</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Status</label>
                        <select v-model="filterForm.status"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option value="">All Status</option>
                            <option>Active</option>
                            <option>Pending</option>
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
            <!-- Students Table -->
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
                <div
                    class="flex flex-col gap-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white p-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">Student Records</h2>
                        <p class="mt-1 text-sm text-slate-500">Student list from database</p>
                    </div>

                    <div class="flex flex-col gap-2 sm:items-end">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <label
                                class="inline-flex cursor-pointer items-center justify-center rounded-xl bg-indigo-50 px-4 py-2 text-sm font-bold text-indigo-700 transition hover:bg-indigo-100">
                                Import Excel
                                <input type="file" class="hidden" accept=".xlsx,.xls,.csv" @change="handleImportFile" />
                            </label>

                            <button v-if="importFile" type="button"
                                class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                                :disabled="importForm.processing" @click="submitImport">
                                {{ importForm.processing ? 'Importing...' : 'Upload Excel' }}
                            </button>

                            <button type="button"
                                class="inline-flex items-center justify-center rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800"
                                @click="openCreateModal">
                                New Student
                            </button>
                        </div>

                        <p v-if="importFile" class="max-w-xs truncate text-xs font-semibold text-slate-500">
                            Selected: {{ importFile.name }}
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[980px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Student</th>
                                <th class="px-6 py-4">Admission No</th>
                                <th class="px-6 py-4">Class</th>
                                <th class="px-6 py-4">Parent</th>
                                <th class="px-6 py-4">Phone</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="student in students.data" :key="student.id"
                                class="transition hover:bg-indigo-50/40">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex size-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 to-sky-100 text-sm font-black text-indigo-700">
                                            {{ student.full_name?.charAt(0) || 'S' }}
                                        </div>

                                        <div>
                                            <p class="font-bold text-slate-950">{{ student.full_name }}</p>
                                            <p class="text-xs text-slate-400">{{ student.email || 'No email' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-700">
                                    {{ student.admission_no }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ student.class_name }} - {{ student.section || '-' }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ student.parent_name }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ student.phone || '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold ring-1" :class="student.status === 'Active'
                                        ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                        : student.status === 'Pending'
                                            ? 'bg-amber-50 text-amber-700 ring-amber-100'
                                            : 'bg-rose-50 text-rose-700 ring-rose-100'
                                        ">
                                        {{ student.status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button type="button"
                                            class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200"
                                            @click="openViewModal(student)">
                                            View
                                        </button>

                                        <button type="button"
                                            class="rounded-xl bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100"
                                            @click="openEditModal(student)">
                                            Edit
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="students.data.length === 0">
                                <td colspan="7" class="px-6 py-10 text-center text-sm font-bold text-slate-400">
                                    No students found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="students.links && students.links.length > 3"
                        class="flex flex-col gap-4 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm font-semibold text-slate-500">
                            Showing
                            <span class="font-black text-slate-800">{{ students.from || 0 }}</span>
                            to
                            <span class="font-black text-slate-800">{{ students.to || 0 }}</span>
                            of
                            <span class="font-black text-slate-800">{{ students.total || 0 }}</span>
                            students
                        </p>

                        <div class="flex flex-wrap gap-2">
                            <Link v-for="link in students.links" :key="link.label" :href="link.url || '#'"
                                preserve-scroll class="rounded-xl px-4 py-2 text-sm font-bold transition" :class="[
                                    link.active
                                        ? 'bg-slate-950 text-white'
                                        : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-100',
                                    !link.url ? 'pointer-events-none cursor-not-allowed opacity-40' : ''
                                ]" v-html="link.label" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Student Dialog -->
            <div v-if="showCreateModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
                <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div
                        class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">
                                Add New Student
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Add student profile, parent details, class assignment and admission information.
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
                                    :class="frontendErrors.full_name || form.errors.full_name ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                    placeholder="Student full name" />
                                <p v-if="frontendErrors.full_name || form.errors.full_name"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ frontendErrors.full_name || form.errors.full_name }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Admission No *</label>
                                <input v-model="form.admission_no"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="frontendErrors.admission_no || form.errors.admission_no ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                    placeholder="STU-1004" />
                                <p v-if="frontendErrors.admission_no || form.errors.admission_no"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ frontendErrors.admission_no || form.errors.admission_no }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Email</label>
                                <input v-model="form.email" type="email"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="frontendErrors.email || form.errors.email ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                    placeholder="student@example.com" />
                                <p v-if="frontendErrors.email || form.errors.email"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ frontendErrors.email || form.errors.email }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Phone</label>
                                <input v-model="form.phone"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="frontendErrors.phone || form.errors.phone ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                    placeholder="+971 50 000 0000" />
                                <p v-if="frontendErrors.phone || form.errors.phone"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ frontendErrors.phone || form.errors.phone }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Gender</label>
                                <select v-model="form.gender"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option value="">Select gender</option>
                                    <option>Male</option>
                                    <option>Female</option>
                                </select>
                                <p v-if="form.errors.gender" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ form.errors.gender }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Date of Birth</label>
                                <input v-model="form.date_of_birth" type="date"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="frontendErrors.date_of_birth || form.errors.date_of_birth ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'" />
                                <p v-if="frontendErrors.date_of_birth || form.errors.date_of_birth"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ frontendErrors.date_of_birth || form.errors.date_of_birth }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Class *</label>
                                <select v-model="form.class_name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="frontendErrors.class_name || form.errors.class_name ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'">
                                    <option value="">Select class</option>
                                    <option>Grade 6</option>
                                    <option>Grade 8</option>
                                    <option>Grade 10</option>
                                </select>
                                <p v-if="frontendErrors.class_name || form.errors.class_name"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ frontendErrors.class_name || form.errors.class_name }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Section</label>
                                <select v-model="form.section"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option value="">Select section</option>
                                    <option>A</option>
                                    <option>B</option>
                                    <option>C</option>
                                </select>
                                <p v-if="form.errors.section" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ form.errors.section }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Parent / Guardian Name *</label>
                                <input v-model="form.parent_name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="frontendErrors.parent_name || form.errors.parent_name ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                    placeholder="Parent name" />
                                <p v-if="frontendErrors.parent_name || form.errors.parent_name"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ frontendErrors.parent_name || form.errors.parent_name }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Parent Phone *</label>
                                <input v-model="form.parent_phone"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="frontendErrors.parent_phone || form.errors.parent_phone ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                    placeholder="+971 50 000 0000" />
                                <p v-if="frontendErrors.parent_phone || form.errors.parent_phone"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ frontendErrors.parent_phone || form.errors.parent_phone }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Admission Date</label>
                                <input v-model="form.admission_date" type="date"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                                <p v-if="form.errors.admission_date" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ form.errors.admission_date }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status *</label>
                                <select v-model="form.status"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>Active</option>
                                    <option>Pending</option>
                                    <option>Inactive</option>
                                </select>
                                <p v-if="form.errors.status" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ form.errors.status }}
                                </p>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Address</label>
                                <textarea v-model="form.address" rows="3"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="frontendErrors.address || form.errors.address ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                    placeholder="Student address"></textarea>
                                <p v-if="frontendErrors.address || form.errors.address"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ frontendErrors.address || form.errors.address }}
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
                            :disabled="form.processing || !canSubmit" @click="submitStudent">
                            {{ form.processing ? 'Saving...' : 'Save Student' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- //pop up for error of import -->
        <div v-if="importError || importForm.errors.file || importForm.errors.import"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl ring-1 ring-slate-200">
                <div class="flex items-start gap-4">
                    <div
                        class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-rose-50 text-xl font-black text-rose-600">
                        !
                    </div>

                    <div class="min-w-0 flex-1">
                        <h3 class="text-lg font-black text-slate-950">
                            Import Error
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            {{ importError || importForm.errors.file || importForm.errors.import }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button"
                        class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800"
                        @click="importError = ''; importForm.clearErrors()">
                        Okay
                    </button>
                </div>
            </div>
        </div>
        <!-- // View Student Dialog -->
        <div v-if="showViewModal && selectedStudent"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
            <div class="w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                <div
                    class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex size-14 items-center justify-center rounded-3xl bg-gradient-to-br from-indigo-500 to-sky-400 text-xl font-black text-white shadow-lg">
                            {{ selectedStudent.full_name?.charAt(0) || 'S' }}
                        </div>

                        <div>
                            <h2 class="text-xl font-black text-slate-950">
                                {{ selectedStudent.full_name }}
                            </h2>
                            <p class="mt-1 text-sm font-semibold text-slate-500">
                                {{ selectedStudent.admission_no }}
                            </p>
                        </div>
                    </div>

                    <button type="button"
                        class="rounded-2xl bg-white p-2 text-slate-400 shadow-sm ring-1 ring-slate-200 transition hover:text-slate-700"
                        @click="closeViewModal">
                        ✕
                    </button>
                </div>

                <div class="max-h-[75vh] overflow-y-auto p-5 sm:p-6">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                            <p class="text-xs font-bold uppercase text-slate-400">Email</p>
                            <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedStudent.email || '-' }}</p>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                            <p class="text-xs font-bold uppercase text-slate-400">Phone</p>
                            <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedStudent.phone || '-' }}</p>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                            <p class="text-xs font-bold uppercase text-slate-400">Gender</p>
                            <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedStudent.gender || '-' }}</p>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                            <p class="text-xs font-bold uppercase text-slate-400">Date of Birth</p>
                            <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedStudent.date_of_birth || '-' }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-indigo-50 p-4 ring-1 ring-indigo-100">
                            <p class="text-xs font-bold uppercase text-indigo-500">Class</p>
                            <p class="mt-1 text-sm font-bold text-slate-800">
                                {{ selectedStudent.class_name }} - {{ selectedStudent.section || '-' }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                            <p class="text-xs font-bold uppercase text-slate-400">Status</p>
                            <p class="mt-1">
                                <span class="rounded-full px-3 py-1 text-xs font-bold ring-1" :class="selectedStudent.status === 'Active'
                                    ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                    : selectedStudent.status === 'Pending'
                                        ? 'bg-amber-50 text-amber-700 ring-amber-100'
                                        : 'bg-rose-50 text-rose-700 ring-rose-100'
                                    ">
                                    {{ selectedStudent.status }}
                                </span>
                            </p>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                            <p class="text-xs font-bold uppercase text-slate-400">Parent / Guardian</p>
                            <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedStudent.parent_name }}</p>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                            <p class="text-xs font-bold uppercase text-slate-400">Parent Phone</p>
                            <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedStudent.parent_phone }}</p>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                            <p class="text-xs font-bold uppercase text-slate-400">Admission Date</p>
                            <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedStudent.admission_date || '-' }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                            <p class="text-xs font-bold uppercase text-slate-400">Admission No</p>
                            <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedStudent.admission_no }}</p>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100 sm:col-span-2">
                            <p class="text-xs font-bold uppercase text-slate-400">Address</p>
                            <p class="mt-1 text-sm font-bold leading-6 text-slate-800">
                                {{ selectedStudent.address || '-' }}
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
                        @click="openEditModal(selectedStudent); showViewModal = false">
                        Edit Student
                    </button>
                </div>
            </div>
        </div>
        <!-- edit -->
        <div v-if="showEditModal && selectedStudent"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
            <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                <div
                    class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                    <div>
                        <h2 class="text-xl font-black text-slate-950">
                            Edit Student
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Update student profile, parent details, class assignment and admission information.
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
                                :class="editFrontendErrors.full_name || editForm.errors.full_name ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                placeholder="Student full name" />
                            <p v-if="editFrontendErrors.full_name || editForm.errors.full_name"
                                class="mt-1 text-xs font-bold text-rose-600">
                                {{ editFrontendErrors.full_name || editForm.errors.full_name }}
                            </p>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-600">Admission No *</label>
                            <input v-model="editForm.admission_no"
                                class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                :class="editFrontendErrors.admission_no || editForm.errors.admission_no ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                placeholder="STU-1004" />
                            <p v-if="editFrontendErrors.admission_no || editForm.errors.admission_no"
                                class="mt-1 text-xs font-bold text-rose-600">
                                {{ editFrontendErrors.admission_no || editForm.errors.admission_no }}
                            </p>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-600">Email</label>
                            <input v-model="editForm.email" type="email"
                                class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                :class="editFrontendErrors.email || editForm.errors.email ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                placeholder="student@example.com" />
                            <p v-if="editFrontendErrors.email || editForm.errors.email"
                                class="mt-1 text-xs font-bold text-rose-600">
                                {{ editFrontendErrors.email || editForm.errors.email }}
                            </p>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-600">Phone</label>
                            <input v-model="editForm.phone"
                                class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                :class="editFrontendErrors.phone || editForm.errors.phone ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                placeholder="+971 50 000 0000" />
                            <p v-if="editFrontendErrors.phone || editForm.errors.phone"
                                class="mt-1 text-xs font-bold text-rose-600">
                                {{ editFrontendErrors.phone || editForm.errors.phone }}
                            </p>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-600">Gender</label>
                            <select v-model="editForm.gender"
                                class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                <option value="">Select gender</option>
                                <option>Male</option>
                                <option>Female</option>
                            </select>
                            <p v-if="editForm.errors.gender" class="mt-1 text-xs font-bold text-rose-600">
                                {{ editForm.errors.gender }}
                            </p>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-600">Date of Birth</label>
                            <input v-model="editForm.date_of_birth" type="date"
                                class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                :class="editFrontendErrors.date_of_birth || editForm.errors.date_of_birth ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'" />
                            <p v-if="editFrontendErrors.date_of_birth || editForm.errors.date_of_birth"
                                class="mt-1 text-xs font-bold text-rose-600">
                                {{ editFrontendErrors.date_of_birth || editForm.errors.date_of_birth }}
                            </p>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-600">Class *</label>
                            <select v-model="editForm.class_name"
                                class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                :class="editFrontendErrors.class_name || editForm.errors.class_name ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'">
                                <option value="">Select class</option>
                                <option>Grade 6</option>
                                <option>Grade 8</option>
                                <option>Grade 10</option>
                            </select>
                            <p v-if="editFrontendErrors.class_name || editForm.errors.class_name"
                                class="mt-1 text-xs font-bold text-rose-600">
                                {{ editFrontendErrors.class_name || editForm.errors.class_name }}
                            </p>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-600">Section</label>
                            <select v-model="editForm.section"
                                class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                <option value="">Select section</option>
                                <option>A</option>
                                <option>B</option>
                                <option>C</option>
                            </select>
                            <p v-if="editForm.errors.section" class="mt-1 text-xs font-bold text-rose-600">
                                {{ editForm.errors.section }}
                            </p>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-600">Parent / Guardian Name *</label>
                            <input v-model="editForm.parent_name"
                                class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                :class="editFrontendErrors.parent_name || editForm.errors.parent_name ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                placeholder="Parent name" />
                            <p v-if="editFrontendErrors.parent_name || editForm.errors.parent_name"
                                class="mt-1 text-xs font-bold text-rose-600">
                                {{ editFrontendErrors.parent_name || editForm.errors.parent_name }}
                            </p>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-600">Parent Phone *</label>
                            <input v-model="editForm.parent_phone"
                                class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                :class="editFrontendErrors.parent_phone || editForm.errors.parent_phone ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                placeholder="+971 50 000 0000" />
                            <p v-if="editFrontendErrors.parent_phone || editForm.errors.parent_phone"
                                class="mt-1 text-xs font-bold text-rose-600">
                                {{ editFrontendErrors.parent_phone || editForm.errors.parent_phone }}
                            </p>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-600">Admission Date</label>
                            <input v-model="editForm.admission_date" type="date"
                                class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                            <p v-if="editForm.errors.admission_date" class="mt-1 text-xs font-bold text-rose-600">
                                {{ editForm.errors.admission_date }}
                            </p>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-600">Status *</label>
                            <select v-model="editForm.status"
                                class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                <option>Active</option>
                                <option>Pending</option>
                                <option>Inactive</option>
                            </select>
                            <p v-if="editForm.errors.status" class="mt-1 text-xs font-bold text-rose-600">
                                {{ editForm.errors.status }}
                            </p>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="text-sm font-semibold text-slate-600">Address</label>
                            <textarea v-model="editForm.address" rows="3"
                                class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                :class="editFrontendErrors.address || editForm.errors.address ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-400 focus:ring-indigo-100'"
                                placeholder="Student address"></textarea>
                            <p v-if="editFrontendErrors.address || editForm.errors.address"
                                class="mt-1 text-xs font-bold text-rose-600">
                                {{ editFrontendErrors.address || editForm.errors.address }}
                            </p>
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
                        :disabled="editForm.processing || !canUpdate" @click="updateStudent">
                        {{ editForm.processing ? 'Updating...' : 'Update Student' }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>