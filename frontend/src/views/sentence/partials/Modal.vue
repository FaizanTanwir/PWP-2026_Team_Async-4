<template>
  <dialog class="modal modal-open">
    <div class="modal-box max-w-2xl border border-base-300 shadow-2xl">
      <form @submit.prevent="submitForm">
        <h3 class="font-black text-xl mb-4 text-primary">
          {{ editingSentence ? 'Modify Sentence Blueprint' : 'Build Learning Sentence' }}
        </h3>

        <div class="space-y-4">
          <!-- Live Preview Generator Hook Box -->
          <div
            v-if="!editingSentence"
            class="p-4 bg-primary/5 rounded-xl border border-primary/20 flex gap-3 items-end"
          >
            <div class="form-control w-full">
              <label class="label text-xs font-bold uppercase text-primary opacity-80">AI Automation Input Tracker</label>
              <input 
                v-model="previewText" 
                type="text" 
                placeholder="Type learning sentence here (e.g., 'Moi, miten menee?')" 
                class="input input-bordered w-full focus:input-primary"
              >
            </div>
            <button
              type="button"
              class="btn btn-primary"
              :disabled="previewLoading"
              @click="runAutomatedPreview"
            >
              <span
                v-if="previewLoading"
                class="loading loading-spinner loading-xs"
              />
              <Wand2
                v-else
                class="size-4"
              /> Preview
            </button>
          </div>

          <!-- Target Language Output Block -->
          <div class="form-control w-full">
            <label class="label text-xs font-bold uppercase opacity-60">Target Text (Learning Phrase)</label>
            <input
              v-model="form.text_target"
              type="text"
              class="input input-bordered w-full"
              required
            >
          </div>

          <!-- Source Language Input Block -->
          <div class="form-control w-full">
            <label class="label text-xs font-bold uppercase opacity-60">Source Text (Translation)</label>
            <input
              v-model="form.text_source"
              type="text"
              class="input input-bordered w-full"
              required
            >
          </div>

          <!-- Tokens Synchronization Dynamic Sub-Forms List -->
          <div class="divider text-xs uppercase opacity-50 font-bold tracking-widest">
            Tokenized Structural Words Map
          </div>
          
          <div class="max-h-56 overflow-y-auto space-y-2 pr-1">
            <div
              v-for="(word, idx) in form.words"
              :key="idx"
              class="grid grid-cols-3 gap-2 bg-base-200 p-2 rounded-lg relative items-center"
            >
              <div>
                <input
                  v-model="word.term"
                  type="text"
                  placeholder="Term"
                  class="input input-sm input-bordered w-full"
                  required
                >
              </div>
              <div>
                <input
                  v-model="word.translation"
                  type="text"
                  placeholder="Translation"
                  class="input input-sm input-bordered w-full"
                >
              </div>
              <div class="flex items-center gap-1">
                <input
                  v-model="word.lemma"
                  type="text"
                  placeholder="Lemma (Base form)"
                  class="input input-sm input-bordered w-full"
                >
                <button
                  type="button"
                  class="btn btn-square btn-ghost btn-xs text-error"
                  @click="removeWordToken(idx)"
                >
                  <X class="size-4" />
                </button>
              </div>
            </div>
          </div>
          
          <button
            type="button"
            class="btn btn-outline btn-xs gap-1 mt-2"
            @click="addEmptyWordToken"
          >
            <Plus class="size-3" /> Insert Extra Word Variant Token
          </button>
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
            Save Blueprint
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
import { ref } from 'vue'
import { Wand2, Plus, X } from 'lucide-vue-next'
import api from '@/utils/api'
import { useToast } from "vue-toastification";

const props = defineProps(['editingSentence', 'unitId'])
const emit = defineEmits(['close', 'saved'])

const toast = useToast();

const loading = ref(false)
const previewLoading = ref(false)
const previewText = ref('')

const form = ref({
  text_target: props.editingSentence?.text_target || '',
  text_source: props.editingSentence?.text_source || '',
  words: props.editingSentence?.words ? JSON.parse(JSON.stringify(props.editingSentence.words)) : []
})

const runAutomatedPreview = async () => {
  if (!previewText.value.trim()) return toast.warning("Insert some target string tracking context.")
  previewLoading.value = true
  try {
    const res = await api.post(`/units/${props.unitId}/sentences/preview`, { text: previewText.value })
    form.value.text_target = res.data.sentence.text_target
    form.value.text_source = res.data.sentence.text_source
    form.value.words = res.data.words
    toast.success("AI translation and token extraction complete.")
  } catch (err) {
    toast.error("Preview parser automation breakdown.")
    console.log(err)
  } finally {
    previewLoading.value = false
  }
}

const addEmptyWordToken = () => {
  form.value.words.push({ term: '', translation: '', lemma: '' })
}

const removeWordToken = (idx) => {
  form.value.words.splice(idx, 1)
}

const submitForm = async () => {
  if (form.value.words.length === 0) {
    return toast.error("You must register at least 1 relational map word validation token.")
  }
  loading.value = true
  try {
    if (props.editingSentence) {
      await api.put(`/sentences/${props.editingSentence.id}`, form.value)
      toast.success("Sentence modification synchronized mapping saved.")
    } else {
      await api.post(`/units/${props.unitId}/sentences`, form.value)
      toast.success("New structural language unit trace built.")
    }
    emit('saved')
    emit('close')
  } catch (err) {
    toast.error(err.response?.data?.message || "Execution engine anomaly.")
  } finally {
    loading.value = false
  }
}
</script>