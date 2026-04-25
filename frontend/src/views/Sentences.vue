<template>
  <div class="p-6 max-w-4xl mx-auto">

    <h1 class="text-2xl font-bold mb-4">
      Unit Sentences
    </h1>

    <!-- ADD SENTENCE -->
    <div class="bg-white border p-4 rounded-lg space-y-3">

      <input
        v-model="english"
        placeholder="English sentence"
        class="w-full px-3 py-2 border rounded"
      />

      <input
        v-model="finnish"
        placeholder="Finnish translation"
        class="w-full px-3 py-2 border rounded"
      />

      <button
        @click="addSentence"
        class="bg-green-600 text-white px-4 py-2 rounded"
      >
        Add Sentence
      </button>

    </div>

    <!-- SENTENCE LIST -->
    <div class="mt-6 space-y-4">

      <div
        v-for="(s, index) in sentences"
        :key="index"
        class="p-4 border rounded-lg bg-white"
      >

        <p class="font-semibold">
          {{ s.english }}
        </p>

        <p class="text-gray-600">
          {{ s.finnish }}
        </p>

        <!-- WORD BREAKDOWN -->
        <div class="mt-2 flex flex-wrap gap-2">
          <span
            v-for="(w, i) in s.finnish.split(' ')"
            :key="i"
            class="px-2 py-1 bg-gray-100 rounded text-sm"
          >
            {{ w }}
          </span>
        </div>

      </div>

    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'

const english = ref('')
const finnish = ref('')

const sentences = ref([
  {
    english: 'Hello',
    finnish: 'Hei'
  },
  {
    english: 'Good morning',
    finnish: 'Hyvää huomenta'
  }
])

const addSentence = () => {

  if (!english.value || !finnish.value) return

  sentences.value.push({
    english: english.value,
    finnish: finnish.value
  })

  english.value = ''
  finnish.value = ''
}
</script>