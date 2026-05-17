<template>
  <dialog
    ref="modalRef"
    class="modal modal-open"
  >
    <div class="modal-box max-w-md border border-base-300 shadow-2xl">
      <form @submit.prevent="submitForm">
        <h3 class="font-black text-xl mb-6 flex items-center gap-2">
          <span
            v-if="editingCourse"
            class="text-info"
          >Update Course</span>
          <span
            v-else
            class="text-primary"
          >Create New Course</span>
        </h3>

        <div class="space-y-4">
          <!-- Course Title -->
          <div class="form-control w-full">
            <label class="label text-xs font-bold uppercase opacity-60 tracking-wider">Course Title</label>
            <input 
              v-model="form.title" 
              type="text" 
              placeholder="e.g. Basic Spanish for Beginners" 
              class="input input-bordered w-full focus:input-primary" 
              required
            >
          </div>

          <!-- Language Selection (Now shown for both Create and Edit) -->
          <div class="grid grid-cols-2 gap-4">
            <div class="form-control w-full">
              <label class="label text-xs font-bold uppercase opacity-60 tracking-wider">Source Lang</label>
              <select
                v-model="form.source_language_id"
                class="select select-bordered focus:select-primary"
                required
              >
                <option
                  disabled
                  value=""
                >
                  Select Source
                </option>
                <option
                  v-for="lang in languageStore.languagesList"
                  :key="lang.id"
                  :value="lang.id"
                >
                  {{ lang.name }}
                </option>
              </select>
            </div>
            <div class="form-control w-full">
              <label class="label text-xs font-bold uppercase opacity-60 tracking-wider">Target Lang</label>
              <select
                v-model="form.target_language_id"
                class="select select-bordered focus:select-primary"
                required
              >
                <option
                  disabled
                  value=""
                >
                  Select Target
                </option>
                <option
                  v-for="lang in languageStore.languagesList"
                  :key="lang.id"
                  :value="lang.id"
                >
                  {{ lang.name }}
                </option>
              </select>
            </div>
          </div>
        </div>

        <div class="modal-action mt-8">
          <button
            type="button"
            class="btn btn-ghost"
            @click="$emit('close')"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="btn btn-primary px-8"
            :disabled="loading"
          >
            <span
              v-if="loading"
              class="loading loading-spinner loading-xs"
            />
            {{ editingCourse ? 'Save Changes' : 'Create Course' }}
          </button>
        </div>
      </form>
    </div>
    <div
      class="modal-backdrop bg-base-900/60"
      @click="$emit('close')"
    />
  </dialog>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/utils/api'
import { useLanguageStore } from '@/stores/language'
import { useToast } from "vue-toastification";

const props = defineProps(['editingCourse', 'languageId']) // Added languageId prop from parent
const emit = defineEmits(['close', 'saved'])
const languageStore = useLanguageStore()
const route = useRoute()
const toast = useToast();

const loading = ref(false)

// Initialize form with prop data if editing, otherwise empty
const form = ref({
  title: props.editingCourse?.title || '',
  source_language_id: props.editingCourse?.source_language.id || '',
  target_language_id: props.editingCourse?.target_language.id || ''
})

onMounted(async () => {
  // Ensure we have the languages loaded for the selects
  if (languageStore.languagesList.length === 0) {
    await languageStore.getLanguages()
  }

  // DEFAULT BEHAVIOR: If creating a new course, auto-select the current language
  if (!props.editingCourse) {
    // Priority 1: Use the languageId passed via props
    // Priority 2: Use the ID from the route params if available
    const currentLangId = props.languageId || route.params.id
    if (currentLangId) {
      form.value.source_language_id = currentLangId
    }
  }
})

const submitForm = async () => {
  loading.value = true
  try {
    if (props.editingCourse) {
      // PUT (Update) - Send full form data including language changes
      await api.put(`/courses/${props.editingCourse.id}`, form.value)
      toast.success("Course updated successfully")
    } else {
      // POST (Create)
      await api.post(`/languages/${form.value.source_language_id}/courses`, form.value)
      toast.success("New course created")
    }
    emit('saved')
    emit('close')
  } catch (err) {
    console.error(err)
    const msg = err.response?.data?.message || "Operation failed"
    toast.error(msg)
  } finally {
    loading.value = false
  }
}
</script>