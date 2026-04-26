<template>
  <div class="p-6 max-w-4xl mx-auto">

    <h1 class="text-2xl font-bold mb-4">
      Unit Sentences
    </h1>

    <!-- ADD SENTENCE -->
    <div class="bg-white border p-4 rounded-lg space-y-3">

      <input
        v-model="source"
        placeholder="Source sentence (English)"
        class="w-full px-3 py-2 border rounded"
      />

      <input
        v-model="target"
        placeholder="Target sentence (Finnish)"
        class="w-full px-3 py-2 border rounded"
      />

      <button
        @click="addSentence"
        class="bg-green-600 text-white px-4 py-2 rounded"
      >
        Add Sentence
      </button>

    </div>

    <!-- LIST -->
    <div class="mt-6 space-y-4">

      <div
        v-for="s in sentenceStore.sentencesList"
        :key="s.id"
        class="p-4 border rounded-lg bg-white flex justify-between items-start"
      >

        <!-- LEFT SIDE: SENTENCE TEXT -->
        <div>
          <p class="font-semibold">
            {{ s.text_source }}
          </p>

          <p class="text-gray-600">
            {{ s.text_target }}
          </p>
        </div>

        <!-- RIGHT SIDE: EDIT BUTTON -->
        <button
          class="text-blue-600 ml-2 text-sm hover:underline"
          @click="editSentence(s.id)"
        >
          ✏️ Edit
        </button>

      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useSentenceStore } from '@/stores/sentences'

const route = useRoute()
const router = useRouter()

const unitId = route.query.unitId

const sentenceStore = useSentenceStore()

const source = ref('')
const target = ref('')

// load sentences
onMounted(() => {
  sentenceStore.getSentencesByUnit(unitId)
})

// add sentence
const addSentence = async () => {
  if (!source.value || !target.value) return

  await sentenceStore.createSentence(unitId, {
    text_source: source.value,
    text_target: target.value,

    words: [
      {
        term: source.value,
        lemma: source.value,
        translation: target.value
      }
    ]
  })

  source.value = ''
  target.value = ''
}

// EDIT SENTENCE
const editSentence = (id) => {
  router.push(`/teacher/edit-sentence/${id}`)
}
</script>