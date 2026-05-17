<template>
  <div class="relative min-h-screen bg-base-200 transition-colors duration-300 flex flex-col">
    <!-- Top Navigation -->
    <header class="navbar bg-transparent absolute top-0 z-50">
      <div class="flex-1 px-4">
        <span class="text-xl font-black tracking-tighter text-primary">LINGUIST.</span>
      </div>
      <div class="flex-none px-4">
        <ThemeSwitcher />
      </div>
    </header>

    <!-- Main Hero Section -->
    <main class="hero min-h-screen">
      <!-- Changed lg:flex-row-reverse to lg:flex-row to put text on the left -->
      <div class="hero-content flex-col lg:flex-row w-full max-w-6xl gap-12 lg:gap-20">
        <!-- Mission Statement (Left Side) -->
        <div class="text-center lg:text-left flex-1">
          <div class="badge badge-primary badge-outline mb-4 px-4 py-3 font-semibold uppercase tracking-widest text-xs">
            Teacher Portal v5.0
          </div>
          <h1 class="text-5xl lg:text-7xl font-black leading-none text-base-content tracking-tight">
            Bridge the <br>
            <span class="text-primary italic">Language Gap.</span>
          </h1>
          <p class="py-8 text-lg text-base-content/70 max-w-md leading-relaxed">
            Manage students, track lesson progress, and deliver world-class 
            language curriculum with our unified dashboard for modern educators.
          </p>
          
          <div class="hidden lg:flex gap-4 items-center">
            <div class="avatar-group -space-x-4 rtl:space-x-reverse">
              <div class="avatar border-base-200">
                <div class="w-10 bg-neutral text-neutral-content flex items-center justify-center">
                  A
                </div>
              </div>
              <div class="avatar border-base-200">
                <div class="w-10 bg-neutral text-neutral-content flex items-center justify-center">
                  B
                </div>
              </div>
              <div class="avatar border-base-200">
                <div class="w-10 bg-neutral text-neutral-content flex items-center justify-center">
                  C
                </div>
              </div>
            </div>
            <span class="text-sm font-medium opacity-60">+2k teachers joined this week</span>
          </div>
        </div>

        <!-- Form Card (Right Side) -->
        <div class="card bg-base-100 w-full max-w-md shrink-0 shadow-2xl border border-base-300">
          <div class="card-body p-8 lg:p-10">
            <slot />
          </div>
        </div>
      </div>
    </main>
    
    <!-- Footer Credit -->
    <footer class="p-6 text-center text-sm opacity-40">
      &copy; 2026 Linguist AI Platform. All rights reserved.
    </footer>
  </div>
</template>

<script setup>
import ThemeSwitcher from '@/components/ThemeSwitcher.vue';
</script>