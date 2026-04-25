<template>
  <div class="p-6 max-w-2xl mx-auto">

    <h1 class="text-2xl font-bold mb-4">
      Practice Questions
    </h1>

    <div class="bg-white border rounded-lg p-6">

      <p class="mb-4 font-medium">
        What is "Hello" in Finnish?
      </p>

      <div class="space-y-2">

        <button
          v-for="option in options"
          :key="option"
          class="w-full border px-4 py-2 rounded hover:bg-gray-50"
          @click="answer(option)"
        >
          {{ option }}
        </button>

      </div>

      <p v-if="result" class="mt-4 font-semibold">
        {{ result }}
      </p>

    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'

const options = ref([
  'Hei',
  'Moii',
  'Terve',
  'Hyvästi'
])

const result = ref('')

const answer = (value) => {
  result.value =
    value === 'Hei'
      ? 'Correct ✅'
      : 'Wrong ❌'
}
</script>