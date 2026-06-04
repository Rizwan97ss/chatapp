<script setup>
import { ref, computed } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    feeRecords: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            class_name: '',
            fee_type: '',
            status: '',
        }),
    },
    feeStats: {
        type: Object,
        default: () => ({
            cards: [],
            overview: {},
            collectionBars: [],
        }),
    },
    classNames: {
        type: Array,
        default: () => [],
    },
    feeTypes: {
        type: Array,
        default: () => [],
    },
})

const invoiceList = computed(() => props.feeRecords || { data: [], links: [] })
const statCards = computed(() => props.feeStats?.cards || [])
const overview = computed(() => props.feeStats?.overview || {})
const collectionBars = computed(() => props.feeStats?.collectionBars || [])

const defaultClasses = ['Grade 6', 'Grade 8', 'Grade 10']
const defaultFeeTypes = ['Tuition Fee', 'Transport Fee', 'Exam Fee', 'Library Fee', 'Sports Fee']

const classOptions = computed(() => [...new Set([...(props.classNames || []), ...defaultClasses])])
const feeTypeOptions = computed(() => [...new Set([...(props.feeTypes || []), ...defaultFeeTypes])])

const showCreateModal = ref(false)
const showViewModal = ref(false)
const showEditModal = ref(false)
const selectedInvoice = ref(null)

const filterForm = useForm({
    search: props.filters?.search || '',
    class_name: props.filters?.class_name || '',
    fee_type: props.filters?.fee_type || '',
    status: props.filters?.status || '',
})

const form = useForm({
    invoice_no: '',
    student_name: '',
    admission_no: '',
    class_name: '',
    fee_type: '',
    amount: '',
    discount: 0,
    paid_amount: 0,
    due_date: '',
    payment_method: '',
    status: 'Pending',
    notes: '',
})

const editForm = useForm({
    invoice_no: '',
    student_name: '',
    admission_no: '',
    class_name: '',
    fee_type: '',
    amount: '',
    discount: 0,
    paid_amount: 0,
    due_date: '',
    payment_method: '',
    status: 'Pending',
    notes: '',
})

const money = (amount) => `AED ${Number(amount || 0).toLocaleString(undefined, {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
})}`

const validateInvoice = (targetForm) => {
    const errors = {}

    if (!targetForm.invoice_no.trim()) errors.invoice_no = 'Invoice number is required.'
    if (!targetForm.student_name.trim()) errors.student_name = 'Student name is required.'
    if (!targetForm.class_name) errors.class_name = 'Class is required.'
    if (!targetForm.fee_type) errors.fee_type = 'Fee type is required.'

    if (targetForm.amount === '' || Number(targetForm.amount) < 0) {
        errors.amount = 'Valid amount is required.'
    }

    if (targetForm.discount !== '' && Number(targetForm.discount) < 0) {
        errors.discount = 'Discount cannot be negative.'
    }

    if (targetForm.paid_amount !== '' && Number(targetForm.paid_amount) < 0) {
        errors.paid_amount = 'Paid amount cannot be negative.'
    }

    if (targetForm.notes && targetForm.notes.length > 1000) {
        errors.notes = 'Notes must not be greater than 1000 characters.'
    }

    return errors
}

const frontendErrors = computed(() => validateInvoice(form))
const editFrontendErrors = computed(() => validateInvoice(editForm))

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
    form.discount = 0
    form.paid_amount = 0
    form.status = 'Pending'
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

const submitInvoice = () => {
    if (!canSubmit.value || form.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    form.post(route('fees.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Invoice created successfully.')
            resetForm()
            showCreateModal.value = false
        },
        onError: () => toast.error('Please check the form errors.'),
    })
}

const openViewModal = (invoice) => {
    selectedInvoice.value = invoice
    showViewModal.value = true
}

const closeViewModal = () => {
    selectedInvoice.value = null
    showViewModal.value = false
}

const openEditModal = (invoice) => {
    selectedInvoice.value = invoice

    editForm.invoice_no = invoice.invoice_no || ''
    editForm.student_name = invoice.student_name || ''
    editForm.admission_no = invoice.admission_no || ''
    editForm.class_name = invoice.class_name || ''
    editForm.fee_type = invoice.fee_type || ''
    editForm.amount = invoice.amount ?? ''
    editForm.discount = invoice.discount ?? 0
    editForm.paid_amount = invoice.paid_amount ?? 0
    editForm.due_date = invoice.due_date || ''
    editForm.payment_method = invoice.payment_method || ''
    editForm.status = invoice.status || 'Pending'
    editForm.notes = invoice.notes || ''

    editForm.clearErrors()
    showEditModal.value = true
}

