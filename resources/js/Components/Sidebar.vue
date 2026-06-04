<script setup>
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'

defineProps({
  open: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['close'])

const menuSections = [
  {
    title: 'Main',
    items: [
      { name: 'Dashboard', routeName: 'dashboard', href: '/dashboard', icon: 'dashboard' },
    ],
  },
  {
    title: 'Academic',
    items: [
      { name: 'Admissions', routeName: 'admissions', href: '/admissions', icon: 'admissions' },
      { name: 'Students', routeName: 'students', href: '/students', icon: 'students' },
      { name: 'Teachers', routeName: 'teachers', href: '/teachers', icon: 'teachers' },
      { name: 'Classes', routeName: 'classes', href: '/classes', icon: 'classes' },
      { name: 'Subjects', routeName: 'subjects', href: '/subjects', icon: 'subjects' },
      { name: 'Attendance', routeName: 'attendance', href: '/attendance', icon: 'attendance' },
      { name: 'Exams', routeName: 'exams', href: '/exams', icon: 'exams' },
      { name: 'Results', routeName: 'results', href: '/results', icon: 'results' },
    ],
  },
  {
    title: 'Management',
    items: [
      { name: 'Fees', routeName: 'fees', href: '/fees', icon: 'fees' },
      { name: 'Library', routeName: 'library', href: '/library', icon: 'library' },
      { name: 'Activities', routeName: 'activities', href: '/activities', icon: 'activities' },
      { name: 'Staffs', routeName: 'staff', href: '/staff', icon: 'staff' },
      { name: 'Parents', routeName: 'parents', href: '/parents', icon: 'parents' },
      { name: 'Hostel', routeName: 'hostels', href: '/hostel', icon: 'hostel' },
      { name: 'Transport', routeName: 'transport', href: '/transport', icon: 'transport' },
    ],
  },
  {
    title: 'System',
    items: [
      { name: 'Reports', routeName: 'reports', href: '/reports', icon: 'reports' },
      { name: 'Administration', routeName: 'administration', href: '/administration', icon: 'admin' },
      { name: 'Settings', routeName: 'settings', href: '/settings', icon: 'settings' },
    ],
  },
]

const isActive = (item) => {
  return route().current(item.routeName)
}

const logout = () => {
  router.post(route('logout'))
}
</script>

<template>
  <!-- Mobile Overlay -->
  <div v-if="open" class="fixed inset-0 z-40 bg-slate-950/50 backdrop-blur-sm lg:hidden" @click="emit('close')" />

  <!-- Sidebar -->
  <aside
    class="fixed inset-y-0 left-0 z-50 flex w-72 transform flex-col border-r border-slate-200 bg-white shadow-2xl transition-transform duration-300 ease-in-out dark:border-slate-800 dark:bg-slate-950 lg:translate-x-0 lg:shadow-none"
    :class="open ? 'translate-x-0' : '-translate-x-full'">
    <!-- Logo -->
    <div class="flex h-16 items-center justify-between border-b border-slate-200 px-5 dark:border-slate-800">
      <Link :href="route('dashboard')" class="flex items-center gap-3">
        <div
          class="flex size-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-sky-400 text-sm font-black text-white shadow-lg shadow-indigo-200 dark:shadow-none">
          S
        </div>

        <div>
          <h1 class="text-lg font-black tracking-tight text-slate-950 dark:text-white">
            SchoolPro
          </h1>
          <p class="-mt-1 text-xs font-semibold text-slate-400">
            Management
          </p>
        </div>
      </Link>

      <button type="button"
        class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-white lg:hidden"
        @click="emit('close')">
        ✕
      </button>
    </div>

    <!-- User Card -->
    <div class="p-4">
      <div
        class="rounded-3xl bg-gradient-to-br from-indigo-600 to-sky-400 p-4 text-white shadow-lg shadow-indigo-100 dark:shadow-none">
        <div class="flex items-center gap-3">
          <div
            class="flex size-11 items-center justify-center rounded-2xl bg-white/20 text-sm font-black backdrop-blur">
            {{ $page.props.auth.user.name.charAt(0) }}
          </div>

          <div class="min-w-0">
            <p class="truncate text-sm font-black">
              {{ $page.props.auth.user.name }}
            </p>
            <p class="truncate text-xs font-semibold text-indigo-100">
              {{ $page.props.auth.user.email }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Menu -->
    <nav class="flex-1 overflow-y-auto px-4 pb-4">
      <div v-for="section in menuSections" :key="section.title" class="mb-5">
        <p class="mb-2 px-3 text-[11px] font-black uppercase tracking-widest text-slate-400">
          {{ section.title }}
        </p>

        <div class="space-y-1">
          <Link v-for="item in section.items" :key="item.name" :href="item.href"
            class="group flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-bold transition"
            :class="isActive(item)
              ? 'bg-indigo-50 text-indigo-700 shadow-sm ring-1 ring-indigo-100 dark:bg-indigo-950/40 dark:text-indigo-300 dark:ring-indigo-900/40'
              : 'text-slate-500 hover:bg-slate-50 hover:text-slate-950 dark:text-slate-400 dark:hover:bg-slate-900 dark:hover:text-white'" @click="emit('close')">
            <span class="flex size-9 shrink-0 items-center justify-center rounded-xl transition"
              :class="isActive(item)
                ? 'bg-gradient-to-br from-indigo-500 to-sky-400 text-white shadow-md shadow-indigo-100 dark:shadow-none'
                : 'bg-slate-100 text-slate-500 group-hover:bg-white group-hover:text-indigo-600 dark:bg-slate-900 dark:text-slate-400 dark:group-hover:bg-slate-800'">
              <!-- Dashboard -->
              <svg v-if="item.icon === 'dashboard'" class="size-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M3.75 13.5h6.75v6.75H3.75V13.5Zm9.75 0h6.75v6.75H13.5V13.5ZM3.75 3.75h6.75v6.75H3.75V3.75Zm9.75 0h6.75v6.75H13.5V3.75Z" />
              </svg>
              <svg v-else-if="item.icon === 'admissions'" class="size-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M7.5 3.75h9l3 3v13.5H4.5V3.75h3Zm9 0v3h3M8.25 10.5h7.5M8.25 14.25h4.5M8.25 18h7.5" />
              </svg>
              <!-- Students -->
              <svg v-else-if="item.icon === 'students'" class="size-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" />
              </svg>

              <!-- Teachers -->
              <svg v-else-if="item.icon === 'teachers'" class="size-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M12 6.75a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM5.25 21a6.75 6.75 0 0 1 13.5 0M3.75 8.25h16.5M6 8.25V18m12-9.75V18" />
              </svg>

              <!-- Classes -->
              <svg v-else-if="item.icon === 'classes'" class="size-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M4.5 5.25h15v13.5h-15V5.25ZM8.25 9h7.5M8.25 12h7.5M8.25 15h4.5" />
              </svg>

              <!-- Subjects -->
              <svg v-else-if="item.icon === 'subjects'" class="size-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M12 6.75c-2.25-1.5-4.5-1.5-6.75-.75v12c2.25-.75 4.5-.75 6.75.75m0-12c2.25-1.5 4.5-1.5 6.75-.75v12c-2.25-.75-4.5-.75-6.75.75m0-12v12" />
              </svg>

              <!-- Attendance -->
              <svg v-else-if="item.icon === 'attendance'" class="size-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M9 12.75 11.25 15 15.75 9.75M4.5 6.75h15M6.75 3.75v3M17.25 3.75v3M5.25 6.75h13.5v13.5H5.25V6.75Z" />
              </svg>
              <!-- Exams -->
              <svg v-else-if="item.icon === 'exams'" class="size-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M7.5 3.75h9l3 3v13.5H4.5V3.75h3Zm9 0v3h3M8.25 10.5h7.5M8.25 14.25h4.5M8.25 18h7.5" />
              </svg>

              <!-- Results -->
              <svg v-else-if="item.icon === 'results'" class="size-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M4.5 19.5h15M7.5 16.5v-6M12 16.5v-9M16.5 16.5v-3M6.75 4.5h10.5A2.25 2.25 0 0 1 19.5 6.75v12.75h-15V6.75A2.25 2.25 0 0 1 6.75 4.5Z" />
              </svg>
              <!-- Fees -->
              <svg v-else-if="item.icon === 'fees'" class="size-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M3.75 7.5h16.5v9H3.75v-9ZM6.75 15h3M14.25 12a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z" />
              </svg>

              <!-- Library -->
              <svg v-else-if="item.icon === 'library'" class="size-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M5.25 4.5h3.75v15H5.25v-15Zm5.25 0h3.75v15H10.5v-15Zm5.25 1.5 3.75-1.5v15l-3.75 1.5V6Z" />
              </svg>

              <!-- Activities -->
              <svg v-else-if="item.icon === 'activities'" class="size-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M4.5 12h3l2.25-6 4.5 12 2.25-6h3" />
              </svg>

              <!-- Staff -->
              <svg v-else-if="item.icon === 'staff'" class="size-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M8.25 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm7.5 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.75 20.25a5.25 5.25 0 0 1 9-3.75 5.25 5.25 0 0 1 7.5 3.75" />
              </svg>

              <!-- Parents -->
              <svg v-else-if="item.icon === 'parents'" class="size-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M8.25 9.75a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm7.5 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM4.5 20.25a5.25 5.25 0 0 1 10.5 0m-4.5-3a5.25 5.25 0 0 1 9 3" />
              </svg>

              <!-- Hostel -->
              <svg v-else-if="item.icon === 'hostel'" class="size-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M4.5 20.25V7.5l7.5-4.5 7.5 4.5v12.75M8.25 20.25v-6h7.5v6M8.25 10.5h.01M15.75 10.5h.01" />
              </svg>

              <!-- Transport -->
              <svg v-else-if="item.icon === 'transport'" class="size-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M5.25 16.5h13.5M6.75 16.5v2.25m10.5-2.25v2.25M4.5 12.75V7.5A2.25 2.25 0 0 1 6.75 5.25h10.5A2.25 2.25 0 0 1 19.5 7.5v5.25A2.25 2.25 0 0 1 17.25 15H6.75A2.25 2.25 0 0 1 4.5 12.75Zm3-3h9" />
              </svg>

              <!-- Reports -->
              <svg v-else-if="item.icon === 'reports'" class="size-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M6.75 3.75h10.5A2.25 2.25 0 0 1 19.5 6v14.25h-15V6A2.25 2.25 0 0 1 6.75 3.75ZM8.25 8.25h7.5M8.25 12h7.5M8.25 15.75h4.5" />
              </svg>

              <!-- Administration -->
              <svg v-else-if="item.icon === 'admin'" class="size-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M12 3.75 4.5 7.5 12 11.25 19.5 7.5 12 3.75Zm-6 6v5.25c0 2.25 2.5 4.25 6 5.25 3.5-1 6-3 6-5.25V9.75" />
              </svg>

              <!-- Settings -->
              <svg v-else-if="item.icon === 'settings'" class="size-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M12 15.75a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5Zm7.5-3.75c0-.5-.05-.98-.15-1.45l2.15-1.65-2.25-3.9-2.55 1.05a7.8 7.8 0 0 0-2.5-1.45L13.8 2.25h-3.6l-.4 2.4a7.8 7.8 0 0 0-2.5 1.45L4.75 5.05 2.5 8.95l2.15 1.65A7.2 7.2 0 0 0 4.5 12c0 .5.05.98.15 1.45L2.5 15.1l2.25 3.9 2.55-1.05a7.8 7.8 0 0 0 2.5 1.45l.4 2.4h3.6l.4-2.4a7.8 7.8 0 0 0 2.5-1.45l2.55 1.05 2.25-3.9-2.15-1.65c.1-.47.15-.95.15-1.45Z" />
              </svg>
            </span>

            <span class="flex-1">
              {{ item.name }}
            </span>

            <span v-if="isActive(item)" class="size-2 rounded-full bg-indigo-500"></span>
          </Link>
        </div>
      </div>
    </nav>

    <!-- Bottom -->
    <div class="border-t border-slate-200 p-4 dark:border-slate-800">
      <div
        class="mb-3 rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-4 ring-1 ring-slate-200 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 dark:ring-slate-700">
        <p class="text-sm font-black text-slate-950 dark:text-white">
          Academic Year
        </p>

        <p class="mt-1 text-xs font-semibold text-slate-500">
          2026 - 2027 session active
        </p>

        <Link :href="route('calendar')"
          class="mt-4 flex w-full items-center justify-center gap-2 rounded-2xl bg-slate-950 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950">
          <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
              d="M6.75 3.75v3m10.5-3v3M4.5 8.25h15m-13.5-3h12A1.5 1.5 0 0 1 19.5 6.75v12A1.5 1.5 0 0 1 18 20.25H6A1.5 1.5 0 0 1 4.5 18.75v-12A1.5 1.5 0 0 1 6 5.25Z" />
          </svg>

          View Calendar
        </Link>
      </div>

      <button type="button"
        class="flex w-full items-center justify-center gap-2 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-black text-rose-600 transition hover:bg-rose-100 dark:bg-rose-950/30 dark:text-rose-300 dark:hover:bg-rose-950/50"
        @click="logout">
        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
            d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 12h8.25m0 0-3-3m3 3-3 3" />
        </svg>

        Log Out
      </button>
    </div>
  </aside>
</template>