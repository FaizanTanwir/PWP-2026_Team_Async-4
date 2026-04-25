<template>
  <div class="min-h-screen bg-gray-50 p-6">

    <!-- Header -->
    <div class="max-w-7xl mx-auto flex justify-between items-center mb-8">

      <div>
        <h1 class="text-3xl font-bold text-gray-900">
          Dashboard
        </h1>

        <p class="text-gray-500 mt-1">
          Welcome {{ auth.getUser?.name || 'User' }}
        </p>
      </div>

      <button
        @click="handleLogout"
        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition"
      >
        Logout
      </button>

    </div>

    <div class="max-w-7xl mx-auto">

      <!-- TEACHER DASHBOARD -->
      <div v-if="role === 'TEACHER'">

        <div class="flex justify-between items-center mb-4">

          <h2 class="text-xl font-semibold text-gray-800">
            Teacher Panel
          </h2>

          <router-link to="/teacher/add-course">
            <button
              class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition"
            >
              + Add Course
            </button>
          </router-link>

        </div>

        <!-- Course List -->
        <div class="grid gap-4">

          <div
            v-for="course in courses"
            :key="course.id"
            class="border p-4 rounded-lg bg-white shadow-sm"
          >
            <div class="flex justify-between items-center">

              <div
                class="cursor-pointer"
                @click="openCourse(course.id)"
              >
                <h3 class="font-semibold text-gray-800">
                  {{ course.name }}
                </h3>

                <p class="text-sm text-gray-500">
                  Click to view units
                </p>
              </div>

              <router-link :to="`/teacher/edit-course/${course.id}`">
                <button class="text-blue-600 hover:text-blue-800">
                  Edit
                </button>
              </router-link>

            </div>
          </div>

        </div>

      </div>

      <!-- STUDENT DASHBOARD -->
      <div v-else-if="role === 'STUDENT'">

        <h2 class="text-xl font-semibold text-gray-800 mb-4">
          Available Courses
        </h2>

        <div class="grid gap-4">

          <div
            v-for="course in courses"
            :key="course.id"
            class="border p-4 rounded-lg bg-white shadow-sm cursor-pointer hover:bg-gray-50 transition"
            @click="openCourse(course.id)"
          >
            <h3 class="font-semibold text-gray-800">
              {{ course.name }}
            </h3>

            <p class="text-sm text-gray-500">
              Click to start learning
            </p>
          </div>

        </div>

      </div>

      <!-- ADMIN -->
      <div v-else>

        <h2 class="text-xl font-semibold text-gray-800 mb-4">
          Admin Dashboard
        </h2>

        <div class="grid md:grid-cols-2 gap-4">

          <router-link to="/admin/users">
            <div class="bg-white border rounded-lg p-5 shadow-sm hover:bg-gray-50 cursor-pointer transition">
              <h3 class="font-semibold text-gray-800">
                Manage Users
              </h3>

              <p class="text-sm text-gray-500 mt-1">
                Assign roles and manage accounts.
              </p>
            </div>
          </router-link>

          <router-link to="/teacher/add-course">
            <div class="bg-white border rounded-lg p-5 shadow-sm hover:bg-gray-50 cursor-pointer transition">
              <h3 class="font-semibold text-gray-800">
                Manage Courses
              </h3>

              <p class="text-sm text-gray-500 mt-1">
                Create and edit all courses.
              </p>
            </div>
          </router-link>

        </div>

      </div>

    </div>

  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()

const role = computed(() => (auth.getUser?.role || 'STUDENT').toUpperCase())

const courses = ref([
  { id: 1, name: 'Beginner Finnish' },
  { id: 2, name: 'Daily Finnish' },
  { id: 3, name: 'Travel Finnish' }
])

const openCourse = (id) => {
  router.push(`/courses/${id}/units`)
}

const handleLogout = async () => {
  await auth.logout()
}
</script>