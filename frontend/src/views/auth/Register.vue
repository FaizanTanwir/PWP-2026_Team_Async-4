<template>
  <AuthLayout>
    <h2 class="card-title text-2xl font-bold mb-4">
      Create Account
    </h2>
    
    <form
      class="space-y-4"
      @submit.prevent="handleRegister"
    >
      <!-- Global Error Alert -->
      <Alert
        v-if="errorMessage"
        :message="errorMessage"
        type="error"
      />

      <!-- Name Field -->
      <div class="form-control">
        <label class="label font-semibold">
          <span class="flex items-center gap-2">
            <User
              :size="18"
              class="text-primary"
            /> Full Name
          </span>
        </label>
        <input
          v-model="name"
          type="text"
          placeholder="John Doe"
          class="input input-primary w-full"
          :class="{ 'input-error': errors.name }"
        >
        <label
          v-if="errors.name"
          class="label"
        >
          <span class="label-text-alt text-error font-medium">{{ errors.name[0] }}</span>
        </label>
      </div>

      <!-- Email Field -->
      <div class="form-control">
        <label class="label font-semibold">
          <span class="flex items-center gap-2">
            <Mail
              :size="18"
              class="text-primary"
            /> Email Address
          </span>
        </label>
        <input
          v-model="email"
          type="email"
          placeholder="m@oulu.fi"
          class="input input-primary w-full"
          :class="{ 'input-error': errors.email }"
        >
        <label
          v-if="errors.email"
          class="label"
        >
          <span class="label-text-alt text-error font-medium">{{ errors.email[0] }}</span>
        </label>
      </div>

      <!-- Password Field -->
      <div class="form-control">
        <label class="label font-semibold">
          <span class="flex items-center gap-2">
            <Lock
              :size="18"
              class="text-primary"
            /> Password
          </span>
        </label>
        <input 
          v-model="password" 
          type="password" 
          placeholder="••••••••"
          class="input input-primary w-full" 
          :class="{ 'input-error': errors.password }"
        >
        <label
          v-if="errors.password"
          class="label"
        >
          <span class="label-text-alt text-error font-medium">{{ errors.password[0] }}</span>
        </label>
      </div>

      <!-- Confirm Password Field -->
      <div class="form-control">
        <label class="label font-semibold">
          <span class="flex items-center gap-2">
            <ShieldCheck
              :size="18"
              class="text-primary"
            /> Confirm Password
          </span>
        </label>
        <input 
          v-model="passwordConfirmation" 
          type="password" 
          placeholder="••••••••"
          class="input input-primary w-full" 
          :class="{ 'input-error': errors.password }" 
        >
      </div>

      <!-- Actions -->
      <div class="form-control mt-6">
        <button 
          type="submit" 
          class="btn btn-primary w-full" 
          :disabled="isLoading"
        >
          <span
            v-if="isLoading"
            class="loading loading-spinner"
          />
          Create Account
        </button>
      </div>

      <div class="divider text-xs text-base-content/50 uppercase">
        Already have an account?
      </div>

      <router-link
        to="/login"
        class="btn btn-outline btn-secondary w-full"
      >
        Sign In Instead
      </router-link>
    </form>
  </AuthLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import AuthLayout from '@/layouts/AuthLayout.vue';
import Alert from '@/components/Alert.vue';
import { useAuthStore } from '@/stores/auth';
import { useToast } from "vue-toastification";
import api from '@/utils/api';
import { Mail, Lock, User, ShieldCheck } from 'lucide-vue-next';

const router = useRouter();
const auth = useAuthStore();
const toast = useToast();

const name = ref('');
const email = ref('');
const password = ref('');
const passwordConfirmation = ref('');
const isLoading = ref(false);
const errors = ref({}); 
const errorMessage = ref('');

const handleRegister = async () => {
  isLoading.value = true;
  errors.value = {};
  errorMessage.value = '';

  try {
    const response = await api.post('/register', {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value
    });

    // 1. Save the auth data (token and user) just like Login
    // This triggers your Pinia store to save to localStorage/state
    auth.setAuth(response.data);

    // 2. Show a professional success notification
    toast.success(`Welcome to Linguist, ${response.data.user.name}!`);

    // 3. Move them directly to the app dashboard
    router.push('/');
  } catch (error) {
    console.log({error})
    const response = error.response;
    
    if (response?.status === 422) {
      errors.value = response.data.errors;
    } else {
      errorMessage.value = response?.data?.message || "Something went wrong. Please try again.";
      toast.error("Please check the form for errors.");
    }
  } finally {
    isLoading.value = false;
  }
};
</script>