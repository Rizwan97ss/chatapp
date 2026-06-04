<script setup>
import { ref, computed } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    books: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            category: '',
            shelf: '',
            status: '',
        }),
    },
    libraryStats: {
        type: Object,
        default: () => ({
            cards: [],
            overview: {},
            categoryBars: [],
        }),
    },
    categories: {
        type: Array,
        default: () => [],
    },
    shelves: {
        type: Array,
        default: () => [],
    },
    issueRecords: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    availableBooks: {
        type: Array,
        default: () => [],
    },
})

const bookList = computed(() => props.books || { data: [], links: [] })
const statCards = computed(() => props.libraryStats?.cards || [])
const overview = computed(() => props.libraryStats?.overview || {})
const categoryBars = computed(() => props.libraryStats?.categoryBars || [])
const issueBookOptions = computed(() => props.availableBooks || [])
const defaultCategories = ['Mathematics', 'English', 'Science', 'History', 'Computer']
const defaultShelves = ['A-12', 'B-08', 'C-04', 'D-02']

const categoryOptions = computed(() => [...new Set([...(props.categories || []), ...defaultCategories])])
const shelfOptions = computed(() => [...new Set([...(props.shelves || []), ...defaultShelves])])

const showCreateModal = ref(false)
const showViewModal = ref(false)
const showEditModal = ref(false)
const selectedBook = ref(null)

const filterForm = useForm({
    search: props.filters?.search || '',
    category: props.filters?.category || '',
    shelf: props.filters?.shelf || '',
    status: props.filters?.status || '',
})
const showReturnModal = ref(false)
const selectedIssue = ref(null)

const returnForm = useForm({
    late_fee: 0,
    lost_fee: 0,
    broken_fee: 0,
    fee_status: 'Paid',
    payment_method: '',
    return_notes: '',
})

const totalReturnFee = computed(() => {
    return Number(returnForm.late_fee || 0)
        + Number(returnForm.lost_fee || 0)
        + Number(returnForm.broken_fee || 0)
})

const openReturnModal = (issue) => {
    selectedIssue.value = issue
    returnForm.reset()
    returnForm.late_fee = 0
    returnForm.lost_fee = 0
    returnForm.broken_fee = 0
    returnForm.fee_status = 'Paid'
    returnForm.clearErrors()
    showReturnModal.value = true
}

const closeReturnModal = () => {
    selectedIssue.value = null
    returnForm.clearErrors()
    showReturnModal.value = false
}

const submitReturn = () => {
    if (!selectedIssue.value || returnForm.processing) return

    returnForm.put(route('library.return', selectedIssue.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Book returned successfully.')
            closeReturnModal()
        },
        onError: () => toast.error('Please check return form errors.'),
    })
}
const form = useForm({
    book_no: '',
    title: '',
    author: '',
    category: '',
    isbn: '',
    shelf: '',
    copies: 0,
    status: 'Available',
    description: '',
})

const editForm = useForm({
    book_no: '',
    title: '',
    author: '',
    category: '',
    isbn: '',
    shelf: '',
    copies: 0,
    status: 'Available',
    description: '',
})

const validateBook = (targetForm) => {
    const errors = {}

    if (!targetForm.book_no.trim()) errors.book_no = 'Book ID is required.'
    if (!targetForm.title.trim()) errors.title = 'Book title is required.'

    if (targetForm.copies === '' || Number(targetForm.copies) < 0) {
        errors.copies = 'Valid copies count is required.'
    }

    if (targetForm.description && targetForm.description.length > 1000) {
        errors.description = 'Description must not be greater than 1000 characters.'
    }

    return errors
}

const frontendErrors = computed(() => validateBook(form))
const editFrontendErrors = computed(() => validateBook(editForm))

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
    form.copies = 0
    form.status = 'Available'
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

const submitBook = () => {
    if (!canSubmit.value || form.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    form.post(route('library.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Book created successfully.')
            resetForm()
            showCreateModal.value = false
        },
        onError: () => toast.error('Please check the form errors.'),
    })
}

