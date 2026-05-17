<template>
  <div class="dropdown dropdown-end">
    <!-- Trigger Button -->
    <div
      tabindex="0"
      role="button"
      class="btn btn-ghost btn-circle"
    >
      <Sun
        v-if="currentTheme === 'corporate'"
        class="w-5 h-5"
      />
      <Moon
        v-else
        class="w-5 h-5"
      />
    </div>

    <!-- Dropdown Menu -->
    <ul
      tabindex="0"
      class="dropdown-content z-1 menu p-2 shadow-2xl bg-base-100 rounded-box w-52 border border-base-300"
    >
      <li>
        <button 
          :class="{ 'active': currentTheme === 'corporate' }" 
          @click="changeTheme('corporate')"
        >
          <Sun class="w-4 h-4" /> 
          Corporate
          <span
            v-if="currentTheme === 'corporate'"
            class="badge badge-xs badge-primary"
          />
        </button>
      </li>
      <li>
        <button 
          :class="{ 'active': currentTheme === 'business' }" 
          @click="changeTheme('business')"
        >
          <Moon class="w-4 h-4" /> 
          Business
          <span
            v-if="currentTheme === 'business'"
            class="badge badge-xs badge-primary"
          />
        </button>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Sun, Moon } from 'lucide-vue-next';

const currentTheme = ref('corporate');

const changeTheme = (themeName) => {
  currentTheme.value = themeName;
  document.documentElement.setAttribute('data-theme', themeName);
  localStorage.setItem('app-theme', themeName);
  
  // Optional: Closes the dropdown after selection by removing focus
  if (document.activeElement instanceof HTMLElement) {
    document.activeElement.blur();
  }
};

onMounted(() => {
  const savedTheme = localStorage.getItem('app-theme') || 'corporate';
  changeTheme(savedTheme);
});
</script>