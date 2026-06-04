<script setup>
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const today = new Date();

const currentMonth = ref(today.getMonth());
const currentYear = ref(today.getFullYear());

const plans = ref([
    {
        id: 1,
        title: 'Parent meeting',
        date: '2026-04-28',
        type: 'Meeting',
    },
    {
        id: 2,
        title: 'Monthly exam preparation',
        date: '2026-04-30',
        type: 'Exam',
    },
]);

const form = ref({
    title: '',
    date: '',
    type: 'General',
});

const monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December',
];

const calendarDays = computed(() => {
    const firstDay = new Date(currentYear.value, currentMonth.value, 1);
    const lastDay = new Date(currentYear.value, currentMonth.value + 1, 0);
    const startDay = firstDay.getDay();

    const days = [];

    for (let i = 0; i < startDay; i++) {
        days.push(null);
    }

    for (let day = 1; day <= lastDay.getDate(); day++) {
        const date = `${currentYear.value}-${String(currentMonth.value + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

        days.push({
            day,
            date,
            plans: plans.value.filter(plan => plan.date === date),
        });
    }

    return days;
});

const selectedDate = ref(
    `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`
);

const selectedPlans = computed(() => {
    return plans.value.filter(plan => plan.date === selectedDate.value);
});

const previousMonth = () => {
    if (currentMonth.value === 0) {
        currentMonth.value = 11;
        currentYear.value--;
    } else {
        currentMonth.value--;
    }
};

const nextMonth = () => {
    if (currentMonth.value === 11) {
        currentMonth.value = 0;
        currentYear.value++;
    } else {
        currentMonth.value++;
    }
};

const selectDate = (date) => {
    selectedDate.value = date;
    form.value.date = date;
};

const addPlan = () => {
    if (!form.value.title || !form.value.date) return;

    plans.value.push({
        id: Date.now(),
        title: form.value.title,
        date: form.value.date,
        type: form.value.type,
    });

    form.value.title = '';
    form.value.type = 'General';
};

const deletePlan = (id) => {
    plans.value = plans.value.filter(plan => plan.id !== id);
};
</script>

<template>
    <AppLayout title="Calendar">
        <div class="space-y-6">
            <div class="overflow-hidden rounded-[2rem] bg-gradient-to-br from-indigo-600 via-indigo-500 to-sky-400 p-6 text-white shadow-sm sm:p-8">
                <p class="text-sm font-bold text-indigo-100">
                    Academic Planner
                </p>

                <h1 class="mt-2 text-3xl font-black tracking-tight">
                    Calendar & Plans
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-6 text-indigo-50">
                    Create plans for exams, meetings, attendance tasks, fee reminders, activities and school events.
                </p>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-[2rem] bg-white p-5 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800 lg:col-span-2">
                    <div class="flex items-center justify-between">
                        <button
                            type="button"
                            class="rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                            @click="previousMonth"
                        >
                            Previous
                        </button>

                        <h2 class="text-lg font-black text-slate-950 dark:text-white">
                            {{ monthNames[currentMonth] }} {{ currentYear }}
                        </h2>

                        <button
                            type="button"
                            class="rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                            @click="nextMonth"
                        >
                            Next
                        </button>
                    </div>

                    <div class="mt-6 grid grid-cols-7 gap-2 text-center text-xs font-black uppercase tracking-wide text-slate-400">
                        <div>Sun</div>
                        <div>Mon</div>
                        <div>Tue</div>
                        <div>Wed</div>
                        <div>Thu</div>
                        <div>Fri</div>
                        <div>Sat</div>
                    </div>

                    <div class="mt-3 grid grid-cols-7 gap-2">
                        <div
                            v-for="(item, index) in calendarDays"
                            :key="index"
                            class="min-h-24 rounded-2xl border p-2 transition"
                            :class="item
                                ? selectedDate === item.date
                                    ? 'border-indigo-300 bg-indigo-50 ring-2 ring-indigo-100 dark:border-indigo-700 dark:bg-indigo-950/40'
                                    : 'border-slate-200 bg-slate-50 hover:bg-white dark:border-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700'
                                : 'border-transparent'"
                            @click="item && selectDate(item.date)"
                        >
                            <template v-if="item">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-black text-slate-800 dark:text-white">
                                        {{ item.day }}
                                    </span>

                                    <span
                                        v-if="item.plans.length"
                                        class="rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-black text-rose-600"
                                    >
                                        {{ item.plans.length }}
                                    </span>
                                </div>

                                <div class="mt-2 space-y-1">
                                    <div
                                        v-for="plan in item.plans.slice(0, 2)"
                                        :key="plan.id"
                                        class="truncate rounded-xl bg-white px-2 py-1 text-[11px] font-bold text-indigo-600 shadow-sm dark:bg-slate-900"
                                    >
                                        {{ plan.title }}
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-[2rem] bg-white p-5 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                        <h3 class="text-lg font-black text-slate-950 dark:text-white">
                            Add Plan
                        </h3>

                        <div class="mt-5 space-y-4">
                            <div>
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">
                                    Plan title
                                </label>

                                <input
                                    v-model="form.title"
                                    type="text"
                                    placeholder="Example: Science exam"
                                    class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-indigo-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">
                                    Date
                                </label>

                                <input
                                    v-model="form.date"
                                    type="date"
                                    class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-indigo-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">
                                    Type
                                </label>

                                <select
                                    v-model="form.type"
                                    class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-indigo-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                >
                                    <option>General</option>
                                    <option>Meeting</option>
                                    <option>Exam</option>
                                    <option>Fees</option>
                                    <option>Activity</option>
                                    <option>Holiday</option>
                                </select>
                            </div>

                            <button
                                type="button"
                                class="w-full rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:shadow-none"
                                @click="addPlan"
                            >
                                Add Plan
                            </button>
                        </div>
                    </div>

                    <div class="rounded-[2rem] bg-white p-5 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                        <h3 class="text-lg font-black text-slate-950 dark:text-white">
                            Plans on {{ selectedDate }}
                        </h3>

                        <div v-if="selectedPlans.length" class="mt-4 space-y-3">
                            <div
                                v-for="plan in selectedPlans"
                                :key="plan.id"
                                class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-black text-slate-900 dark:text-white">
                                            {{ plan.title }}
                                        </p>

                                        <p class="mt-1 text-xs font-bold text-indigo-600">
                                            {{ plan.type }}
                                        </p>
                                    </div>

                                    <button
                                        type="button"
                                        class="text-xs font-bold text-rose-600 hover:text-rose-700"
                                        @click="deletePlan(plan.id)"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>

                        <p v-else class="mt-4 rounded-2xl bg-slate-50 p-4 text-sm font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                            No plans for this date.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>