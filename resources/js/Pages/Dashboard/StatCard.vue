<script setup>
defineProps({
  title: String,
  value: [String, Number],
})
</script>

<template>
  <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
    <p class="text-sm font-medium text-slate-500">
      {{ title }}
    </p>

    <h3 class="mt-3 text-3xl font-semibold text-slate-900">
      {{ value }}
    </h3>

    <p class="mt-2 text-xs font-medium text-emerald-600">
      +12% from last month
    </p>
  </div>
</template>