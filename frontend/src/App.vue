<template>
  <nav v-if="auth.isAuthenticated">
    <router-link to="/">Dashboard</router-link> |
    <button @click="handleLogout">Logout</button>
  </nav>

  <main>
    <router-view />
  </main>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth';
const auth = useAuthStore();

console.log({auth})
</script>