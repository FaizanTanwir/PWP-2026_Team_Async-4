<template>
  <div class="drawer lg:drawer-open min-h-screen bg-base-100">
    <!-- Toggle for Mobile -->
    <input id="dashboard-drawer" type="checkbox" class="drawer-toggle" />

    <!-- Main Content Area -->
    <div class="drawer-content flex flex-col bg-base-200/50">
      
      <!-- Navbar -->
      <header class="navbar bg-base-100 border-b border-base-300 px-4 sticky top-0 z-30">
        <div class="flex-none lg:hidden">
          <label for="dashboard-drawer" class="btn btn-square btn-ghost drawer-button">
            <Menu class="size-5" />
          </label>
        </div>
        
        <div class="flex-1 px-2 font-bold text-lg">
          <!-- We can make this title dynamic based on the route later -->
          Linguist Dashboard
        </div>

        <div class="flex-none gap-2">
          <ThemeSwitcher />
          
          <!-- User Profile Dropdown -->
          <div class="dropdown dropdown-end">
            <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar online">
              <div class="w-10 rounded-full">
                <img alt="User Avatar" src="https://api.dicebear.com/7.x/avataaars/svg?seed=Felix" />
              </div>
            </div>
            <ul tabindex="0" class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52 border border-base-300">
              <li class="menu-title"><span>{{ auth.user?.name }}</span></li>
              <li><a>Profile</a></li>
              <li><button @click="handleLogout" class="text-error">Logout</button></li>
            </ul>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="p-6 grow">
        <!-- This is where all your pages will render! -->
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>

      <!-- Dashboard Footer -->
      <footer class="p-4 text-center opacity-30 text-xs">
        Linguist Platform v5.0.1
      </footer>
    </div>

    <!-- Sidebar (Drawer Side) -->
    <div class="drawer-side z-40 border-r border-base-300">
      <label for="dashboard-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
      
      <aside class="flex min-h-full flex-col w-64 bg-base-100">
        <!-- Logo Area -->
        <div class="p-6 border-b border-base-300">
          <span class="text-2xl font-black tracking-tighter text-primary">LINGUIST.</span>
        </div>

        <!-- Navigation Menu -->
        <ul class="menu p-4 w-full grow gap-2 text-base">
          <li>
            <router-link to="/" active-class="active">
              <LayoutDashboard class="size-5" /> Homepage
            </router-link>
          </li>
          <li>
            <router-link to="/students" active-class="active">
              <Users class="size-5" /> Students
            </router-link>
          </li>
          <li>
            <router-link to="/lessons" active-class="active">
              <BookOpen class="size-5" /> Lessons
            </router-link>
          </li>
          <div class="divider opacity-50"></div>
          <li>
            <router-link to="/settings" active-class="active">
              <Settings class="size-5" /> Settings
            </router-link>
          </li>
        </ul>

        <!-- Logout Bottom Section -->
        <div class="p-4 mt-auto">
          <button @click="handleLogout" class="btn btn-outline btn-error btn-block gap-2">
            <LogOut class="size-4" /> Logout
          </button>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup>
import { LayoutDashboard, Users, BookOpen, Settings, LogOut, Menu } from 'lucide-vue-next';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import ThemeSwitcher from '@/components/ThemeSwitcher.vue';

const auth = useAuthStore();
const router = useRouter();

const handleLogout = () => {
  auth.logout();
  router.push('/login');
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>