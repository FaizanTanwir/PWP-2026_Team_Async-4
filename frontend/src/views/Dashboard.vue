<template>
  <div>
    <h1>Teacher Dashboard</h1>
    <p>Welcome, {{ auth.user?.name }}</p>
    <button @click="auth.logout(); $router.push('/login')">Logout</button>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth';
const auth = useAuthStore();
</script>