const openViewModal = (book) => {
    selectedBook.value = book
    showViewModal.value = true
}

const closeViewModal = () => {
    selectedBook.value = null
    showViewModal.value = false
}

const openEditModal = (book) => {
    selectedBook.value = book

    editForm.book_no = book.book_no || ''
    editForm.title = book.title || ''
    editForm.author = book.author || ''
    editForm.category = book.category || ''
    editForm.isbn = book.isbn || ''
    editForm.shelf = book.shelf || ''
    editForm.copies = book.copies ?? 0
    editForm.status = book.status || 'Available'
    editForm.description = book.description || ''

    editForm.clearErrors()
    showEditModal.value = true
}

const closeEditModal = () => {
    selectedBook.value = null
    editForm.clearErrors()
    showEditModal.value = false
}

const updateBook = () => {
    if (!selectedBook.value || !canUpdate.value || editForm.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    editForm.put(route('library.update', selectedBook.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Book updated successfully.')
            closeEditModal()
        },
        onError: () => toast.error('Please check the form errors.'),
    })
}

const deleteBook = (book) => {
    if (!confirm(`Delete book ${book.book_no}?`)) return

    router.delete(route('library.destroy', book.id), {
        preserveScroll: true,
        onSuccess: () => toast.success('Book deleted successfully.'),
        onError: () => toast.error('Unable to delete book.'),
    })
}

