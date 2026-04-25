<template>
  <div class="p-6 max-w-xl mx-auto">

    <h1 class="text-2xl font-bold mb-4">Add Course</h1>

    <div class="bg-white p-6 border rounded-xl space-y-4">

      <input
        v-model="name"
        type="text"
        placeholder="Course name"
        class="w-full px-3 py-2 border rounded-lg"
      />

      <button
        @click="createCourse"
        class="bg-green-600 text-white px-4 py-2 rounded-lg"
      >
        Create Course
      </button>

    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/utils/axios'

const name = ref('')

const createCourse = async () => {
  try {
    await api.post('/courses', {
      name: name.value
    })

    alert('Course created!')
    name.value = ''

  } catch (err) {
    console.error(err)
    alert('Failed to create course')
  }
}
</script>