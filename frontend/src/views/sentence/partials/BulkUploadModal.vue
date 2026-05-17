<template>
  <dialog class="modal modal-open">
    <div class="modal-box max-w-md border border-base-300">
      <h3 class="font-black text-xl mb-4 text-secondary flex items-center gap-2">
        <Upload class="size-5" /> Bulk Pipeline Importer
      </h3>
      
      <p class="text-xs opacity-75 mb-4 leading-relaxed">
        Upload a structured text file containing raw target sentences <strong>(one per line)</strong>. 
        The system handles parsing, token isolation, and dictionary insertions asynchronously via background worker jobs.
      </p>

      <div class="form-control w-full border border-dashed border-base-300 bg-base-200/30 rounded-xl p-6 text-center">
        <input 
          type="file" 
          accept=".txt" 
          class="file-input file-input-bordered file-input-primary w-full" 
          @change="handleFileSelection"
        >
      </div>

      <div class="modal-action mt-6">
        <button
          class="btn btn-ghost btn-sm"
          :disabled="uploading"
          @click="$emit('close')"
        >
          Cancel
        </button>
        <button
          class="btn btn-secondary btn-sm"
          :disabled="!selectedFile || uploading"
          @click="processUpload"
        >
          <span
            v-if="uploading"
            class="loading loading-spinner loading-xs"
          />
          Dispatch Queue Pipeline
        </button>
      </div>
    </div>
    <div
      class="modal-backdrop bg-base-900/60"
      @click="$emit('close')"
    />
  </dialog>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/utils/api'
import { Upload } from 'lucide-vue-next'
import { useToast } from "vue-toastification";

const props = defineProps(['unitId'])
const emit = defineEmits(['close'])

const toast = useToast();

const selectedFile = ref(null)
const uploading = ref(false)

const handleFileSelection = (e) => {
  selectedFile.value = e.target.files[0]
}

const processUpload = async () => {
  if (!selectedFile.value) return
  
  uploading.value = true
  const formData = new FormData()
  formData.append('file', selectedFile.value)

  try {
    // Matches: POST /units/{id}/sentences/upload
    const res = await api.post(`/units/${props.unitId}/sentences/upload`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    toast.success(res.data.message || "File pipeline accepted.", { autoClose: 6000 })
    emit('close')
  } catch (err) {
    toast.error(err.response?.data?.message || "Bulk delivery failed.")
  } finally {
    uploading.value = false
  }
}
</script>