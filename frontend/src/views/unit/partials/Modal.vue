<template>
  <dialog class="modal modal-open">
    <div class="modal-box max-w-sm border border-base-300">
      <form @submit.prevent="submitForm">
        <h3 class="font-black text-xl mb-6">
          {{ editingUnit ? 'Edit Unit' : 'Add New Unit' }}
        </h3>

        <div class="space-y-4">
          <!-- Unit Title -->
          <div class="form-control w-full">
            <label class="label text-xs font-bold uppercase opacity-60">Unit Title</label>
            <input 
              v-model="form.title" 
              type="text" 
              placeholder="e.g. Present Tense Basics" 
              class="input input-bordered w-full focus:input-primary" 
              required
            />
          </div>
        </div>

        <div class="modal-action mt-8">
          <button type="button" @click="$emit('close')" class="btn btn-ghost">Cancel</button>
          <button type="submit" class="btn btn-primary px-8" :disabled="loading">
            <span v-if="loading" class="loading loading-spinner loading-xs"></span>
            {{ editingUnit ? 'Update Unit' : 'Create Unit' }}
          </button>
        </div>
      </form>
    </div>
    <div class="modal-backdrop bg-base-900/60" @click="$emit('close')"></div>
  </dialog>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/utils/api'
import { useToast } from "vue-toastification";

const props = defineProps(['editingUnit', 'courseId'])
const emit = defineEmits(['close', 'saved'])

const toast = useToast();

const loading = ref(false)
const form = ref({
  title: props.editingUnit?.title || '',
})

const submitForm = async () => {
  loading.value = true
  try {
    if (props.editingUnit) {
      // Update: PUT /units/{id}
      await api.put(`/units/${props.editingUnit.id}`, form.value)
      toast.success("Unit updated")
    } else {
      // Create: POST /courses/{courseId}/units
      await api.post(`/courses/${props.courseId}/units`, form.value)
      toast.success("Unit created")
    }
    emit('saved')
    emit('close')
  } catch (err) {
    const msg = err.response?.data?.message || "Something went wrong"
    toast.error(msg)
  } finally {
    loading.value = false
  }
}
</script>