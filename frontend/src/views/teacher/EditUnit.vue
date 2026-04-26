<template>
  <div class="p-6 max-w-xl mx-auto">

    <h1 class="text-2xl font-bold mb-4">
      Edit Unit
    </h1>

    <div v-if="loading">Loading...</div>

    <div v-else class="bg-white p-6 border rounded-xl space-y-4">

      <input
        v-model="title"
        class="w-full px-3 py-2 border rounded-lg"
        placeholder="Unit title"
      />

      <button
        @click="updateUnit"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg"
      >
        Update Unit
      </button>

    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/utils/axios'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const unitId = route.params.id

const title = ref('')
const courseId = ref(null)
const loading = ref(true)

// LOAD UNIT
onMounted(async () => {
  try {
    const res = await api.get(`/units/${unitId}`)

    title.value = res.data.title
    courseId.value = res.data.course_id

  } catch (err) {
    console.error('Failed to load unit:', err)
  } finally {
    loading.value = false
  }
})

// UPDATE UNIT
const updateUnit = async () => {
  try {
    await api.patch(`/units/${unitId}`, {
      title: title.value
    })

    alert('Unit updated successfully!')

    // go back to course units page
    router.push(`/courses/${courseId.value}/units`)

  } catch (err) {
    console.error('Update failed:', err)
    alert('Failed to update unit')
  }
}
</script>