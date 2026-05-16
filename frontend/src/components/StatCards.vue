<template>
  <div class="stats shadow bg-base-100 w-full border border-base-300">
    <div v-for="stat in stats" :key="stat.label" class="stat">
      <div class="stat-figure text-primary">
        <component :is="stat.icon" class="size-8" />
      </div>
      <div class="stat-title">{{ stat.label }}</div>
      <div class="stat-value text-primary">{{ stat.value }}</div>
      <div class="stat-desc">{{ stat.desc }}</div>
    </div>
  </div>
</template>

<script setup>
defineProps({ stats: Array });
</script>