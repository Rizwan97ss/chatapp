<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import Sidebar from '@/Components/Sidebar.vue'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
defineProps({
    title: String,
    
});
const sidebarOpen = ref(false);
const showingNavigationDropdown = ref(false);
const mobileSearchOpen = ref(false);
const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div>

        <Head :title="title" />

        <Banner />

        <div class="min-h-screen bg-slate-100 dark:bg-slate-950">
            <Sidebar :open="sidebarOpen" @close="sidebarOpen = false" />

            <div class="lg:pl-72">
                <nav
                    class="sticky top-0 z-40 border-b border-slate-200/70 bg-white/85 shadow-sm shadow-slate-200/40 backdrop-blur-xl dark:border-slate-700/70 dark:bg-slate-900/85 dark:shadow-none">
                    <!-- Primary Navigation Menu -->
                    <div class="mx-auto max-w-8xl px-4 sm:px-6 lg:px-8">
                        <div class="flex h-16 items-center justify-between gap-4">
                            <!-- Left -->
                            <div class="flex min-w-0 items-center gap-3">
                                <!-- Mobile Sidebar Button -->
                                <button
                                    class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white p-2.5 text-slate-500 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-white lg:hidden"
                                    @click="sidebarOpen = true">
                                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>

                                <div class="hidden sm:flex">
                                    <NavLink :href="route('dashboard')" :active="route().current('dashboard')"
                                        class="rounded-xl px-4 py-2 text-sm font-semibold transition">
                                        Dashboard
                                    </NavLink>
                                </div>
                            </div>

                            <!-- Center Search -->
                            <div class="hidden flex-1 justify-center px-4 md:flex">
                                <div
                                    class="flex w-full max-w-md items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 transition focus-within:border-indigo-300 focus-within:bg-white dark:border-slate-700 dark:bg-slate-800">
                                    <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                                    </svg>

                                    <input type="text" placeholder="Search students, teachers, classes..."
                                        class="w-full border-0 bg-transparent p-0 text-sm text-slate-700 placeholder:text-slate-400 focus:ring-0 dark:text-slate-200" />
                                </div>
                            </div>

                            <!-- Right Actions -->
                            <div class="flex shrink-0 items-center justify-end gap-2">
                                <!-- Mobile Search -->
                                <button type="button"
                                    class="rounded-2xl border border-slate-200 bg-white p-2.5 text-slate-500 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 sm:hidden dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400"
                                    @click="mobileSearchOpen = !mobileSearchOpen">
                                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                                    </svg>
                                </button>

                                <!-- Desktop / Tablet Notification -->
                                <Link :href="route('notifications')"
                                    class="relative hidden rounded-2xl border border-slate-200 bg-white p-2.5 text-slate-500 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 sm:inline-flex dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">
                                    <span
                                        class="absolute right-2 top-2 size-2 rounded-full bg-rose-500 ring-2 ring-white dark:ring-slate-800"></span>

                                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a3 3 0 1 1-5.714 0" />
                                    </svg>
                                </Link>

                                <!-- Desktop / Tablet Team -->
                                <div class="hidden sm:block">
                                    <Dropdown v-if="$page.props.jetstream.hasTeamFeatures" align="right" width="60">
                                        <template #trigger>
                                            <button type="button"
                                                class="inline-flex max-w-44 items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                <span class="truncate">
                                                    {{ $page.props.auth.user.current_team.name }}
                                                </span>

                                                <svg class="size-4 shrink-0 text-slate-400" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                                </svg>
                                            </button>
                                        </template>

                                        <template #content>
                                            <div class="w-60">
                                                <div
                                                    class="block px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                    Manage Team
                                                </div>

                                                <DropdownLink
                                                    :href="route('teams.show', $page.props.auth.user.current_team)">
                                                    Team Settings
                                                </DropdownLink>

                                                <DropdownLink v-if="$page.props.jetstream.canCreateTeams"
                                                    :href="route('teams.create')">
                                                    Create New Team
                                                </DropdownLink>

                                                <template v-if="$page.props.auth.user.all_teams.length > 1">
                                                    <div class="border-t border-gray-200 dark:border-gray-600" />

                                                    <div
                                                        class="block px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                        Switch Teams
                                                    </div>

                                                    <template v-for="team in $page.props.auth.user.all_teams"
                                                        :key="team.id">
                                                        <form @submit.prevent="switchToTeam(team)">
                                                            <DropdownLink as="button">
                                                                <div class="flex items-center">
                                                                    <svg v-if="team.id == $page.props.auth.user.current_team_id"
                                                                        class="me-2 size-5 text-emerald-500"
                                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                        viewBox="0 0 24 24" stroke-width="1.5"
                                                                        stroke="currentColor">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round"
                                                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>

                                                                    <div>{{ team.name }}</div>
                                                                </div>
                                                            </DropdownLink>
                                                        </form>
                                                    </template>
                                                </template>
                                            </div>
                                        </template>
                                    </Dropdown>
                                </div>

                                <!-- Desktop / Tablet Profile -->
                                <div class="hidden sm:block">
                                    <Dropdown align="right" width="48">
                                        <template #trigger>
                                            <button type="button"
                                                class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                <span
                                                    class="flex size-8 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-sky-400 text-xs font-bold text-white">
                                                    {{ $page.props.auth.user.name.charAt(0) }}
                                                </span>

                                                <span class="hidden max-w-32 truncate md:block">
                                                    {{ $page.props.auth.user.name }}
                                                </span>

                                                <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>
                                        </template>

                                        <template #content>
                                            <div
                                                class="block px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                Manage Account
                                            </div>

                                            <DropdownLink :href="route('profile.show')">
                                                Profile
                                            </DropdownLink>

                                            <DropdownLink v-if="$page.props.jetstream.hasApiFeatures"
                                                :href="route('api-tokens.index')">
                                                API Tokens
                                            </DropdownLink>

                                            <div class="border-t border-gray-200 dark:border-gray-600" />

                                            <form @submit.prevent="logout">
                                                <DropdownLink as="button">
                                                    Log Out
                                                </DropdownLink>
                                            </form>
                                        </template>
                                    </Dropdown>
                                </div>

                                <!-- Mobile More Dropdown -->
                                <div class="sm:hidden">
                                    <Dropdown align="right" width="64">
                                        <template #trigger>
                                            <button type="button"
                                                class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white p-2.5 text-slate-500 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">
                                                <svg class="size-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 6.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM12 12.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM12 18.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" />
                                                </svg>
                                            </button>
                                        </template>

                                        <template #content>
                                            <div class="w-64">
                                                <!-- User Info -->
                                                <div class="border-b border-slate-200 px-4 py-4 dark:border-slate-700">
                                                    <div class="flex items-center gap-3">
                                                        <div
                                                            class="flex size-10 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-sky-400 text-sm font-bold text-white">
                                                            {{ $page.props.auth.user.name.charAt(0) }}
                                                        </div>

                                                        <div class="min-w-0">
                                                            <p
                                                                class="truncate text-sm font-bold text-slate-900 dark:text-white">
                                                                {{ $page.props.auth.user.name }}
                                                            </p>
                                                            <p class="truncate text-xs text-slate-500">
                                                                {{ $page.props.auth.user.email }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Notification -->
                                                <Link :href="route('notifications')"
                                                    class="flex w-full items-center justify-between px-4 py-3 text-left text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800">
                                                    <span>Notifications</span>

                                                    <span
                                                        class="rounded-full bg-rose-100 px-2 py-0.5 text-xs font-bold text-rose-600">
                                                        3
                                                    </span>
                                                </Link>

                                                <!-- Team -->
                                                <template v-if="$page.props.jetstream.hasTeamFeatures">
                                                    <div class="border-t border-slate-200 dark:border-slate-700"></div>

                                                    <div
                                                        class="px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                        Team
                                                    </div>

                                                    <DropdownLink
                                                        :href="route('teams.show', $page.props.auth.user.current_team)">
                                                        Team Settings
                                                    </DropdownLink>

                                                    <DropdownLink v-if="$page.props.jetstream.canCreateTeams"
                                                        :href="route('teams.create')">
                                                        Create New Team
                                                    </DropdownLink>
                                                </template>

                                                <!-- Account -->
                                                <div class="border-t border-slate-200 dark:border-slate-700"></div>

                                                <div
                                                    class="px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                    Account
                                                </div>

                                                <DropdownLink :href="route('profile.show')">
                                                    Profile
                                                </DropdownLink>

                                                <DropdownLink v-if="$page.props.jetstream.hasApiFeatures"
                                                    :href="route('api-tokens.index')">
                                                    API Tokens
                                                </DropdownLink>

                                                <form @submit.prevent="logout">
                                                    <DropdownLink as="button">
                                                        Log Out
                                                    </DropdownLink>
                                                </form>
                                            </div>
                                        </template>
                                    </Dropdown>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Responsive Navigation Menu -->
                    <div :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }"
                        class="border-t border-slate-200 bg-white/95 shadow-xl backdrop-blur sm:hidden dark:border-slate-700 dark:bg-slate-900/95">
                        <div class="space-y-1 px-3 pb-3 pt-3">
                            <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                Dashboard
                            </ResponsiveNavLink>
                        </div>

                        <!-- Responsive Settings Options -->
                        <div class="border-t border-slate-200 px-4 pb-4 pt-4 dark:border-slate-700">
                            <div class="flex items-center gap-3 rounded-2xl bg-slate-50 p-3 dark:bg-slate-800">
                                <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0">
                                    <img class="size-11 rounded-full object-cover"
                                        :src="$page.props.auth.user.profile_photo_url"
                                        :alt="$page.props.auth.user.name">
                                </div>

                                <div v-else
                                    class="flex size-11 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-sky-400 text-sm font-bold text-white">
                                    {{ $page.props.auth.user.name.charAt(0) }}
                                </div>

                                <div class="min-w-0">
                                    <div class="truncate text-base font-bold text-slate-900 dark:text-white">
                                        {{ $page.props.auth.user.name }}
                                    </div>
                                    <div class="truncate text-sm font-medium text-slate-500">
                                        {{ $page.props.auth.user.email }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 space-y-1">
                                <ResponsiveNavLink :href="route('profile.show')"
                                    :active="route().current('profile.show')">
                                    Profile
                                </ResponsiveNavLink>

                                <ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures"
                                    :href="route('api-tokens.index')" :active="route().current('api-tokens.index')">
                                    API Tokens
                                </ResponsiveNavLink>

                                <form method="POST" @submit.prevent="logout">
                                    <ResponsiveNavLink as="button">
                                        Log Out
                                    </ResponsiveNavLink>
                                </form>

                                <template v-if="$page.props.jetstream.hasTeamFeatures">
                                    <div class="my-3 border-t border-slate-200 dark:border-slate-700" />

                                    <div
                                        class="block px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Manage Team
                                    </div>

                                    <ResponsiveNavLink :href="route('teams.show', $page.props.auth.user.current_team)"
                                        :active="route().current('teams.show')">
                                        Team Settings
                                    </ResponsiveNavLink>

                                    <ResponsiveNavLink v-if="$page.props.jetstream.canCreateTeams"
                                        :href="route('teams.create')" :active="route().current('teams.create')">
                                        Create New Team
                                    </ResponsiveNavLink>

                                    <template v-if="$page.props.auth.user.all_teams.length > 1">
                                        <div class="my-3 border-t border-slate-200 dark:border-slate-700" />

                                        <div
                                            class="block px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            Switch Teams
                                        </div>

                                        <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                            <form @submit.prevent="switchToTeam(team)">
                                                <ResponsiveNavLink as="button">
                                                    <div class="flex items-center">
                                                        <svg v-if="team.id == $page.props.auth.user.current_team_id"
                                                            class="me-2 size-5 text-emerald-500"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <div>{{ team.name }}</div>
                                                    </div>
                                                </ResponsiveNavLink>
                                            </form>
                                        </template>
                                    </template>
                                </template>
                            </div>
                        </div>
                    </div>
                </nav>
                <div v-if="mobileSearchOpen"
                    class="border-t border-slate-200 bg-white px-4 py-3 sm:hidden dark:border-slate-700 dark:bg-slate-900">
                    <div
                        class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800">
                        <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                        </svg>

                        <input type="text" placeholder="Search..."
                            class="w-full border-0 bg-transparent p-0 text-sm text-slate-700 placeholder:text-slate-400 focus:ring-0 dark:text-slate-200" />
                    </div>
                </div>
                <!-- Page Heading -->
                <!-- <header v-if="$slots.header" class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header> -->

                <!-- Page Content -->
                <main class="p-2 sm:p-2 lg:p-2">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
