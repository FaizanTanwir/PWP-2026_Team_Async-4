<template>
  <div class="min-h-screen bg-gray-50 p-6">

    <!-- HEADER -->
    <div class="max-w-7xl mx-auto flex justify-between items-center mb-8">

      <div>
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-500 mt-1">
          Welcome {{ auth.getUser?.name || 'User' }}
        </p>
      </div>

      <button
        @click="handleLogout"
        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg"
      >
        Logout
      </button>

    </div>

    <div class="max-w-7xl mx-auto">

      <!-- ================= TEACHER ================= -->
      <div v-if="role === 'TEACHER'">

        <!-- LANGUAGE SELECTOR -->
        <div class="mb-6">

          <label class="block text-sm font-medium mb-2">
            Select Language
          </label>

          <select
            v-model="selectedLanguageId"
            class="border px-3 py-2 rounded-lg w-full max-w-xs"
          >
            <option value="" disabled>Select language</option>

            <option
              v-for="lang in languageStore.languagesList"
              :key="lang.id"
              :value="lang.id"
            >
              {{ lang.name }}
            </option>
          </select>

        </div>

        <div class="flex justify-between items-center mb-4">

          <h2 class="text-xl font-semibold">Teacher Panel</h2>

          <router-link to="/teacher/add-course">
            <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg">
              + Add Course
            </button>
          </router-link>

        </div>

        <!-- COURSES -->
        <div class="grid gap-4">

          <div
            v-for="course in teacherCourses"
            :key="course.id"
            class="border p-4 bg-white rounded hover:bg-gray-50 cursor-pointer"
            @click="openCourse(course.id)"
          >

            <div class="flex justify-between">

              <div>
                <h3 class="font-semibold">{{ course.title }}</h3>
                <p class="text-sm text-gray-500">Click to view units</p>
              </div>

              <router-link :to="`/teacher/edit-course/${course.id}`">
                <button @click.stop class="text-blue-600 text-sm">
                  ✏️ Edit
                </button>
              </router-link>

            </div>

          </div>

        </div>

        <p v-if="selectedLanguageId && teacherCourses.length === 0"
           class="text-gray-500 mt-4">
          No courses found for this language
        </p>

      </div>

      <!-- ================= STUDENT ================= -->
      <div v-else-if="role === 'STUDENT'">

        <!-- LANGUAGE SELECTOR -->
        <div class="mb-6">

          <label class="block text-sm font-medium mb-2">
            Select Language
          </label>

          <select
            v-model="studentLanguageId"
            class="border px-3 py-2 rounded-lg w-full max-w-xs"
          >
            <option value="" disabled>Select language</option>

            <option
              v-for="lang in languageStore.languagesList"
              :key="lang.id"
              :value="lang.id"
            >
              {{ lang.name }}
            </option>

          </select>

        </div>

        <h2 class="text-xl font-semibold mb-4">
          Available Courses
        </h2>

        <div class="grid gap-4">

          <div
            v-for="course in studentCourses"
            :key="course.id"
            class="border p-4 bg-white rounded hover:bg-gray-50 cursor-pointer"
            @click="openCourse(course.id)"
          >
            <h3 class="font-semibold">{{ course.title }}</h3>
            <p class="text-sm text-gray-500">
              Click to start learning
            </p>
          </div>

        </div>

      </div>

      <!-- ================= ADMIN ================= -->
      <div v-else>

        <h2 class="text-xl font-semibold mb-4">Admin Dashboard</h2>

        <div class="grid md:grid-cols-2 gap-4">

          <router-link to="/admin/users">
            <div class="bg-white p-5 border rounded">
              <h3 class="font-semibold">Manage Users</h3>
            </div>
          </router-link>

        </div>

      </div>

    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useLanguageStore } from '@/stores/language'
import api from '@/utils/axios'

const auth = useAuthStore()
const languageStore = useLanguageStore()
const router = useRouter()

const role = computed(() =>
  (auth.getUser?.role || 'STUDENT').toUpperCase()
)

/* ================= TEACHER ================= */
const selectedLanguageId = ref(null)
const teacherCourses = ref([])

/* ================= STUDENT ================= */
const studentLanguageId = ref(null)
const studentCourses = ref([])

/* load languages */
onMounted(async () => {
  await languageStore.getLanguages()
})

/* teacher courses */
watch(selectedLanguageId, async (langId) => {
  if (!langId) return

  try {
    const res = await api.get(`/languages/${langId}/courses`)
    teacherCourses.value = res.data
  } catch (err) {
    teacherCourses.value = []
    console.error(err)
  }
})

/* student courses */
watch(studentLanguageId, async (langId) => {
  if (!langId) return

  try {
    const res = await api.get(`/languages/${langId}/courses`)
    studentCourses.value = res.data
  } catch (err) {
    studentCourses.value = []
    console.error(err)
  }
})

/* navigation */
const openCourse = (id) => {
  router.push(`/courses/${id}/units`)
}

/* logout */
const handleLogout = async () => {
  await auth.logout()
}
</script>