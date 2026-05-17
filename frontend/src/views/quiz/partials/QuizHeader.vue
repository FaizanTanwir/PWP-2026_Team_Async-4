<template>
  <div class="mb-8">
    <div class="flex justify-between items-center mb-4">
      <div>
        <h1 class="text-xl font-bold">
          Unit {{ unitId }} Quiz
        </h1>
        <p class="text-sm opacity-70">
          Progress
        </p>
      </div>
      <div class="badge badge-outline p-4 font-mono">
        {{ currentIndex + 1 }} / {{ total }}
      </div>
    </div>
    
    <progress 
      class="progress progress-primary w-full" 
      :value="currentIndex + 1" 
      :max="total"
    />
  </div>
</template>

<script setup>
defineProps(['currentIndex', 'total', 'unitId']);
</script>