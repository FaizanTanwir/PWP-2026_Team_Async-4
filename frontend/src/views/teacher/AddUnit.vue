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
        @click="createUnitHandler"
        class="bg-green-600 text-white px-4 py-2 rounded-lg"
      >
        Create Unit
      </button>

    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useUnitStore } from '@/stores/units'

const route = useRoute()
const router = useRouter()
const unitStore = useUnitStore()

const name = ref('')

// ✅ SAFE courseId
const courseId = computed(() => route.query.courseId)

const createUnitHandler = async () => {

  if (!courseId.value) {
    alert("Course ID missing. Navigation broken.")
    return
  }

  try {
    const newUnit = await unitStore.createUnit(courseId.value, {
      title: name.value
    })

    alert('Unit created!')

    name.value = ''

    // next step
    router.push(`/teacher/add-sentence?unitId=${newUnit.id}`)

  } catch (err) {
    console.error(err)
    alert('Failed to create unit')
  }
}
</script>