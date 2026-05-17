<template>
  <div class="space-y-6 animate-in fade-in duration-500">
    <div class="flex justify-between items-center">
      <div class="flex items-center gap-4">
        <div class="p-3 bg-secondary/10 rounded-xl text-secondary">
          <History class="size-8" />
        </div>
        <div>
          <h1 class="text-2xl font-black tracking-tight">Unit Submissions</h1>
          <p class="text-sm opacity-60">Results for Unit #{{ $route.params.id }}</p>
        </div>
      </div>
      <button @click="$router.back()" class="btn btn-sm btn-ghost">Back to Units</button>
    </div>

    <div class="card bg-base-100 border border-base-300 shadow-sm overflow-hidden">
      <table class="table table-zebra w-full">
        <thead class="bg-base-200/50">
          <tr>
            <th v-if="isTeacherOrAdmin">Student</th>
            <th>Type</th>
            <th>Question</th>
            <th>Answer</th>
            <th>Accuracy</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading"><td colspan="6" class="text-center py-10"><span class="loading loading-spinner"></span></td></tr>
          <tr v-else-if="submissions.length === 0"><td colspan="6" class="text-center py-10 opacity-50">No data available.</td></tr>
          
          <tr v-for="sub in submissions" :key="sub.id" class="hover">
            <td v-if="isTeacherOrAdmin" class="font-bold text-primary">
              {{ sub.user?.name || 'Unknown' }}
            </td>
            <td>
              <div class="badge badge-outline badge-sm text-[10px] uppercase font-mono">
                {{ sub.type.replace(/_/g, ' ') }}
              </div>
            </td>
            <td class="max-w-[200px] truncate">{{ sub.question_text }}</td>
            <td class="italic">{{ sub.provided_answer }}</td>
            <td>
              <div class="flex items-center gap-2">
                <span :class="sub.accuracy * 100 >= 50 ? 'text-success' : ''" class="font-bold">
                  {{ (sub.accuracy * 100).toFixed(2) }}%
                </span>
              </div>
            </td>
            <td class="text-xs opacity-60">{{ formatDate(sub.submitted_at) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { UserRole } from '@/constants/roles'
import { History } from 'lucide-vue-next'
import dayjs from 'dayjs'
import api from '@/utils/api'

const route = useRoute()
const auth = useAuthStore()
const submissions = ref([])
const loading = ref(true)

const isTeacherOrAdmin = computed(() => 
  auth.getRole === UserRole.TEACHER || auth.getRole === UserRole.ADMIN
)

const fetchSubmissions = async () => {
  loading.value = true
  try {
    const response = await api.get(`/units/${route.params.id}/submissions`)
    submissions.value = response.data
  } catch (error) {
    console.error("Fetch error:", error)
  } finally {
    loading.value = false
  }
}

const formatDate = (date) => dayjs(date).format('DD/MM/YY HH:mm')

onMounted(fetchSubmissions)
</script>