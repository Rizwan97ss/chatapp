<script setup>
import { computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
const page = usePage()

const form = useForm({
    application_no: '',
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
    address: '',
    remarks: '',
})

const validateAdmission = computed(() => {
    const errors = {}

    if (!form.student_name.trim()) errors.student_name = 'Student name is required.'
    if (!form.applied_class) errors.applied_class = 'Applied class is required.'
    if (!form.parent_name.trim()) errors.parent_name = 'Parent name is required.'
    if (!form.parent_phone.trim()) errors.parent_phone = 'Parent phone is required.'

    if (form.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.email = 'Enter a valid student email.'
    }

    if (form.parent_email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.parent_email)) {
        errors.parent_email = 'Enter a valid parent email.'
    }

    if (form.date_of_birth) {
        const dob = new Date(form.date_of_birth)
        const today = new Date()
        today.setHours(0, 0, 0, 0)

        if (dob >= today) errors.date_of_birth = 'Date of birth must be before today.'
    }

    if (form.phone && form.phone.length > 30) errors.phone = 'Phone number is too long.'
    if (form.parent_phone && form.parent_phone.length > 30) errors.parent_phone = 'Parent phone is too long.'
    if (form.address && form.address.length > 1000) errors.address = 'Address must not be greater than 1000 characters.'
    if (form.remarks && form.remarks.length > 1000) errors.remarks = 'Remarks must not be greater than 1000 characters.'

    return errors
})

const canSubmit = computed(() => Object.keys(validateAdmission.value).length === 0)

const fieldError = (field) => validateAdmission.value[field] || form.errors[field]

const inputClass = (field) => {
    return fieldError(field)
        ? 'border-rose-300 bg-rose-50 focus:border-rose-400 focus:ring-rose-100'
        : 'border-slate-200 bg-white focus:border-indigo-500 focus:ring-indigo-100'
}

const submitAdmission = () => {
    if (!canSubmit.value || form.processing) {
        toast.error('Please complete all required fields.')
        return
    }

    form.post(route('admissions.public.store'), {
        preserveScroll: true,

        onSuccess: () => {
            toast.success('Your admission application has been submitted successfully.')
            form.reset()
        },

        onError: (errors) => {
            if (errors.server) {
                toast.error(errors.server)
            } else {
                toast.error('Please check the form errors and try again.')
            }
        },
    })
}
</script>

