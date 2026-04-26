<template>
  <div class="p-6 max-w-xl mx-auto">

    <h1 class="text-2xl font-bold mb-4">Add Course</h1>

    <div class="bg-white p-6 border rounded-xl space-y-4">

      <!-- Course Name -->
      <input
        v-model="name"
        type="text"
        placeholder="Course name"
        class="w-full px-3 py-2 border rounded-lg"
      />

      <!-- Source Language -->
      <select
        v-model="sourceLanguageId"
        class="w-full px-3 py-2 border rounded-lg"
      >
        <option value="" disabled>Select source language</option>
        <option
          v-for="language in languageStore.languagesList"
          :key="language.id"
          :value="language.id"
        >
          {{ language.name }}
        </option>
      </select>

      <!-- Target Language -->
      <select
        v-model="targetLanguageId"
        class="w-full px-3 py-2 border rounded-lg"
      >
        <option value="" disabled>Select target language</option>
        <option
          v-for="language in languageStore.languagesList"
          :key="language.id"
          :value="language.id"
        >
          {{ language.name }}
        </option>
      </select>

      <!-- Submit Button -->
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
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

import { useLanguageStore } from '@/stores/language'
import { useCourseStore } from '@/stores/courses'  

const router = useRouter()

const languageStore = useLanguageStore()
const courseStore = useCourseStore()

const name = ref('')
const sourceLanguageId = ref(null)
const targetLanguageId = ref(null)

const createCourse = async () => {
  try {
    const newCourse = await courseStore.createCourse(
      {
        title: name.value,
        target_language_id: targetLanguageId.value
      },
      sourceLanguageId.value
    )

    alert('Course created!')

    // reset form
    name.value = ''
    sourceLanguageId.value = null
    targetLanguageId.value = null

    // 🔥 MOVE TO ADD UNIT PAGE (THIS IS THE KEY LINE)
    router.push(`/teacher/add-unit?courseId=${newCourse.id}`)

  } catch (err) {
    console.error(err)
    alert('Failed to create course')
  }
}

onMounted(async () => {
  await languageStore.getLanguages()
})
</script>