const closeEditModal = () => {
    selectedInvoice.value = null
    editForm.clearErrors()
    showEditModal.value = false
}

const updateInvoice = () => {
    if (!selectedInvoice.value || !canUpdate.value || editForm.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    editForm.put(route('fees.update', selectedInvoice.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Invoice updated successfully.')
            closeEditModal()
        },
        onError: () => toast.error('Please check the form errors.'),
    })
}

const deleteInvoice = (invoice) => {
    if (!confirm(`Delete invoice ${invoice.invoice_no}?`)) return

    router.delete(route('fees.destroy', invoice.id), {
        preserveScroll: true,
        onSuccess: () => toast.success('Invoice deleted successfully.'),
        onError: () => toast.error('Unable to delete invoice.'),
    })
}

const applyFilters = () => {
    router.get(route('fees'), {
        search: filterForm.search,
        class_name: filterForm.class_name,
        fee_type: filterForm.fee_type,
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
    filterForm.fee_type = ''
    filterForm.status = ''

    router.get(route('fees'), {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}
const downloadInvoice = (invoice) => {
    const invoiceWindow = window.open('', '_blank')

    if (!invoiceWindow) {
        toast.error('Please allow popups to download invoice.')
        return
    }

    const balance = Number(invoice.balance || 0)
    const netAmount = Number(invoice.net_amount || 0)

    invoiceWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Invoice ${invoice.invoice_no}</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    color: #0f172a;
                    padding: 40px;
                }
                .header {
                    display: flex;
                    justify-content: space-between;
                    border-bottom: 2px solid #e2e8f0;
                    padding-bottom: 20px;
                    margin-bottom: 30px;
                }
                .title {
                    font-size: 28px;
                    font-weight: 800;
                }
                .muted {
                    color: #64748b;
                    font-size: 13px;
                }
                .section {
                    margin-top: 25px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 15px;
                }
                th, td {
                    border: 1px solid #e2e8f0;
                    padding: 12px;
                    text-align: left;
                    font-size: 14px;
                }
                th {
                    background: #f8fafc;
                }
                .summary {
                    width: 350px;
                    margin-left: auto;
                    margin-top: 25px;
                }
                .summary-row {
                    display: flex;
                    justify-content: space-between;
                    padding: 10px 0;
                    border-bottom: 1px solid #e2e8f0;
                }
                .total {
                    font-size: 18px;
                    font-weight: 800;
                }
                .badge {
                    display: inline-block;
                    padding: 6px 12px;
                    border-radius: 999px;
                    background: #eef2ff;
                    color: #3730a3;
                    font-weight: 700;
                    font-size: 12px;
                }
                .footer {
                    margin-top: 50px;
                    font-size: 12px;
                    color: #64748b;
                    border-top: 1px solid #e2e8f0;
                    padding-top: 20px;
                }
                @media print {
                    button {
                        display: none;
                    }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <div>
                    <div class="title">Fee Invoice</div>
                    <p class="muted">School Management System</p>
                </div>
                <div>
                    <p><strong>Invoice No:</strong> ${invoice.invoice_no}</p>
                    <p><strong>Date:</strong> ${new Date().toLocaleDateString()}</p>
                    <p><strong>Status:</strong> <span class="badge">${invoice.status}</span></p>
                </div>
            </div>

            <div class="section">
                <h3>Student Details</h3>
                <table>
                    <tr>
                        <th>Student Name</th>
                        <td>${invoice.student_name || '-'}</td>
                    </tr>
                    <tr>
                        <th>Admission No</th>
                        <td>${invoice.admission_no || '-'}</td>
                    </tr>
                    <tr>
                        <th>Class</th>
                        <td>${invoice.class_name || '-'}</td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <h3>Fee Details</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Fee Type</th>
                            <th>Due Date</th>
                            <th>Payment Method</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>${invoice.fee_type || '-'}</td>
                            <td>${invoice.due_date || '-'}</td>
                            <td>${invoice.payment_method || '-'}</td>
                            <td>${invoice.notes || '-'}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="summary">
                <div class="summary-row">
                    <span>Amount</span>
                    <strong>${money(invoice.amount)}</strong>
                </div>
                <div class="summary-row">
                    <span>Discount</span>
                    <strong>${money(invoice.discount)}</strong>
                </div>
                <div class="summary-row">
                    <span>Net Amount</span>
                    <strong>${money(netAmount)}</strong>
                </div>
                <div class="summary-row">
                    <span>Paid Amount</span>
                    <strong>${money(invoice.paid_amount)}</strong>
                </div>
                <div class="summary-row total">
                    <span>Balance</span>
                    <strong>${money(balance)}</strong>
                </div>
            </div>

            <div class="footer">
                <p>This is a computer-generated invoice.</p>
                <p>Please keep this invoice for your records.</p>
            </div>

            <script>
                window.onload = function () {
                    window.print()
                }
            <\/script>
        </body>
        </html>
    `)

    invoiceWindow.document.close()
}
</script>

<template>
    <AppLayout title="Fees">
        <div class="space-y-6">
            <!-- Header -->
            <div class="rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 ring-1 ring-slate-200 sm:p-6 lg:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">Finance Management</p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                            Fees
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                            Manage fee invoices, collections, pending balances, discounts and student payment status.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button
                            type="button"
                            class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50"
                        >
                            Export Report
                        </button>

                        <button
                            type="button"
                            class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800"
                            @click="openCreateModal"
                        >
                            Create Invoice
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

          

            <!-- Finance Overview -->
            <div class="grid gap-6 xl:grid-cols-3">
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 xl:col-span-2">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Fee Collection Overview</h2>
                            <p class="mt-1 text-sm text-slate-500">Collection percentage by fee category.</p>
                        </div>

                        <span class="w-fit rounded-full bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                            {{ overview.collection_percentage || 0 }}% Collected
                        </span>
                    </div>

                    <div class="mt-8 space-y-5">
                        <div v-for="item in collectionBars" :key="item.label">
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

                        <p v-if="collectionBars.length === 0" class="rounded-2xl bg-slate-50 p-5 text-sm font-bold text-slate-400">
                            No collection data available.
                        </p>
                    </div>
                </div>

                <div class="rounded-3xl bg-gradient-to-br from-indigo-600 to-sky-500 p-6 text-white shadow-lg shadow-indigo-100">
                    <h3 class="text-lg font-black">Payment Summary</h3>

                    <p class="mt-1 text-sm text-white/75">
                        Current finance snapshot
                    </p>

                    <div class="mt-6 space-y-4">
                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Collected</p>
                            <p class="mt-1 text-xl font-black">{{ money(overview.collected) }}</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Pending</p>
                            <p class="mt-1 text-xl font-black">{{ money(overview.pending) }}</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Discounts</p>
                            <p class="mt-1 text-xl font-black">{{ money(overview.discounts) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fee Type Cards
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <div
                    v-for="item in collectionBars.slice(0, 4)"
                    :key="item.label"
                    class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                >
                    <p class="text-sm font-bold text-slate-900">{{ item.label }}</p>
                    <p class="mt-1 text-xs text-slate-500">Fee collection rate</p>
                    <p class="mt-5 text-2xl font-black text-slate-950">{{ item.value }}%</p>
                    <p class="mt-2 text-xs font-bold text-emerald-600">collected</p>
                </div>

                <div
                    v-if="collectionBars.length === 0"
                    class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 md:col-span-2 xl:col-span-4"
                >
                    <p class="text-sm font-bold text-slate-900">No fee category data</p>
                    <p class="mt-1 text-xs text-slate-500">Create invoices to see collection cards.</p>
                </div>
            </div> -->
  <!-- Filters -->
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <form class="grid gap-4 md:grid-cols-2 xl:grid-cols-6" @submit.prevent="applyFilters">
                    <div class="xl:col-span-2">
                        <label class="text-sm font-semibold text-slate-600">Search Invoice</label>
                        <input
                            v-model="filterForm.search"
                            type="text"
                            placeholder="Search by student, invoice no or admission no"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        />
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
                        <label class="text-sm font-semibold text-slate-600">Fee Type</label>
                        <select
                            v-model="filterForm.fee_type"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        >
                            <option value="">All Types</option>
                            <option v-for="feeType in feeTypeOptions" :key="feeType" :value="feeType">
                                {{ feeType }}
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
                            <option>Paid</option>
                            <option>Partial</option>
                            <option>Pending</option>
                            <option>Overdue</option>
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
            <!-- Fee Records Table -->
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">Fee Records</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Student invoice and payment records.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800"
                        @click="openCreateModal"
                    >
                        Create Invoice
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1120px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Student</th>
                                <th class="px-6 py-4">Invoice No</th>
                                <th class="px-6 py-4">Class</th>
                                <th class="px-6 py-4">Fee Type</th>
                                <th class="px-6 py-4">Amount</th>
                                <th class="px-6 py-4">Paid</th>
                                <th class="px-6 py-4">Balance</th>
                                <th class="px-6 py-4">Due Date</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="record in invoiceList.data"
                                :key="record.id"
                                class="transition hover:bg-indigo-50/40"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 to-sky-100 text-sm font-black text-indigo-700">
                                            {{ record.student_name?.charAt(0) || 'S' }}
                                        </div>

                                        <div>
                                            <p class="font-bold text-slate-950">
                                                {{ record.student_name }}
                                            </p>
                                            <p class="text-xs text-slate-400">
                                                {{ record.admission_no || '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-700">
                                    {{ record.invoice_no }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ record.class_name }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ record.fee_type }}
                                </td>

                                <td class="px-6 py-4 font-bold text-slate-900">
                                    {{ money(record.amount) }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ money(record.paid_amount) }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ money(record.balance) }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ record.due_date || '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                                        :class="
                                            record.status === 'Paid'
                                                ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                                : record.status === 'Partial'
                                                    ? 'bg-amber-50 text-amber-700 ring-amber-100'
                                                    : record.status === 'Pending'
                                                        ? 'bg-sky-50 text-sky-700 ring-sky-100'
                                                        : 'bg-rose-50 text-rose-700 ring-rose-100'
                                        "
                                    >
                                        {{ record.status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            type="button"
                                            class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200"
                                            @click="openViewModal(record)"
                                        >
                                            View
                                        </button>

                                        <button
                                            type="button"
                                            class="rounded-xl bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100"
                                            @click="openEditModal(record)"
                                        >
                                            Edit
                                        </button>
                                        <button
    type="button"
    class="rounded-xl bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-100"
    @click="downloadInvoice(record)"
>
    Download
</button>
                                        <button
                                            type="button"
                                            class="rounded-xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100"
                                            @click="deleteInvoice(record)"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="invoiceList.data.length === 0">
                                <td colspan="10" class="px-6 py-10 text-center text-sm font-bold text-slate-400">
                                    No fee invoices found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="invoiceList.links && invoiceList.links.length > 3"
                    class="flex flex-col gap-4 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <p class="text-sm font-semibold text-slate-500">
                        Showing
                        <span class="font-black text-slate-800">{{ invoiceList.from || 0 }}</span>
                        to
                        <span class="font-black text-slate-800">{{ invoiceList.to || 0 }}</span>
                        of
                        <span class="font-black text-slate-800">{{ invoiceList.total || 0 }}</span>
                        invoices
                    </p>

                    <div class="flex flex-wrap gap-2">
                        <Link
                            v-for="link in invoiceList.links"
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

            <!-- Create Invoice Modal -->
            <div
                v-if="showCreateModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
            >
                <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">
                                Create Fee Invoice
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Generate student fee invoice with fee type, amount and due date.
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
                                <label class="text-sm font-semibold text-slate-600">Student Name *</label>
                                <input
                                    v-model="form.student_name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'student_name')"
                                    placeholder="Ali Khan"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'student_name')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'student_name') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Invoice No *</label>
                                <input
                                    v-model="form.invoice_no"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'invoice_no')"
                                    placeholder="INV-1004"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'invoice_no')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'invoice_no') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Admission No</label>
                                <input
                                    v-model="form.admission_no"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                    placeholder="STU-1001"
                                />
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
                                <label class="text-sm font-semibold text-slate-600">Fee Type *</label>
                                <select
                                    v-model="form.fee_type"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'fee_type')"
                                >
                                    <option value="">Select fee type</option>
                                    <option v-for="feeType in feeTypeOptions" :key="feeType" :value="feeType">
                                        {{ feeType }}
                                    </option>
                                </select>
                                <p v-if="fieldError(frontendErrors, form, 'fee_type')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'fee_type') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Amount *</label>
                                <input
                                    v-model="form.amount"
                                    type="number"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'amount')"
                                    placeholder="4500"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'amount')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'amount') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Discount</label>
                                <input
                                    v-model="form.discount"
                                    type="number"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'discount')"
                                    placeholder="0"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'discount')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'discount') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Paid Amount</label>
                                <input
                                    v-model="form.paid_amount"
                                    type="number"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'paid_amount')"
                                    placeholder="0"
                                />
                                <p v-if="fieldError(frontendErrors, form, 'paid_amount')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'paid_amount') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Due Date</label>
                                <input
                                    v-model="form.due_date"
                                    type="date"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Payment Method</label>
                                <select
                                    v-model="form.payment_method"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option value="">Select method</option>
                                    <option>Cash</option>
                                    <option>Card</option>
                                    <option>Bank Transfer</option>
                                    <option>Online Payment</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status</label>
                                <select
                                    v-model="form.status"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option>Pending</option>
                                    <option>Paid</option>
                                    <option>Partial</option>
                                    <option>Overdue</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Notes</label>
                                <textarea
                                    v-model="form.notes"
                                    rows="3"
                                    placeholder="Optional invoice notes"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'notes')"
                                ></textarea>
                                <p v-if="fieldError(frontendErrors, form, 'notes')" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'notes') }}
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
                            @click="submitInvoice"
                        >
                            {{ form.processing ? 'Saving...' : 'Save Invoice' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- View Modal -->
            <div
                v-if="showViewModal && selectedInvoice"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
            >
                <div class="w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">{{ selectedInvoice.student_name }}</h2>
                            <p class="mt-1 text-sm font-semibold text-slate-500">
                                {{ selectedInvoice.invoice_no }} · {{ selectedInvoice.admission_no || '-' }}
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
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedInvoice.class_name }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Fee Type</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedInvoice.fee_type }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Amount</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ money(selectedInvoice.amount) }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Discount</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ money(selectedInvoice.discount) }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Paid Amount</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ money(selectedInvoice.paid_amount) }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Balance</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ money(selectedInvoice.balance) }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Due Date</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedInvoice.due_date || '-' }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Status</p>
                                <p class="mt-1">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-bold ring-1"
                                        :class="
                                            selectedInvoice.status === 'Paid'
                                                ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                                : selectedInvoice.status === 'Partial'
                                                    ? 'bg-amber-50 text-amber-700 ring-amber-100'
                                                    : selectedInvoice.status === 'Pending'
                                                        ? 'bg-sky-50 text-sky-700 ring-sky-100'
                                                        : 'bg-rose-50 text-rose-700 ring-rose-100'
                                        "
                                    >
                                        {{ selectedInvoice.status }}
                                    </span>
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100 sm:col-span-2">
                                <p class="text-xs font-bold uppercase text-slate-400">Notes</p>
                                <p class="mt-1 text-sm font-bold leading-6 text-slate-800">
                                    {{ selectedInvoice.notes || '-' }}
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
                            @click="openEditModal(selectedInvoice); showViewModal = false"
                        >
                            Edit Invoice
                        </button>
                        <button
    type="button"
    class="rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-emerald-700"
    @click="downloadInvoice(selectedInvoice)"
>
    Download Invoice
</button>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div
                v-if="showEditModal && selectedInvoice"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
            >
                <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">Edit Invoice</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Update invoice details and payment status.
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
                                <label class="text-sm font-semibold text-slate-600">Student Name *</label>
                                <input
                                    v-model="editForm.student_name"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'student_name')"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Invoice No *</label>
                                <input
                                    v-model="editForm.invoice_no"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'invoice_no')"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Admission No</label>
                                <input
                                    v-model="editForm.admission_no"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
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
                                <label class="text-sm font-semibold text-slate-600">Fee Type *</label>
                                <select
                                    v-model="editForm.fee_type"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'fee_type')"
                                >
                                    <option value="">Select fee type</option>
                                    <option v-for="feeType in feeTypeOptions" :key="feeType" :value="feeType">
                                        {{ feeType }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Amount *</label>
                                <input
                                    v-model="editForm.amount"
                                    type="number"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'amount')"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Discount</label>
                                <input
                                    v-model="editForm.discount"
                                    type="number"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'discount')"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Paid Amount</label>
                                <input
                                    v-model="editForm.paid_amount"
                                    type="number"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'paid_amount')"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Due Date</label>
                                <input
                                    v-model="editForm.due_date"
                                    type="date"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Payment Method</label>
                                <select
                                    v-model="editForm.payment_method"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option value="">Select method</option>
                                    <option>Cash</option>
                                    <option>Card</option>
                                    <option>Bank Transfer</option>
                                    <option>Online Payment</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status</label>
                                <select
                                    v-model="editForm.status"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                >
                                    <option>Pending</option>
                                    <option>Paid</option>
                                    <option>Partial</option>
                                    <option>Overdue</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Notes</label>
                                <textarea
                                    v-model="editForm.notes"
                                    rows="3"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'notes')"
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
                            @click="updateInvoice"
                        >
                            {{ editForm.processing ? 'Updating...' : 'Update Invoice' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>