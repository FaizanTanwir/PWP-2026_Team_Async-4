<template>
  <AuthLayout>
    <h2 class="card-title text-2xl font-bold mb-4">Welcome Back</h2>
    
    <form @submit.prevent="handleLogin" class="space-y-4">
      <Alert v-if="errorMessage" :message="errorMessage" type="error" />
      <!-- Email Field -->
      <div class="form-control">
        <label class="label font-semibold">
          <span class="flex items-center gap-2">
            <Mail :size="18" class="text-primary" /> Email Address
          </span>
        </label>
        <input
          v-model="email"
          class="input input-primary w-full"
        />
        <label v-if="errors.email" class="label">
          <span class="label-text-alt text-error font-medium">{{ errors.email[0] }}</span>
        </label>
      </div>

      <!-- Password Field -->
      <div class="form-control">
        <label class="label font-semibold">
          <span class="flex items-center gap-2">
            <Lock :size="18" class="text-primary" /> Password
          </span>
        </label>
        <input 
          v-model="password" 
          type="password" 
          class="input input-primary w-full" 
        />
        <label v-if="errors.password" class="label">
          <span class="label-text-alt text-error font-medium">{{ errors.password[0] }}</span>
        </label>
      </div>

      <!-- Actions -->
      <div class="form-control mt-6">
        <button 
          type="submit" 
          class="btn btn-primary w-full" 
          :disabled="isLoading"
        >
          <span v-if="isLoading" class="loading loading-spinner"></span>
          Sign In
        </button>
      </div>

      <div class="divider text-xs text-base-content/50 uppercase">New Here?</div>

      <router-link to="/register" class="btn btn-outline btn-secondary w-full">
        Create Student Account
      </router-link>
    </form>
  </AuthLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import AuthLayout from '@/layouts/AuthLayout.vue';
import Alert from '@/components/Alert.vue';
import api from '@/utils/api';
import { Mail, Lock } from 'lucide-vue-next';

const auth = useAuthStore();
const router = useRouter();

const email = ref('');
const password = ref('');
const isLoading = ref(false);
const errors = ref({}); // Store Laravel validation errors
const errorMessage = ref(''); // Store "Invalid Credentials" message

const handleLogin = async () => {
  isLoading.value = true;
  errors.value = {};
  errorMessage.value = '';

  try {
    const response = await api.post('/login', {
      email: email.value,
      password: password.value
    });
    auth.setAuth(response.data);
    router.push('/');
  } catch (error) {
    console.error('Login failed', error);
    const response = error.response;
    
    if (response?.status === 422) {
      // Laravel Validation Errors
      errors.value = response.data.errors;
    } else if (response?.status === 401) {
      // Invalid Credentials
      errorMessage.value = response.data.message;
    } else {
      errorMessage.value = "An unexpected error occurred. Please try again.";
    }
  } finally {
    isLoading.value = false;
  }
};
</script>