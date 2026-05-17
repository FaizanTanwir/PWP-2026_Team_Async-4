<template>
  <div class="space-y-6 animate-in fade-in duration-500">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <!-- Visual breadcrumb feel -->
        <div class="text-xs font-bold uppercase tracking-widest text-primary mb-1">
          {{ selectedLanguageName }} Curriculum
        </div>
        <h1 class="text-3xl font-black tracking-tight">
          Courses
        </h1>
        <p class="text-sm opacity-60">
          Manage content for this language pair.
        </p>
      </div>
      
      <button
        class="btn btn-primary shadow-md"
        @click="showModal = true"
      >
        <Plus class="size-5" /> New Course
      </button>
    </div>

    <!-- Table Partial -->
    <Table 
      :courses="courses" 
      @refresh="fetchCourses" 
      @edit="openEdit" 
    />

    <!-- Upsert Modal Partial -->
    <Modal 
      v-if="showModal" 
      :is-open="showModal" 
      :editing-course="selectedCourse"
      :language-id="languageId"
      @close="closeModal" 
      @saved="fetchCourses" 
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/utils/api'
import { useLanguageStore } from '@/stores/language'
import { Plus } from 'lucide-vue-next'

// Partial Imports
import Table from './partials/Table.vue'
import Modal from './partials/Modal.vue'

const route = useRoute()
const languageStore = useLanguageStore()

const courses = ref([])
const showModal = ref(false)
const selectedCourse = ref(null)

// Extract the ID from the URL parameter :id
const languageId = computed(() => route.params.id)

// Find the language name from the store for the header
const selectedLanguageName = computed(() => {
  const lang = languageStore.languagesList.find(l => l.id == languageId.value)
  return lang ? lang.name : 'Language'
})

const fetchCourses = async () => {
  try {
    // Hits the hierarchical route: GET /languages/{id}/courses
    const res = await api.get(`/languages/${languageId.value}/courses`)
    courses.value = res.data
  } catch (err) {
    console.error("Fetch failed", err)
  }
}

const openEdit = (course) => {
  selectedCourse.value = course
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedCourse.value = null
}

onMounted(() => {
  fetchCourses()
  // Ensure store has languages for the header name lookup
  if (languageStore.languagesList.length === 0) {
    languageStore.getLanguages()
  }
})
</script>