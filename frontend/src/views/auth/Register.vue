<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">

    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-xl shadow-lg border border-gray-100">

      <!-- Header -->
      <div>
        <h2 class="text-center text-3xl font-extrabold text-gray-900">
          Create Account
        </h2>

        <p class="mt-2 text-center text-sm text-gray-600">
          Already have an account?
          <router-link
            to="/login"
            class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors"
          >
            Sign in here
          </router-link>
        </p>
      </div>

      <!-- Form -->
      <form class="mt-8 space-y-6" @submit.prevent="handleRegister">

        <div class="rounded-md shadow-sm space-y-3">

          <!-- Name -->
          <input
            v-model="name"
            type="text"
            required
            placeholder="Full name"
            class="w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
          />

          <!-- Email -->
          <input
            v-model="email"
            type="email"
            required
            placeholder="Email address"
            class="w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
          />

          <!-- Password -->
          <input
            v-model="password"
            type="password"
            required
            placeholder="Password"
            class="w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
          />

          <!-- Confirm Password -->
          <input
            v-model="confirmPassword"
            type="password"
            required
            placeholder="Confirm password"
            class="w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
          />

        </div>

        <!-- Error -->
        <p v-if="error" class="text-sm text-red-500">
          {{ error }}
        </p>

        <!-- Submit -->
        <button
          type="submit"
          :disabled="isLoading"
          class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50"
        >
          <span v-if="isLoading">Creating account...</span>
          <span v-else>Register</span>
        </button>

      </form>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/utils/axios'

const router = useRouter()

const name = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const isLoading = ref(false)
const error = ref('')

const handleRegister = async () => {

  error.value = ''

  // frontend validation
  if (password.value !== confirmPassword.value) {
    error.value = "Passwords do not match"
    return
  }

  isLoading.value = true

  try {
    await api.post('/register', {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: confirmPassword.value
    })

    router.push('/login')

  } catch (err) {
    console.error(err)

    // show real backend message if available
    const backendMessage =
      err.response?.data?.message ||
      Object.values(err.response?.data?.errors || {})
        .flat()
        .join(' ') ||
      "Registration failed. Please try again."

    error.value = backendMessage

  } finally {
    isLoading.value = false
  }
}
</script>