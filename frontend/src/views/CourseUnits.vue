<template>
  <div class="p-6 max-w-4xl mx-auto">

    <h1 class="text-2xl font-bold mb-4">
      Course Units
    </h1>

    <!-- ONLY TEACHER CAN ADD UNIT -->
    <router-link
      v-if="role === 'TEACHER'"
      :to="`/teacher/add-unit?courseId=${courseId}`"
    >
      <button class="bg-green-600 text-white px-4 py-2 rounded mb-4">
        + Add Unit
      </button>
    </router-link>

    <!-- UNIT LIST -->
    <div class="space-y-3">

      <div
        v-for="unit in units"
        :key="unit.id"
        class="p-4 border rounded bg-white cursor-pointer hover:bg-gray-50"
        @click="openUnit(unit.id)"
      >

        <div class="flex justify-between items-center">

          <h2 class="font-semibold">
            {{ unit.title }}
          </h2>

          <!-- ONLY TEACHER CAN EDIT -->
          <router-link
            v-if="role === 'TEACHER'"
            :to="`/teacher/edit-unit/${unit.id}`"
            @click.stop
            class="text-blue-600 text-sm hover:underline"
          >
            ✏️ Edit
          </router-link>

        </div>

      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/utils/axios'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

// role detection
const role = computed(() =>
  (auth.getUser?.role || 'STUDENT').toUpperCase()
)

// course id from URL
const courseId = route.params.id

// units state
const units = ref([])

// fetch units for course
onMounted(async () => {
  const res = await api.get(`/courses/${courseId}/units`)
  units.value = res.data
})

// role-based navigation
const openUnit = (unitId) => {
  if (role.value === 'TEACHER') {
    router.push(`/teacher/add-sentence?unitId=${unitId}`)
  } else {
    router.push(`/units/${unitId}/practice`)
  }
}
</script>