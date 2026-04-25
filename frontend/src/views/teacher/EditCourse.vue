<template>
  <div class="p-6 max-w-xl mx-auto">

    <h1 class="text-2xl font-bold mb-4">Edit Course</h1>

    <div class="bg-white p-6 border rounded-xl space-y-4">

      <input
        v-model="course.name"
        type="text"
        class="w-full px-3 py-2 border rounded-lg"
      />

      <button
        @click="updateCourse"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg"
      >
        Update Course
      </button>

    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/utils/axios'
import { useRoute } from 'vue-router'

const route = useRoute()
const course = ref({ name: '' })

onMounted(async () => {
  const res = await api.get(`/courses/${route.params.id}`)
  course.value = res.data
})

const updateCourse = async () => {
  try {
    await api.put(`/courses/${route.params.id}`, course.value)
    alert('Updated!')
  } catch (err) {
    console.error(err)
  }
}
</script>