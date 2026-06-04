<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SchoolDashboard from '@/Pages/SchoolDB.vue';
</script>

<template>
    <AppLayout title="Dashboard">
        <!-- <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard
            </h2>
        </template> -->

        <div class="py-4">
            <div class="max-w-8xl mx-auto sm:px-2 lg:px-2">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-2">
                    <SchoolDashboard />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