const applyFilters = () => {
    router.get(route('library'), {
        search: filterForm.search,
        category: filterForm.category,
        shelf: filterForm.shelf,
        status: filterForm.status,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

const resetFilters = () => {
    filterForm.search = ''
    filterForm.category = ''
    filterForm.shelf = ''
    filterForm.status = ''

    router.get(route('library'), {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

const issueList = computed(() => props.issueRecords || { data: [], links: [] })
const showIssueModal = ref(false)

const issueForm = useForm({
    library_book_id: '',
    student_name: '',
    admission_no: '',
    class_name: '',
    issue_date: new Date().toISOString().slice(0, 10),
    due_date: '',
    notes: '',
})

const openIssueModal = (book = null) => {
    issueForm.clearErrors()
    issueForm.library_book_id = book?.id || ''
    showIssueModal.value = true
}

const closeIssueModal = () => {
    issueForm.reset()
    issueForm.issue_date = new Date().toISOString().slice(0, 10)
    showIssueModal.value = false
}

const submitIssue = () => {
    issueForm.post(route('library.issue'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Book issued successfully.')
            closeIssueModal()
        },
        onError: () => toast.error('Please check the issue form.'),
    })
}

const returnBook = (issue) => {
    if (!confirm('Mark this book as returned?')) return

    router.put(route('library.return', issue.id), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success('Book returned successfully.'),
    })
}

const deleteIssue = (issue) => {
    if (!confirm('Delete this issue record?')) return

    router.delete(route('library.issue.destroy', issue.id), {
        preserveScroll: true,
        onSuccess: () => toast.success('Issue record deleted.'),
    })
}
// Import Books
const importForm = useForm({
    file: null,
})

const submitImport = () => {
    if (!importForm.file || importForm.processing) {
        toast.error('Please select an Excel file.')
        return
    }

    importForm.post(route('library.import'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            toast.success('Books imported successfully.')
            importForm.reset()
        },
        onError: () => toast.error('Import failed. Please check your file.'),
    })
}
</script>

<template>
    <AppLayout title="Library">
        <div class="space-y-6">
            <!-- Header -->
            <div
                class="rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 ring-1 ring-slate-200 sm:p-6 lg:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">Library Management</p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                            Library
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                            Manage books, categories, shelves, issued records and overdue tracking.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <!-- <button type="button"
                            class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                            Export Books
                        </button> -->
                        <form class="flex gap-2" @submit.prevent="submitImport">
                            <input type="file" accept=".xlsx,.xls,.csv"
                                class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-600"
                                @change="importForm.file = $event.target.files[0]" />

                            <button type="submit"
                                class="rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-100 transition hover:bg-emerald-700 disabled:opacity-50"
                                :disabled="importForm.processing">
                                {{ importForm.processing ? 'Importing...' : 'Import' }}
                            </button>
                        </form>
                        <button type="button"
                            class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800"
                            @click="openCreateModal">
                            Add Book
                        </button>
                        <button type="button"
                            class="rounded-xl bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-100"
                            @click="openIssueModal(book)">
                            Issue Book
                        </button>
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
                            index === 1 ? 'bg-gradient-to-br from-amber-500 to-orange-400' : '',
                            index === 2 ? 'bg-gradient-to-br from-emerald-500 to-teal-400' : '',
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

            <!-- Filters -->
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <form class="grid gap-4 md:grid-cols-2 xl:grid-cols-6" @submit.prevent="applyFilters">
                    <div class="xl:col-span-2">
                        <label class="text-sm font-semibold text-slate-600">Search Book</label>
                        <input v-model="filterForm.search" type="text"
                            placeholder="Search by title, author, ISBN or category"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Category</label>
                        <select v-model="filterForm.category"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option value="">All Categories</option>
                            <option v-for="category in categoryOptions" :key="category" :value="category">
                                {{ category }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Shelf</label>
                        <select v-model="filterForm.shelf"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option value="">All Shelves</option>
                            <option v-for="shelf in shelfOptions" :key="shelf" :value="shelf">
                                {{ shelf }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-600">Status</label>
                        <select v-model="filterForm.status"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                            <option value="">All Status</option>
                            <option>Available</option>
                            <option>Issued</option>
                            <option>Overdue</option>
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

            <!-- Overview -->
            <!-- <div class="grid gap-6 xl:grid-cols-3">
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 xl:col-span-2">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Category Availability</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Book distribution percentage by category.
                            </p>
                        </div>

                        <span
                            class="w-fit rounded-full bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                            {{ overview.available_percentage || 0 }}% Available
                        </span>
                    </div>

                    <div class="mt-8 space-y-5">
                        <div v-for="item in categoryBars" :key="item.label">
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

                        <p v-if="categoryBars.length === 0"
                            class="rounded-2xl bg-slate-50 p-5 text-sm font-bold text-slate-400">
                            No category data available.
                        </p>
                    </div>
                </div>

                <div
                    class="rounded-3xl bg-gradient-to-br from-indigo-600 to-sky-500 p-6 text-white shadow-lg shadow-indigo-100">
                    <h3 class="text-lg font-black">Library Summary</h3>

                    <p class="mt-1 text-sm text-white/75">
                        Current library snapshot
                    </p>

                    <div class="mt-6 space-y-4">
                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Available</p>
                            <p class="mt-1 text-xl font-black">{{ overview.available || 0 }} Books</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Issued</p>
                            <p class="mt-1 text-xl font-black">{{ overview.issued || 0 }} Books</p>
                        </div>

                        <div class="rounded-2xl bg-white/15 p-4 ring-1 ring-white/20">
                            <p class="text-xs text-white/70">Overdue</p>
                            <p class="mt-1 text-xl font-black">{{ overview.overdue || 0 }} Books</p>
                        </div>
                    </div>
                </div>
            </div> -->

            <!-- Book Cards -->
            <!-- <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <div v-for="book in bookList.data" :key="book.id"
                    class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                {{ book.book_no }}
                            </p>

                            <h2 class="mt-2 text-lg font-black text-slate-950">
                                {{ book.title }}
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                {{ book.author || '-' }}
                            </p>
                        </div>

                        <span class="rounded-full px-3 py-1 text-xs font-bold ring-1" :class="book.status === 'Available'
                            ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                            : book.status === 'Issued'
                                ? 'bg-indigo-50 text-indigo-700 ring-indigo-100'
                                : 'bg-rose-50 text-rose-700 ring-rose-100'
                            ">
                            {{ book.status }}
                        </span>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-indigo-50 p-4">
                            <p class="text-xs font-semibold text-indigo-500">Category</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ book.category || '-' }}</p>
                        </div>

                        <div class="rounded-2xl bg-sky-50 p-4">
                            <p class="text-xs font-semibold text-sky-500">Shelf</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ book.shelf || '-' }}</p>
                        </div>

                        <div class="rounded-2xl bg-violet-50 p-4">
                            <p class="text-xs font-semibold text-violet-500">Copies</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">{{ book.copies }}</p>
                        </div>

                        <div class="rounded-2xl bg-amber-50 p-4">
                            <p class="text-xs font-semibold text-amber-500">ISBN</p>
                            <p class="mt-1 truncate text-sm font-bold text-slate-900">{{ book.isbn || '-' }}</p>
                        </div>
                    </div>

                    <div class="mt-5 flex gap-2">
                        <button type="button"
                            class="flex-1 rounded-2xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                            @click="openViewModal(book)">
                            View
                        </button>

                        <button type="button"
                            class="flex-1 rounded-2xl bg-indigo-50 px-4 py-2.5 text-sm font-bold text-indigo-700 transition hover:bg-indigo-100"
                            @click="openEditModal(book)">
                            Edit
                        </button>
                    </div>
                </div>

                <div v-if="bookList.data.length === 0"
                    class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 md:col-span-2 xl:col-span-4">
                    <p class="text-sm font-bold text-slate-900">No books found</p>
                    <p class="mt-1 text-xs text-slate-500">Add books to see library records.</p>
                </div>
            </div> -->

            <!-- Books Table -->
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
                <div
                    class="flex flex-col gap-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">Book Records</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Complete library book records.
                        </p>
                    </div>
                    <button type="button"
                        class="rounded-xl bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-100"
                        @click="openIssueModal(book)">
                        Issue
                    </button>
                    <button type="button"
                        class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800"
                        @click="openCreateModal">
                        New Book
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1050px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Book</th>
                                <th class="px-6 py-4">Book ID</th>
                                <th class="px-6 py-4">Author</th>
                                <th class="px-6 py-4">Category</th>
                                <th class="px-6 py-4">ISBN</th>
                                <th class="px-6 py-4">Shelf</th>
                                <th class="px-6 py-4">Copies</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="book in bookList.data" :key="book.id" class="transition hover:bg-indigo-50/40">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex size-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 to-sky-100 text-sm font-black text-indigo-700">
                                            {{ book.title?.charAt(0) || 'B' }}
                                        </div>

                                        <div>
                                            <p class="font-bold text-slate-950">
                                                {{ book.title }}
                                            </p>
                                            <p class="text-xs text-slate-400">
                                                {{ book.category || '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-700">
                                    {{ book.book_no }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ book.author || '-' }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ book.category || '-' }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ book.isbn || '-' }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ book.shelf || '-' }}
                                </td>

                                <td class="px-6 py-4 font-bold text-slate-900">
                                    {{ book.copies }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold ring-1" :class="book.status === 'Available'
                                        ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                        : book.status === 'Issued'
                                            ? 'bg-indigo-50 text-indigo-700 ring-indigo-100'
                                            : 'bg-rose-50 text-rose-700 ring-rose-100'
                                        ">
                                        {{ book.status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button type="button"
                                            class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200"
                                            @click="openViewModal(book)">
                                            View
                                        </button>

                                        <button type="button"
                                            class="rounded-xl bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100"
                                            @click="openEditModal(book)">
                                            Edit
                                        </button>

                                        <button type="button"
                                            class="rounded-xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100"
                                            @click="deleteBook(book)">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="bookList.data.length === 0">
                                <td colspan="9" class="px-6 py-10 text-center text-sm font-bold text-slate-400">
                                    No books found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="bookList.links && bookList.links.length > 3"
                    class="flex flex-col gap-4 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm font-semibold text-slate-500">
                        Showing
                        <span class="font-black text-slate-800">{{ bookList.from || 0 }}</span>
                        to
                        <span class="font-black text-slate-800">{{ bookList.to || 0 }}</span>
                        of
                        <span class="font-black text-slate-800">{{ bookList.total || 0 }}</span>
                        books
                    </p>

                    <div class="flex flex-wrap gap-2">
                        <Link v-for="link in bookList.links" :key="link.label" :href="link.url || '#'" preserve-scroll
                            class="rounded-xl px-4 py-2 text-sm font-bold transition" :class="[
                                link.active
                                    ? 'bg-slate-950 text-white'
                                    : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-100',
                                !link.url ? 'pointer-events-none cursor-not-allowed opacity-40' : ''
                            ]" v-html="link.label" />
                    </div>
                </div>
            </div>
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
                <div
                    class="flex flex-col gap-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">Book Issue Management</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Manage issued, returned and overdue books.
                        </p>
                    </div>

                    <button type="button"
                        class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800"
                        @click="openIssueModal()">
                        Issue Book
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1050px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Book</th>
                                <th class="px-6 py-4">Student</th>
                                <th class="px-6 py-4">Admission No</th>
                                <th class="px-6 py-4">Class</th>

                                <th class="px-6 py-4">Issue Date</th>
                                <th class="px-6 py-4">Due Date</th>
                                <th class="px-6 py-4">Return Date</th>
                                <th class="px-6 py-4">Fine</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Fee Status</th>

                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="issue in issueList.data" :key="issue.id">
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    {{ issue.book?.title || '-' }}
                                    <p class="text-xs text-slate-400">{{ issue.book?.book_no || '-' }}</p>
                                </td>

                                <td class="px-6 py-4 text-slate-500">{{ issue.student_name }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ issue.admission_no || '-' }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ issue.class_name || '-' }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ issue.issue_date || '-' }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ issue.due_date || '-' }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ issue.return_date || '-' }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    AED {{ Number(issue.total_fee || 0).toFixed(2) }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold ring-1" :class="issue.fee_status === 'Paid'
                                            ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                            : 'bg-amber-50 text-amber-700 ring-amber-100'
                                        ">
                                        {{ issue.fee_status || 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold ring-1" :class="issue.status === 'Returned'
                                        ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                        : issue.status === 'Issued'
                                            ? 'bg-indigo-50 text-indigo-700 ring-indigo-100'
                                            : 'bg-rose-50 text-rose-700 ring-rose-100'
                                        ">
                                        {{ issue.status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button v-if="issue.status !== 'Returned'" type="button"
                                            class="rounded-xl bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-100"
                                            @click="openReturnModal(issue)">
                                            Return
                                        </button>

                                        <button type="button"
                                            class="rounded-xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100"
                                            @click="deleteIssue(issue)">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="issueList.data.length === 0">
                                <td colspan="9" class="px-6 py-10 text-center text-sm font-bold text-slate-400">
                                    No issued books found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Create Modal -->
            <div v-if="showCreateModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
                <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div
                        class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">Add Book</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Add book details, shelf number, copies and availability status.
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
                                <label class="text-sm font-semibold text-slate-600">Book Title *</label>
                                <input v-model="form.title"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'title')"
                                    placeholder="Advanced Mathematics" />
                                <p v-if="fieldError(frontendErrors, form, 'title')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'title') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Book ID *</label>
                                <input v-model="form.book_no"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'book_no')" placeholder="BK-1005" />
                                <p v-if="fieldError(frontendErrors, form, 'book_no')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'book_no') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Author</label>
                                <input v-model="form.author"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                    placeholder="Author name" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Category</label>
                                <select v-model="form.category"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option value="">Select category</option>
                                    <option v-for="category in categoryOptions" :key="category" :value="category">
                                        {{ category }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">ISBN</label>
                                <input v-model="form.isbn"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                    placeholder="978-1001234567" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Shelf Number</label>
                                <select v-model="form.shelf"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option value="">Select shelf</option>
                                    <option v-for="shelf in shelfOptions" :key="shelf" :value="shelf">
                                        {{ shelf }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Copies *</label>
                                <input v-model="form.copies" type="number"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'copies')" placeholder="10" />
                                <p v-if="fieldError(frontendErrors, form, 'copies')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'copies') }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status</label>
                                <select v-model="form.status"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>Available</option>
                                    <option>Issued</option>
                                    <option>Overdue</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Description</label>
                                <textarea v-model="form.description" rows="3" placeholder="Optional book description"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(frontendErrors, form, 'description')"></textarea>
                                <p v-if="fieldError(frontendErrors, form, 'description')"
                                    class="mt-1 text-xs font-bold text-rose-600">
                                    {{ fieldError(frontendErrors, form, 'description') }}
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
                            :disabled="form.processing || !canSubmit" @click="submitBook">
                            {{ form.processing ? 'Saving...' : 'Save Book' }}
                        </button>
                    </div>
                </div>
            </div>
            <!-- issue modal -->
            <div v-if="showIssueModal"
                class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
                <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div
                        class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">Issue Book</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Assign a book to a student and track due date.
                            </p>
                        </div>

                        <button type="button"
                            class="rounded-2xl bg-white p-2 text-slate-400 shadow-sm ring-1 ring-slate-200 transition hover:text-slate-700"
                            @click="closeIssueModal">
                            ✕
                        </button>
                    </div>

                    <div class="max-h-[75vh] overflow-y-auto p-5 sm:p-6">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="text-sm font-semibold text-slate-600">Book *</label>
                                <select v-model="issueForm.library_book_id"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option value="">Select book</option>
                                    <option v-for="book in issueBookOptions" :key="book.id" :value="book.id">
                                        {{ book.book_no }} - {{ book.title }}
                                    </option>
                                </select>
                                <p v-if="issueForm.errors.library_book_id" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ issueForm.errors.library_book_id }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Student Name *</label>
                                <input v-model="issueForm.student_name"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                    placeholder="Ali Khan" />
                                <p v-if="issueForm.errors.student_name" class="mt-1 text-xs font-bold text-rose-600">
                                    {{ issueForm.errors.student_name }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Admission No</label>
                                <input v-model="issueForm.admission_no"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                    placeholder="STU-1001" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Class</label>
                                <input v-model="issueForm.class_name"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                    placeholder="Grade 8" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Issue Date *</label>
                                <input v-model="issueForm.issue_date" type="date"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Due Date</label>
                                <input v-model="issueForm.due_date" type="date"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Notes</label>
                                <textarea v-model="issueForm.notes" rows="3" placeholder="Optional issue notes"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"></textarea>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 p-5 sm:flex-row sm:justify-end sm:p-6">
                        <button type="button"
                            class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                            @click="closeIssueModal">
                            Cancel
                        </button>

                        <button type="button"
                            class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="issueForm.processing" @click="submitIssue">
                            {{ issueForm.processing ? 'Issuing...' : 'Issue Book' }}
                        </button>
                    </div>
                </div>
            </div>
            <!-- View Modal -->
            <div v-if="showViewModal && selectedBook"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
                <div class="w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div
                        class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">{{ selectedBook.title }}</h2>
                            <p class="mt-1 text-sm font-semibold text-slate-500">
                                {{ selectedBook.book_no }} · {{ selectedBook.author || '-' }}
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
                                <p class="text-xs font-bold uppercase text-indigo-500">Category</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedBook.category || '-' }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Shelf</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedBook.shelf || '-' }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">ISBN</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedBook.isbn || '-' }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Copies</p>
                                <p class="mt-1 text-sm font-bold text-slate-800">{{ selectedBook.copies }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <p class="text-xs font-bold uppercase text-slate-400">Status</p>
                                <p class="mt-1">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold ring-1" :class="selectedBook.status === 'Available'
                                        ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                        : selectedBook.status === 'Issued'
                                            ? 'bg-indigo-50 text-indigo-700 ring-indigo-100'
                                            : 'bg-rose-50 text-rose-700 ring-rose-100'
                                        ">
                                        {{ selectedBook.status }}
                                    </span>
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100 sm:col-span-2">
                                <p class="text-xs font-bold uppercase text-slate-400">Description</p>
                                <p class="mt-1 text-sm font-bold leading-6 text-slate-800">
                                    {{ selectedBook.description || '-' }}
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
                            @click="openEditModal(selectedBook); showViewModal = false">
                            Edit Book
                        </button>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div v-if="showEditModal && selectedBook"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
                <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                    <div
                        class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">Edit Book</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Update book details, shelf number, copies and status.
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
                                <label class="text-sm font-semibold text-slate-600">Book Title *</label>
                                <input v-model="editForm.title"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'title')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Book ID *</label>
                                <input v-model="editForm.book_no"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'book_no')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Author</label>
                                <input v-model="editForm.author"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Category</label>
                                <select v-model="editForm.category"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option value="">Select category</option>
                                    <option v-for="category in categoryOptions" :key="category" :value="category">
                                        {{ category }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">ISBN</label>
                                <input v-model="editForm.isbn"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Shelf Number</label>
                                <select v-model="editForm.shelf"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option value="">Select shelf</option>
                                    <option v-for="shelf in shelfOptions" :key="shelf" :value="shelf">
                                        {{ shelf }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Copies *</label>
                                <input v-model="editForm.copies" type="number"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'copies')" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-600">Status</label>
                                <select v-model="editForm.status"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                    <option>Available</option>
                                    <option>Issued</option>
                                    <option>Overdue</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-slate-600">Description</label>
                                <textarea v-model="editForm.description" rows="3"
                                    class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm outline-none focus:ring-4"
                                    :class="inputClass(editFrontendErrors, editForm, 'description')"></textarea>
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
                            :disabled="editForm.processing || !canUpdate" @click="updateBook">
                            {{ editForm.processing ? 'Updating...' : 'Update Book' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="showReturnModal && selectedIssue"
            class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
            <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                <div
                    class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 sm:p-6">
                    <div>
                        <h2 class="text-xl font-black text-slate-950">Return Book</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Collect late, lost or broken book charges before marking as returned.
                        </p>
                    </div>

                    <button type="button"
                        class="rounded-2xl bg-white p-2 text-slate-400 shadow-sm ring-1 ring-slate-200 transition hover:text-slate-700"
                        @click="closeReturnModal">
                        ✕
                    </button>
                </div>

                <div class="max-h-[75vh] overflow-y-auto p-5 sm:p-6">
                    <div class="mb-5 rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                        <p class="text-xs font-bold uppercase text-slate-400">Book</p>
                        <p class="mt-1 text-sm font-black text-slate-900">
                            {{ selectedIssue.book?.title || '-' }}
                        </p>
                        <p class="text-xs text-slate-500">
                            {{ selectedIssue.book?.book_no || '-' }} · {{ selectedIssue.student_name }}
                        </p>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-slate-600">Late Fee</label>
                            <input v-model="returnForm.late_fee" type="number" min="0"
                                class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                placeholder="0" />
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-600">Lost Book Fee</label>
                            <input v-model="returnForm.lost_fee" type="number" min="0"
                                class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                placeholder="0" />
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-600">Broken Book Fee</label>
                            <input v-model="returnForm.broken_fee" type="number" min="0"
                                class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                                placeholder="0" />
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-600">Payment Status</label>
                            <select v-model="returnForm.fee_status"
                                class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                <option>Paid</option>
                                <option>Pending</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-600">Payment Method</label>
                            <select v-model="returnForm.payment_method"
                                class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                                <option value="">Select method</option>
                                <option>Cash</option>
                                <option>Card</option>
                                <option>Bank Transfer</option>
                                <option>Online Payment</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-600">Total Fee</label>
                            <div
                                class="mt-2 rounded-2xl bg-slate-50 px-4 py-3 text-sm font-black text-slate-900 ring-1 ring-slate-200">
                                AED {{ totalReturnFee.toFixed(2) }}
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="text-sm font-semibold text-slate-600">Return Notes</label>
                            <textarea v-model="returnForm.return_notes" rows="3" placeholder="Optional return notes"
                                class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"></textarea>
                        </div>
                    </div>
                </div>

                <div
                    class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 p-5 sm:flex-row sm:justify-end sm:p-6">
                    <button type="button"
                        class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                        @click="closeReturnModal">
                        Cancel
                    </button>

                    <button type="button"
                        class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="returnForm.processing" @click="submitReturn">
                        {{ returnForm.processing ? 'Returning...' : 'Confirm Return' }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>