<template>
    <AppLayout title="Admission Application">
        <div class="space-y-6">
            <!-- Header -->
            <div class="overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-6 ring-1 ring-slate-200 lg:p-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">
                            Online Admission
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                            Admission Application Form
                        </h1>

                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500">
                            Submit student and parent information for admission review. Our team will contact you after checking the application.
                        </p>
                    </div>

                    <div class="rounded-2xl bg-white px-5 py-4 shadow-sm ring-1 ring-slate-200">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Application Status
                        </p>
                        <p class="mt-1 text-sm font-black text-indigo-600">
                            New Application
                        </p>
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            <div
                v-if="page.props.flash?.success"
                class="rounded-2xl bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700 ring-1 ring-emerald-100"
            >
                {{ page.props.flash.success }}
            </div>

            <div
                v-if="form.errors.server"
                class="rounded-2xl bg-rose-50 px-5 py-4 text-sm font-bold text-rose-700 ring-1 ring-rose-100"
            >
                {{ form.errors.server }}
            </div>

            <!-- Main Form -->
            <form class="grid gap-6 xl:grid-cols-[260px_1fr]" @submit.prevent="submitAdmission">
                <!-- Side Card -->
                <aside class="hidden xl:block">
                    <div class="sticky top-6 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                        <h2 class="text-base font-black text-slate-950">
                            Form Sections
                        </h2>

                        <div class="mt-5 space-y-3">
                            <a href="#student-info" class="flex items-center gap-3 rounded-2xl bg-indigo-50 px-4 py-3 text-sm font-bold text-indigo-700">
                                <span class="flex size-8 items-center justify-center rounded-xl bg-indigo-600 text-xs text-white">1</span>
                                Student Info
                            </a>

                            <a href="#parent-info" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                                <span class="flex size-8 items-center justify-center rounded-xl bg-slate-100 text-xs text-slate-700">2</span>
                                Parent Info
                            </a>

                            <a href="#additional-info" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                                <span class="flex size-8 items-center justify-center rounded-xl bg-slate-100 text-xs text-slate-700">3</span>
                                Additional
                            </a>
                        </div>

                        <div class="mt-6 rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                            <p class="text-sm font-black text-slate-800">
                                Required Fields
                            </p>
                            <p class="mt-2 text-xs leading-5 text-slate-500">
                                Student name, applied class, parent name and parent phone are required.
                            </p>
                        </div>
                    </div>
                </aside>

                <!-- Form Card -->
                <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
                    <div class="border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white p-6">
                        <h2 class="text-xl font-black text-slate-950">
                            Applicant Details
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Fields marked with <span class="font-black text-rose-500">*</span> are required.
                        </p>
                    </div>

                    <div class="space-y-6 p-5 sm:p-6">
                        <!-- Student Information -->
                        <section id="student-info" class="rounded-3xl border border-slate-200 bg-white p-5">
                            <div class="flex items-center gap-3">
                                <div class="flex size-11 items-center justify-center rounded-2xl bg-indigo-600 text-sm font-black text-white">
                                    1
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-slate-950">
                                        Student Information
                                    </h3>
                                    <p class="text-sm text-slate-500">
                                        Basic student and requested class details.
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6 grid gap-5 md:grid-cols-2">
                                <div>
                                    <label class="text-sm font-bold text-slate-700">Student Name *</label>
                                    <input
                                        v-model="form.student_name"
                                        type="text"
                                        class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:ring-4"
                                        :class="inputClass('student_name')"
                                        placeholder="Enter student full name"
                                    />
                                    <p v-if="fieldError('student_name')" class="mt-1 text-xs font-bold text-rose-600">
                                        {{ fieldError('student_name') }}
                                    </p>
                                </div>

                                <div>
                                    <label class="text-sm font-bold text-slate-700">Applied Class *</label>
                                    <select
                                        v-model="form.applied_class"
                                        class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm text-slate-800 outline-none transition focus:ring-4"
                                        :class="inputClass('applied_class')"
                                    >
                                        <option value="">Select class</option>
                                        <option>Grade 5</option>
                                        <option>Grade 6</option>
                                        <option>Grade 7</option>
                                        <option>Grade 8</option>
                                        <option>Grade 9</option>
                                        <option>Grade 10</option>
                                    </select>
                                    <p v-if="fieldError('applied_class')" class="mt-1 text-xs font-bold text-rose-600">
                                        {{ fieldError('applied_class') }}
                                    </p>
                                </div>

                                <div>
                                    <label class="text-sm font-bold text-slate-700">Gender</label>
                                    <select
                                        v-model="form.gender"
                                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                                    >
                                        <option value="">Select gender</option>
                                        <option>Male</option>
                                        <option>Female</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="text-sm font-bold text-slate-700">Date of Birth</label>
                                    <input
                                        v-model="form.date_of_birth"
                                        type="date"
                                        class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm text-slate-800 outline-none transition focus:ring-4"
                                        :class="inputClass('date_of_birth')"
                                    />
                                    <p v-if="fieldError('date_of_birth')" class="mt-1 text-xs font-bold text-rose-600">
                                        {{ fieldError('date_of_birth') }}
                                    </p>
                                </div>

                                <div>
                                    <label class="text-sm font-bold text-slate-700">Student Email</label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:ring-4"
                                        :class="inputClass('email')"
                                        placeholder="student@example.com"
                                    />
                                    <p v-if="fieldError('email')" class="mt-1 text-xs font-bold text-rose-600">
                                        {{ fieldError('email') }}
                                    </p>
                                </div>

                                <div>
                                    <label class="text-sm font-bold text-slate-700">Student Phone</label>
                                    <input
                                        v-model="form.phone"
                                        type="text"
                                        class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:ring-4"
                                        :class="inputClass('phone')"
                                        placeholder="+971 50 000 0000"
                                    />
                                    <p v-if="fieldError('phone')" class="mt-1 text-xs font-bold text-rose-600">
                                        {{ fieldError('phone') }}
                                    </p>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="text-sm font-bold text-slate-700">Previous School</label>
                                    <input
                                        v-model="form.previous_school"
                                        type="text"
                                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                                        placeholder="Previous school name"
                                    />
                                </div>
                            </div>
                        </section>

                        <!-- Parent Information -->
                        <section id="parent-info" class="rounded-3xl border border-slate-200 bg-white p-5">
                            <div class="flex items-center gap-3">
                                <div class="flex size-11 items-center justify-center rounded-2xl bg-emerald-600 text-sm font-black text-white">
                                    2
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-slate-950">
                                        Parent / Guardian Information
                                    </h3>
                                    <p class="text-sm text-slate-500">
                                        Contact details for admission follow-up.
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6 grid gap-5 md:grid-cols-2">
                                <div>
                                    <label class="text-sm font-bold text-slate-700">Parent Name *</label>
                                    <input
                                        v-model="form.parent_name"
                                        type="text"
                                        class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:ring-4"
                                        :class="inputClass('parent_name')"
                                        placeholder="Parent / guardian name"
                                    />
                                    <p v-if="fieldError('parent_name')" class="mt-1 text-xs font-bold text-rose-600">
                                        {{ fieldError('parent_name') }}
                                    </p>
                                </div>

                                <div>
                                    <label class="text-sm font-bold text-slate-700">Parent Phone *</label>
                                    <input
                                        v-model="form.parent_phone"
                                        type="text"
                                        class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:ring-4"
                                        :class="inputClass('parent_phone')"
                                        placeholder="+971 50 000 0000"
                                    />
                                    <p v-if="fieldError('parent_phone')" class="mt-1 text-xs font-bold text-rose-600">
                                        {{ fieldError('parent_phone') }}
                                    </p>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="text-sm font-bold text-slate-700">Parent Email</label>
                                    <input
                                        v-model="form.parent_email"
                                        type="email"
                                        class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:ring-4"
                                        :class="inputClass('parent_email')"
                                        placeholder="parent@example.com"
                                    />
                                    <p v-if="fieldError('parent_email')" class="mt-1 text-xs font-bold text-rose-600">
                                        {{ fieldError('parent_email') }}
                                    </p>
                                </div>
                            </div>
                        </section>

                        <!-- Additional Information -->
                        <section id="additional-info" class="rounded-3xl border border-slate-200 bg-white p-5">
                            <div class="flex items-center gap-3">
                                <div class="flex size-11 items-center justify-center rounded-2xl bg-amber-500 text-sm font-black text-white">
                                    3
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-slate-950">
                                        Additional Details
                                    </h3>
                                    <p class="text-sm text-slate-500">
                                        Address and optional remarks.
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6 grid gap-5">
                                <div>
                                    <label class="text-sm font-bold text-slate-700">Address</label>
                                    <textarea
                                        v-model="form.address"
                                        rows="3"
                                        class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:ring-4"
                                        :class="inputClass('address')"
                                        placeholder="Enter full address"
                                    ></textarea>
                                    <p v-if="fieldError('address')" class="mt-1 text-xs font-bold text-rose-600">
                                        {{ fieldError('address') }}
                                    </p>
                                </div>

                                <div>
                                    <label class="text-sm font-bold text-slate-700">Remarks</label>
                                    <textarea
                                        v-model="form.remarks"
                                        rows="3"
                                        class="mt-2 w-full rounded-2xl border px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:ring-4"
                                        :class="inputClass('remarks')"
                                        placeholder="Any additional notes"
                                    ></textarea>
                                    <p v-if="fieldError('remarks')" class="mt-1 text-xs font-bold text-rose-600">
                                        {{ fieldError('remarks') }}
                                    </p>
                                </div>
                            </div>
                        </section>

                        <!-- Submit -->
<div class="sticky bottom-0 z-10 border-t border-slate-200 bg-white/80 backdrop-blur">
    <div class="flex flex-col gap-4 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-slate-700">
                Ready to submit your application?
            </p>
            <p class="text-xs text-slate-500">
                Please make sure all required fields are filled correctly.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <span
                v-if="!canSubmit"
                class="hidden sm:inline text-xs font-semibold text-rose-500"
            >
                Complete required fields
            </span>

            <button
                type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-slate-950 px-6 py-3 text-sm font-bold text-white shadow-md transition hover:bg-slate-800 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="form.processing || !canSubmit"
            >
                <svg
                    v-if="form.processing"
                    class="h-4 w-4 animate-spin"
                    viewBox="0 0 24 24"
                    fill="none"
                >
                    <circle
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="3"
                        class="opacity-25"
                    />
                    <path
                        d="M22 12a10 10 0 0 1-10 10"
                        stroke="currentColor"
                        stroke-width="3"
                        class="opacity-75"
                    />
                </svg>

                {{ form.processing ? 'Submitting...' : 'Submit Application' }}
            </button>
        </div>
    </div>
</div>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>