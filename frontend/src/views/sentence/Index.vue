<template>
  <div class="space-y-6 animate-in fade-in duration-500">
    <!-- Header with Hierarchy Breadcrumbs -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-primary mb-1">
          <router-link :to="`/courses/${unit?.course_id}/units`" class="hover:underline">
            {{ unit?.course?.title || 'Units' }}
          </router-link>
          <ChevronRight class="size-3 opacity-50" />
          <span class="opacity-60">{{ unit?.title || 'Loading...' }}</span>
        </div>
        <h1 class="text-3xl font-black tracking-tight">Sentences Dictionary</h1>
        <p class="text-sm opacity-60">Manage target phrases and tokenized structural words.</p>
      </div>

      <div class="flex gap-2 w-full sm:w-auto">
        <button @click="openBulkUpload" class="btn btn-outline btn-sm">
          <Upload class="size-4" /> Bulk Upload
        </button>
        <button @click="openCreateModal" class="btn btn-primary btn-sm shadow-md">
          <Plus class="size-4" /> Add Sentence
        </button>
      </div>
    </div>

    <!-- Main Content Split Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left side: The Sentence Data Table (Takes 2 cols) -->
      <div class="lg:col-span-2">
        <Table 
          :sentences="sentences" 
          @refresh="fetchSentences" 
          @edit="openEditModal" 
        />
      </div>

      <!-- Right side: Bulk File Upload Info Panel (Takes 1 col) -->
      <div class="space-y-4">
        <div class="card bg-base-200/50 border border-base-300 shadow-sm p-5">
          <h3 class="font-bold text-sm uppercase tracking-wider mb-2 flex items-center gap-2 text-secondary">
            <Sparkles class="size-4" /> Smart Processing
          </h3>
          <p class="text-xs opacity-70 leading-relaxed">
            When you add a sentence, our system automatically segments it into individual dictionary word tokens, saves missing lemmas, and establishes dynamic mapping loops.
          </p>
        </div>
      </div>
    </div>

    <!-- Sentence Upsert Modal (Handles AI Preview & Verification) -->
    <Modal 
      v-if="showModal" 
      :unit-id="unitId"
      :editing-sentence="selectedSentence"
      @close="closeModal" 
      @saved="fetchSentences" 
    />

    <!-- Bulk Upload Modal -->
    <BulkUploadModal 
      v-if="showUploadModal" 
      :unit-id="unitId"
      @close="closeUploadModal"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/utils/api'
import { Plus, ChevronRight, Upload, Sparkles } from 'lucide-vue-next'

import Table from './partials/Table.vue'
import Modal from './partials/Modal.vue'
import BulkUploadModal from './partials/BulkUploadModal.vue'

const route = useRoute()
const unitId = computed(() => route.params.id)

const sentences = ref([])
const unit = ref(null)

const showModal = ref(false)
const showUploadModal = ref(false)
const selectedSentence = ref(null)

const fetchUnitDetails = async () => {
  try {
    const res = await api.get(`/units/${unitId.value}`)
    unit.value = res.data
  } catch (err) {
    console.error("Failed to load parent unit layout", err)
  }
}

const fetchSentences = async () => {
  try {
    const res = await api.get(`/units/${unitId.value}/sentences`)
    sentences.value = res.data
  } catch (err) {
    console.error(err)
  }
}

const openCreateModal = () => {
  selectedSentence.value = null
  showModal.value = true
}

const openEditModal = (sentence) => {
  selectedSentence.value = sentence
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedSentence.value = null
}

const openBulkUpload = () => {
  showUploadModal.value = true
}

const closeUploadModal = () => {
  showUploadModal.value = false
}

onMounted(() => {
  fetchUnitDetails()
  fetchSentences()
})
</script>