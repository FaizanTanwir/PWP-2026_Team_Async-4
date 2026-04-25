<template>
  <div class="p-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Units</h1>

      <!-- Teacher only -->
      <router-link
        v-if="role === 'TEACHER'"
        to="/teacher/add-unit"
      >
        <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg">
          + Add Unit
        </button>
      </router-link>
    </div>

    <!-- Units List -->
    <div class="grid gap-4">

      <div
        v-for="unit in units"
        :key="unit.id"
        class="p-4 border rounded-lg bg-white hover:bg-gray-50"
      >

        <div class="flex justify-between items-center">

          <!-- Unit Click -->
          <div
            class="cursor-pointer flex-1"
            @click="openUnit(unit.id)"
          >
            <h3 class="font-semibold">{{ unit.name }}</h3>

            <p class="text-sm text-gray-500">
              {{ role === 'TEACHER'
                ? 'Manage sentences'
                : 'Start practice'
              }}
            </p>
          </div>

          <!-- Teacher edit -->
          <router-link
            v-if="role === 'TEACHER'"
            :to="`/teacher/edit-unit/${unit.id}`"
          >
            <button class="text-blue-600">
              Edit
            </button>
          </router-link>

        </div>

      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

const role = computed(() => auth.getUser?.role || 'STUDENT')

const units = ref([
  { id: 1, name: 'Greetings' },
  { id: 2, name: 'Numbers' },
  { id: 3, name: 'Common Vocabulary' }
])

const openUnit = (id) => {

  if (role.value === 'TEACHER') {
    router.push(`/units/${id}/sentences`)
  } else {
    router.push(`/units/${id}/practice`)
  }

}
</script>