<template>
  <div class="drawer lg:drawer-open min-h-screen bg-base-100">
    <input id="dashboard-drawer" type="checkbox" class="drawer-toggle" />

    <div class="drawer-content flex flex-col bg-base-200/50">
      <!-- Navbar -->
      <header class="navbar bg-base-100 border-b border-base-300 px-4 sticky top-0 z-30">
        <div class="flex-none lg:hidden">
          <label for="dashboard-drawer" class="btn btn-square btn-ghost drawer-button">
            <Menu class="size-5" />
          </label>
        </div>
        
        <div class="flex-1 px-2 font-bold text-lg tracking-tight">
          Linguist <span class="text-primary text-sm font-black">PRO</span>
        </div>

        <div class="flex-none gap-2">
          <ThemeSwitcher />
          
          <div class="dropdown dropdown-end">
            <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar online">
              <div class="w-10 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                <img :src="`https://api.dicebear.com/7.x/avataaars/svg?seed=${auth.user?.name}`" />
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="p-6 grow">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>

      <footer class="p-4 text-center opacity-30 text-[10px] uppercase tracking-widest">
        Linguist API v1.0 • Connected to {{ auth.getRole }} Node
      </footer>
    </div>

    <!-- Sidebar -->
    <div class="drawer-side z-40 border-r border-base-300">
      <label for="dashboard-drawer" class="drawer-overlay"></label>
      
      <aside class="flex min-h-full flex-col w-64 bg-base-100">
        <div class="p-6 border-b border-base-300">
          <span class="text-2xl font-black tracking-tighter text-primary italic">LINGUIST.</span>
        </div>

        <ul class="menu p-4 w-full grow gap-1 text-base">
          <!-- Common Link -->
          <li>
            <router-link to="/dashboard" active-class="active">
              <LayoutDashboard class="size-5" /> Dashboard
            </router-link>
          </li>

          <!-- TEACHER & ADMIN: Management Resources -->
          <template v-if="auth.getRole === UserRole.TEACHER || auth.getRole === UserRole.ADMIN">
            <div class="divider text-[10px] opacity-40 uppercase font-bold mt-4">Management</div>
            <li>
              <router-link to="/languages" active-class="active">
                <Languages class="size-5" /> Languages
              </router-link>
            </li>
            <li>
              <router-link to="/courses" active-class="active">
                <BookOpen class="size-5" /> My Courses
              </router-link>
            </li>
          </template>

          <!-- STUDENT: Learning Resources -->
          <template v-if="auth.getRole === UserRole.STUDENT">
            <div class="divider text-[10px] opacity-40 uppercase font-bold mt-4">Learning</div>
            <li>
              <router-link to="/courses" active-class="active">
                <GraduationCap class="size-5" /> Catalog
              </router-link>
            </li>
            <li>
              <router-link to="/my-submissions" active-class="active">
                <History class="size-5" /> Progress Logs
              </router-link>
            </li>
          </template>

          <!-- System/Shared -->
          <div class="divider text-[10px] opacity-40 uppercase font-bold mt-4">System</div>
          <li v-if="auth.getRole === UserRole.ADMIN">
            <router-link to="/users" active-class="active">
              <Users class="size-5" /> User Manager
            </router-link>
          </li>
          <li>
            <router-link to="/settings" active-class="active">
              <Settings class="size-5" /> Settings
            </router-link>
          </li>
        </ul>

        <div class="p-4 mt-auto border-t border-base-300 bg-base-200/30">
          <button @click="handleLogout" class="btn btn-sm btn-ghost btn-block text-error gap-2 justify-start hover:bg-error/10">
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
  Users, 
  BookOpen, 
  Settings, 
  LogOut, 
  Menu, 
  Languages, 
  GraduationCap, 
  History 
} from 'lucide-vue-next';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import { UserRole } from '@/constants/roles'; // Import your roles
import ThemeSwitcher from '@/components/ThemeSwitcher.vue';

const auth = useAuthStore();
const router = useRouter();

const handleLogout = () => {
  auth.logout();
  router.push('/login');
};
</script>