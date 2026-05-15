<template>
  <div class="space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black tracking-tight text-base-content">
          Hello, <span class="text-primary">{{ auth.user?.name }}</span>!
        </h1>
        <p class="text-base-content/60">Here is what's happening with your language learning.</p>
      </div>
      
      <div v-if="auth.getRole !== UserRole.ADMIN" class="form-control">
        <div class="join">
          <div class="join-item btn btn-md no-animation bg-base-300 border-none">Language</div>
          <select 
            v-model="selectedLanguageId" 
            class="select select-bordered join-item focus:outline-none focus:border-primary"
          >
            <option :value="null" disabled>Select to filter...</option>
            <option v-for="lang in languageStore.languagesList" :key="lang.id" :value="lang.id">
              {{ lang.name }}
            </option>
          </select>
        </div>
      </div>
    </div>


    <section>
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold flex items-center gap-2">
          <BookOpen class="size-5 text-primary" />
          {{ auth.getRole === UserRole.TEACHER ? 'Your Courses' : 'Available Courses' }}
        </h2>
        
        <router-link v-if="auth.getRole === UserRole.TEACHER" to="/teacher/add-course" class="btn btn-primary btn-sm">
          <Plus class="size-4" /> Add New Course
        </router-link>
      </div>

      <div v-if="currentCourses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="course in currentCourses" 
          :key="course.id"
          class="card bg-base-100 shadow-sm border border-base-300 hover:shadow-md transition-all group"
        >
          <div class="card-body">
            <div class="flex justify-between items-start">
              <h3 class="card-title text-lg group-hover:text-primary transition-colors cursor-pointer" @click="openCourse(course.id)">
                {{ course.title }}
              </h3>
              <router-link v-if="auth.getRole === UserRole.TEACHER" :to="`/teacher/edit-course/${course.id}`" class="btn btn-ghost btn-xs btn-square">
                <Pencil class="size-4 opacity-50 hover:opacity-100" />
              </router-link>
            </div>
            
            <p class="text-sm text-base-content/60 line-clamp-2">Click to manage units and lessons.</p>
            
            <div class="card-actions justify-end mt-4">
              <button @click="openCourse(course.id)" class="btn btn-sm btn-ghost gap-2">
                View Details <ArrowRight class="size-4" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="hero bg-base-100 rounded-box border-2 border-dashed border-base-300 py-12">
        <div class="hero-content text-center">
          <div class="max-w-md">
            <div class="bg-base-200 p-4 rounded-full inline-block mb-4">
               <Search class="size-8 opacity-20" />
            </div>
            <h3 class="text-lg font-bold opacity-60">No courses found</h3>
            <p class="text-sm opacity-50">Try selecting a different language or create a new course.</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed, ref, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useLanguageStore } from '@/stores/language'
import { UserRole } from '@/constants/roles'
import api from '@/utils/api'
import StatCards from '@/components/StatCards.vue'
import { BookOpen, Plus, Pencil, ArrowRight, Search, Activity, MessageSquare, Target, CheckCircle, TrendingUp, Flame } from 'lucide-vue-next'

const auth = useAuthStore()
const languageStore = useLanguageStore()
const router = useRouter()

const selectedLanguageId = ref(null)
const courses = ref([])

onMounted(() => {
  languageStore.getLanguages()
  fetchDashboardStats()
  fetchCourses()
})

const fetchCourses = async (langId = null) => {
  try {
    // Both URLs now point to the same smart logic in Laravel
    const url = langId ? `/languages/${langId}/courses` : '/courses';
    const res = await api.get(url);
    courses.value = res.data;
  } catch (err) {
    courses.value = [];
  }
}

watch(selectedLanguageId, (newId) => fetchCourses(newId))

const currentCourses = computed(() => courses.value)
const openCourse = (id) => router.push(`/courses/${id}/units`)
</script>