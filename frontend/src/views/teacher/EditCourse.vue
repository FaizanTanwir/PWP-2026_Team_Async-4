<template>
  <div class="p-6 max-w-xl mx-auto">

    <h1 class="text-2xl font-bold mb-4">Edit Course</h1>

    <div v-if="loading">Loading...</div>

    <div v-else class="space-y-4">

      <input
        v-model="title"
        placeholder="Course title"
        class="w-full px-3 py-2 border rounded"
      />

      <button
        @click="updateCourseHandler"
        class="bg-blue-600 text-white px-4 py-2 rounded"
      >
        Update Course
      </button>

    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useCourseStore } from '@/stores/courses'

const route = useRoute()
const router = useRouter()
const courseStore = useCourseStore()

const courseId = route.params.id

const title = ref('')
const loading = ref(true)

onMounted(async () => {
  const course = await courseStore.getCourseById(courseId)

  if (course) {
    title.value = course.title
  }

  loading.value = false
})

const updateCourseHandler = async () => {
  await courseStore.updateCourse(courseId, {
    title: title.value
  })

  alert('Course updated!')
  router.push('/')
}
</script>