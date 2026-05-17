<template>
  <div class="space-y-8 animate-in fade-in duration-500">
    <!-- Greeting Card -->
    <div class="card bg-base-100 border border-base-300 shadow-sm">
      <div class="card-body flex-row items-center justify-between">
        <div>
          <h1 class="text-2xl font-black tracking-tight">
            Hello, <span class="text-primary">{{ auth.user?.name }}</span>!
          </h1>
          <p class="text-sm opacity-60">
            {{ currentTime }}
          </p>
        </div>
        <div class="badge badge-primary badge-outline font-bold uppercase tracking-widest text-[10px]">
          {{ auth.getRole }}
        </div>
      </div>
    </div>

    <!-- Language Selection Grid -->
    <section>
      <div class="flex items-center gap-2 mb-6">
        <Languages class="size-6 text-primary" />
        <h2 class="text-xl font-bold tracking-tight">
          Select a Base Language
        </h2>
      </div>

      <div
        v-if="languageStore.languagesList.length > 0"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4"
      >
        <div 
          v-for="lang in languageStore.languagesList" 
          :key="lang.id"
          class="card bg-base-100 border border-base-300 hover:border-primary hover:shadow-md transition-all cursor-pointer group"
          @click="goToLanguage(lang.id)"
        >
          <div class="card-body items-center text-center p-6">
            <div class="size-12 rounded-full bg-primary/10 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
              <span class="text-xl font-black text-primary uppercase">{{ lang.code }}</span>
            </div>
            <h3 class="font-bold text-lg">
              {{ lang.name }}
            </h3>
            <p class="text-xs opacity-50 uppercase tracking-tighter">
              Enter Curriculum
            </p>
          </div>
        </div>
      </div>

      <!-- Loading / Empty State -->
      <div
        v-else
        class="flex flex-col items-center justify-center py-12 opacity-30"
      >
        <span class="loading loading-dots loading-lg" />
        <p>Loading your languages...</p>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import dayjs from 'dayjs'
import { useAuthStore } from '@/stores/auth'
import { useLanguageStore } from '@/stores/language'
import { Languages } from 'lucide-vue-next'

const auth = useAuthStore()
const languageStore = useLanguageStore()
const router = useRouter()

const currentTime = ref('')

const updateClock = () => {
  currentTime.value = dayjs().format('dddd, MMMM D, YYYY · h:mm:ss A')
}

const goToLanguage = (id) => {
  // Journey Start: Move to the Courses page for this language
  // router.push(`/languages/${id}/courses`)
  router.push({
    name: 'Courses',
    params: { id }
  })
}

let timer
onMounted(() => {
  updateClock()
  timer = setInterval(updateClock, 1000)
  languageStore.getLanguages()
})

onUnmounted(() => clearInterval(timer))
</script>