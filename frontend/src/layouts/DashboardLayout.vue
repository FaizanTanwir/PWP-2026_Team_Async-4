<template>
  <div class="drawer lg:drawer-open min-h-screen bg-base-100">
    <input
      id="dashboard-drawer"
      type="checkbox"
      class="drawer-toggle"
    >

    <div class="drawer-content flex flex-col bg-base-200/50">
      <header class="navbar bg-base-100 border-b border-base-300 px-4 sticky top-0 z-30">
        <div class="flex-none lg:hidden">
          <label
            for="dashboard-drawer"
            class="btn btn-square btn-ghost drawer-button"
          >
            <Menu class="size-5" />
          </label>
        </div>
        
        <div class="flex-1 px-2 font-bold text-lg tracking-tight">
          Linguist <span class="text-primary text-sm font-black">PRO</span>
        </div>

        <div class="flex-none gap-2">
          <ThemeSwitcher />
          
          <div class="avatar online">
            <div class="w-10 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
              <img :src="`https://api.dicebear.com/7.x/avataaars/svg?seed=${auth.user?.name}`">
            </div>
          </div>
        </div>
      </header>

      <main class="p-6 grow">
        <router-view v-slot="{ Component }">
          <transition
            name="fade"
            mode="out-in"
          >
            <component :is="Component" />
          </transition>
        </router-view>
      </main>

      <footer class="p-4 text-center opacity-30 text-[10px] uppercase tracking-widest">
        Linguist API v1.0 • {{ auth.getRole }} Mode • Team Async 4
      </footer>
    </div>

    <div class="drawer-side z-40 border-r border-base-300">
      <label
        for="dashboard-drawer"
        class="drawer-overlay"
      />
      
      <aside class="flex min-h-full flex-col w-64 bg-base-100">
        <div class="p-6 border-b border-base-300">
          <span class="text-2xl font-black tracking-tighter text-primary italic">LINGUIST.</span>
        </div>

        <ul class="menu p-4 w-full grow gap-1 text-base">
          <li>
            <router-link
              to="/dashboard"
              active-class="active"
            >
              <LayoutDashboard class="size-5" /> Dashboard
            </router-link>
          </li>

          <template v-if="auth.getRole === UserRole.TEACHER || auth.getRole === UserRole.ADMIN">
            <div class="divider text-[10px] opacity-40 uppercase font-bold mt-4">
              Management
            </div>
            <li>
              <router-link
                to="/"
                active-class="active"
              >
                <BookOpen class="size-5" /> TEACHER related pages
              </router-link>
            </li>
          </template>

          <template v-if="auth.getRole === UserRole.STUDENT">
            <div class="divider text-[10px] opacity-40 uppercase font-bold mt-4">
              Learning
            </div>
            <li>
              <router-link
                to="/"
                active-class="active"
              >
                <History class="size-5" /> STUDENT related pages
              </router-link>
            </li>
          </template>

          <div class="divider text-[10px] opacity-40 uppercase font-bold mt-4">
            Project
          </div>
          <li>
            <router-link
              to="/about"
              active-class="active"
            >
              <Info class="size-5" /> About Team
            </router-link>
          </li>
        </ul>

        <div class="p-4 mt-auto border-t border-base-300 bg-base-200/30 text-xs">
          <div class="px-4 py-2 opacity-50 font-bold uppercase truncate">
            {{ auth.user?.name }}
          </div>
          <button
            class="btn btn-sm btn-ghost btn-block text-error gap-2 justify-start hover:bg-error/10"
            @click="handleLogout"
          >
            <LogOut class="size-4" /> Sign Out
          </button>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup>
import { 
  LayoutDashboard, 
  BookOpen, 
  LogOut, 
  Menu,
  History,
  Info 
} from 'lucide-vue-next';
import { useAuthStore } from '@/stores/auth';
import { UserRole } from '@/constants/roles';
import ThemeSwitcher from '@/components/ThemeSwitcher.vue';

const auth = useAuthStore();

const handleLogout = () => {
  auth.logout();
};
</script>