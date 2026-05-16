<template>
  <div class="space-y-6 animate-in fade-in duration-500">
    <div class="card bg-base-100 border border-base-300 shadow-sm">
      <div class="card-body flex-row items-center justify-between">
        <div>
          <h1 class="text-2xl font-black tracking-tight">
            Hello, <span class="text-primary">{{ auth.user?.name }}</span>!
          </h1>
          <p class="text-sm opacity-60">{{ currentTime }}</p>
        </div>
        <div class="hidden sm:block text-right">
          <div class="badge badge-primary badge-outline font-bold uppercase tracking-widest text-[10px]">
            {{ auth.getRole }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import dayjs from 'dayjs'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

const currentTime = ref('')

// Clock Logic
const updateClock = () => {
  currentTime.value = dayjs().format('dddd, MMMM D, YYYY · h:mm:ss A')
}

let timer
onMounted(() => {
  updateClock()
  timer = setInterval(updateClock, 1000)
})

onUnmounted(() => clearInterval(timer))

</script>