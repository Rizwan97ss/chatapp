<script setup>
// import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Pages/Dashboard/StatCard.vue'

const stats = {
  students: 1240,
  teachers: 82,
  attendance: '94%',
  pendingFees: 'AED 48,500',
}

const recentStudents = [
  { name: 'Ali Khan', class: 'Grade 8-A', status: 'Active' },
  { name: 'Sara Ahmed', class: 'Grade 6-B', status: 'Active' },
  { name: 'John Peter', class: 'Grade 10-C', status: 'Pending' },
]
</script>

<template>
  <div class="space-y-6">
    
    <!-- Header -->
    <div
      class="rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 shadow-sm ring-1 ring-slate-200 sm:p-6 lg:p-8">
      <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <p class="text-sm font-medium text-indigo-600">
            Welcome back, Admin
          </p>

          <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">
            School Dashboard
          </h1>

          <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
            Manage students, teachers, attendance, fees and daily school operations from one clean workspace.
          </p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
          <button
            class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            View Reports
          </button>

          <button
            class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-200 transition hover:-translate-y-0.5 hover:bg-slate-800">
            Add Student
          </button>
        </div>
      </div>
    </div>

    <!-- Stats -->
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <StatCard title="Total Students" :value="stats.students" />
      <StatCard title="Teachers" :value="stats.teachers" />
      <StatCard title="Today Attendance" :value="stats.attendance" />
      <StatCard title="Pending Fees" :value="stats.pendingFees" />
    </div>

    <!-- Main Grid -->
    <div class="grid gap-6 xl:grid-cols-3">
      <!-- Attendance Card -->
      <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-6 xl:col-span-2">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 class="text-lg font-bold text-slate-950">
              Attendance Overview
            </h2>
            <p class="mt-1 text-sm text-slate-500">
              Dummy weekly attendance performance
            </p>
          </div>

          <span
            class="w-fit rounded-full bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-100">
            +8.2% this week
          </span>
        </div>

        <div class="mt-8 grid h-64 grid-cols-7 items-end gap-2 sm:gap-4">
          <div v-for="(height, index) in [60, 75, 68, 90, 82, 70, 95]" :key="height"
            class="flex h-full flex-col items-center justify-end gap-3">
            <div
              class="w-full max-w-12 rounded-t-2xl bg-gradient-to-t from-indigo-500 to-sky-300 shadow-sm transition hover:scale-105"
              :style="{ height: `${height}%` }"></div>

            <div class="text-center">
              <p class="text-xs font-semibold text-slate-700">
                {{ height }}%
              </p>
              <p class="text-[11px] text-slate-400">
                Day {{ index + 1 }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Notice Board -->
      <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-lg font-bold text-slate-950">
              Notice Board
            </h2>
            <p class="mt-1 text-sm text-slate-500">
              Latest school updates
            </p>
          </div>

          <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
            2 New
          </span>
        </div>

        <div class="mt-5 space-y-4">
          <div class="rounded-2xl bg-gradient-to-br from-amber-50 to-white p-4 ring-1 ring-amber-100">
            <p class="text-sm font-bold text-slate-900">
              Parent meeting
            </p>
            <p class="mt-1 text-xs leading-5 text-slate-500">
              Scheduled for Friday at 10:00 AM.
            </p>
          </div>

          <div class="rounded-2xl bg-gradient-to-br from-violet-50 to-white p-4 ring-1 ring-violet-100">
            <p class="text-sm font-bold text-slate-900">
              Exam timetable
            </p>
            <p class="mt-1 text-xs leading-5 text-slate-500">
              Final exams start next month.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Students -->
    <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
      <div
        class="flex flex-col gap-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
        <div>
          <h2 class="text-lg font-bold text-slate-950">
            Recent Students
          </h2>
          <p class="mt-1 text-sm text-slate-500">
            Latest dummy student records
          </p>
        </div>

        <button
          class="w-fit rounded-xl bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-100">
          View All
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[640px] text-left text-sm">
          <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
            <tr>
              <th class="px-5 py-4 sm:px-6">Name</th>
              <th class="px-5 py-4 sm:px-6">Class</th>
              <th class="px-5 py-4 sm:px-6">Status</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            <tr v-for="student in recentStudents" :key="student.name" class="transition hover:bg-indigo-50/40">
              <td class="px-5 py-4 sm:px-6">
                <div class="flex items-center gap-3">
                  <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-indigo-100 to-sky-100 text-sm font-bold text-indigo-700">
                    {{ student.name.charAt(0) }}
                  </div>

                  <div>
                    <p class="font-semibold text-slate-900">
                      {{ student.name }}
                    </p>
                    <p class="text-xs text-slate-400">
                      Student profile
                    </p>
                  </div>
                </div>
              </td>

              <td class="px-5 py-4 font-medium text-slate-500 sm:px-6">
                {{ student.class }}
              </td>

              <td class="px-5 py-4 sm:px-6">
                <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="student.status === 'Active'
                    ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100'
                    : 'bg-amber-50 text-amber-700 ring-1 ring-amber-100'
                  ">
                  {{ student.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>