<template>
  <div class="space-y-6 animate-in fade-in duration-500">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <!-- Breadcrumb navigation -->
        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-primary mb-1">
          <router-link
            :to="`/languages/${course?.source_language_id}/courses`"
            class="hover:underline"
          >
            {{ course?.source_language?.name || 'Courses' }}
          </router-link>
          <ChevronRight class="size-3 opacity-50" />
          <span class="opacity-60">{{ course?.title || 'Loading...' }}</span>
        </div>
        
        <h1 class="text-3xl font-black tracking-tight">
          Units
        </h1>
        <p class="text-sm opacity-60">
          Organize your course content into manageable learning blocks.
        </p>
      </div>
      
      <div class="flex gap-2">
        <!-- Back button to Courses -->
        <button
          class="btn btn-ghost btn-sm"
          @click="router.back()"
        >
          <ArrowLeft class="size-4" /> Back
        </button>
        <button
          class="btn btn-primary shadow-md"
          @click="showModal = true"
        >
          <Plus class="size-5" /> New Unit
        </button>
      </div>
    </div>

    <!-- Stats summary (Optional but looks professional) -->
    <div class="stats shadow border border-base-300 w-full sm:w-auto">
      <div class="stat px-8">
        <div class="stat-title text-xs uppercase font-bold">
          Total Units
        </div>
        <div class="stat-value text-primary text-2xl">
          {{ units.length }}
        </div>
      </div>
    </div>

    <!-- Unit Table Partial -->
    <Table 
      :units="units"
      :course="course" 
      @refresh="fetchUnits" 
      @edit="openEdit" 
    />

    <!-- Unit Upsert Modal Partial -->
    <Modal 
      v-if="showModal" 
      :course-id="courseId"
      :editing-unit="selectedUnit"
      @close="closeModal" 
      @saved="fetchUnits" 
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/utils/api'
import { Plus, ArrowLeft, ChevronRight } from 'lucide-vue-next'

// Partial Imports (You'll create these next)
import Table from './partials/Table.vue'
import Modal from './partials/Modal.vue'

const route = useRoute()
const router = useRouter()

const units = ref([])
const course = ref(null)
const showModal = ref(false)
const selectedUnit = ref(null)

// The ID of the course from the route: /courses/:id/units
const courseId = computed(() => route.params.id)

const fetchCourseDetails = async () => {
  try {
    const res = await api.get(`/courses/${courseId.value}`)
    course.value = res.data
  } catch (err) {
    console.error("Failed to fetch course details", err)
  }
}

const fetchUnits = async () => {
  try {
    // Hierarchical route: GET /courses/{id}/units
    const res = await api.get(`/courses/${courseId.value}/units`)
    units.value = res.data
  } catch (err) {
    console.error("Fetch units failed", err)
  }
}

const openEdit = (unit) => {
  selectedUnit.value = unit
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedUnit.value = null
}

onMounted(() => {
  fetchCourseDetails()
  fetchUnits()
})
</script>