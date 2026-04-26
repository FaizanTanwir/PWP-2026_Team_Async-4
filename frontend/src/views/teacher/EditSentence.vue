<template>
  <div class="p-6 max-w-xl mx-auto">

    <h1 class="text-2xl font-bold mb-4">Edit Sentence</h1>

    <div v-if="loading">Loading...</div>

    <div v-else class="space-y-4">

      <!-- Source sentence -->
      <input
        v-model="text_source"
        class="w-full px-3 py-2 border rounded"
        placeholder="Source text"
      />

      <!-- Target sentence -->
      <input
        v-model="text_target"
        class="w-full px-3 py-2 border rounded"
        placeholder="Target text"
      />

      <!-- Update button -->
      <button
        @click="updateHandler"
        class="bg-blue-600 text-white px-4 py-2 rounded"
      >
        Update Sentence
      </button>

    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useSentenceStore } from '@/stores/sentences'
import api from '@/utils/axios'

const route = useRoute()
const router = useRouter()
const sentenceStore = useSentenceStore()

const sentenceId = route.params.id

const text_source = ref('')
const text_target = ref('')
const loading = ref(true)

let existingWords = ref([])

// 🔹 Load sentence
onMounted(async () => {
  try {
    const res = await api.get(`/sentences/${sentenceId}`)

    text_source.value = res.data.text_source
    text_target.value = res.data.text_target

    // keep existing words if backend sends them
    existingWords.value = res.data.words || []

  } catch (err) {
    console.error('Failed to load sentence:', err)
  } finally {
    loading.value = false
  }
})

// 🔹 Update sentence
const updateHandler = async () => {
  try {

    // 🧠 ensure words ALWAYS exist (backend requirement fix)
    const wordsPayload =
      existingWords.value.length > 0
        ? existingWords.value
        : [
            {
              term: text_source.value.split(' ')[0] || '',
              lemma: text_source.value.split(' ')[0] || '',
              translation: text_target.value.split(' ')[0] || ''
            }
          ]

    await sentenceStore.updateSentence(sentenceId, {
      text_source: text_source.value,
      text_target: text_target.value,
      words: wordsPayload
    })

    alert('Sentence updated successfully!')
    router.back()

  } catch (err) {
    console.error('Update failed:', err)
    alert('Failed to update sentence')
  }
}
</script>