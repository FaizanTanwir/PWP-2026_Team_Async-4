<template>
  <div class="p-6 max-w-xl mx-auto">

    <h1 class="text-2xl font-bold mb-4">Edit Unit</h1>

    <div class="bg-white p-6 border rounded-xl space-y-4">

      <input
        v-model="unit.name"
        class="w-full px-3 py-2 border rounded-lg"
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
import { useRoute } from 'vue-router'

const route = useRoute()
const unit = ref({ name: '' })

onMounted(async () => {
  const res = await api.get(`/units/${route.params.id}`)
  unit.value = res.data
})

const updateUnit = async () => {
  try {
    await api.put(`/units/${route.params.id}`, unit.value)
    alert('Updated!')
  } catch (err) {
    console.error(err)
  }
}
</script>