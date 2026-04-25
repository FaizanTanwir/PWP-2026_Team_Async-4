<template>
  <div class="p-6 max-w-xl mx-auto">

    <h1 class="text-2xl font-bold mb-4">Add Unit</h1>

    <div class="bg-white p-6 border rounded-xl space-y-4">

      <input
        v-model="name"
        type="text"
        placeholder="Unit name"
        class="w-full px-3 py-2 border rounded-lg"
      />

      <button
        @click="createUnit"
        class="bg-green-600 text-white px-4 py-2 rounded-lg"
      >
        Create Unit
      </button>

    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/utils/axios'
import { useRoute } from 'vue-router'

const route = useRoute()
const name = ref('')

const createUnit = async () => {
  try {
    await api.post('/units', {
      name: name.value,
      course_id: route.query.course_id
    })

    alert('Unit created!')
    name.value = ''

  } catch (err) {
    console.error(err)
    alert('Failed to create unit')
  }
}
</script>