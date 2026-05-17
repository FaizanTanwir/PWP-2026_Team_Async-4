<template>
  <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <div class="card bg-base-100 border border-base-300 shadow-sm">
      <div class="card-body">
        <div class="flex items-center gap-4">
          <div class="p-3 bg-primary/10 rounded-xl">
            <Info class="size-8 text-primary" />
          </div>
          <div>
            <h1 class="text-3xl font-black tracking-tighter uppercase">
              About Team Async 4
            </h1>
            <p class="text-sm opacity-60">
              Linguist Pro • University Project 2026
            </p>
          </div>
        </div>
      </div>
    </div>

    <section>
      <div class="flex items-center gap-2 mb-6 px-2">
        <Users class="size-5 text-primary" />
        <h2 class="text-xl font-bold tracking-tight">
          Project Contributors
        </h2>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card bg-base-100 border border-base-300 shadow-sm">
          <div class="card-body items-center text-center">
            <div class="avatar placeholder mb-2">
              <div class="bg-neutral text-neutral-content rounded-full w-16">
                <span class="text-xl">AT</span>
              </div>
            </div>
            <h3 class="font-bold text-lg">
              Arshman Tariq
            </h3>
            <div class="badge badge-secondary badge-sm mb-4">
              Frontend Lead
            </div>
            <p class="text-sm opacity-70">
              Created wireframes, handled UI designing, and implemented frontend components.
            </p>
          </div>
        </div>

        <div class="card bg-base-100 border border-base-300 shadow-sm ring-1 ring-primary/20">
          <div class="card-body items-center text-center">
            <div class="avatar placeholder mb-2">
              <div class="bg-primary text-primary-content rounded-full w-16">
                <span class="text-xl">AB</span>
              </div>
            </div>
            <h3 class="font-bold text-lg">
              Atif Bashir
            </h3>
            <div class="badge badge-primary badge-sm mb-4">
              Backend Lead
            </div>
            <p class="text-sm opacity-70">
              Architected the server-side logic, API development, and database management.
            </p>
          </div>
        </div>

        <div class="card bg-base-100 border border-base-300 shadow-sm">
          <div class="card-body items-center text-center">
            <div class="avatar placeholder mb-2">
              <div class="bg-neutral text-neutral-content rounded-full w-16">
                <span class="text-xl">MB</span>
              </div>
            </div>
            <h3 class="font-bold text-lg">
              Muhammad Bilal
            </h3>
            <div class="badge badge-accent badge-sm mb-4">
              DevOps
            </div>
            <p class="text-sm opacity-70">
              Managed Docker containerization, VPS configuration, and cloud deployment.
            </p>
          </div>
        </div>

        <div class="card bg-base-100 border border-base-300 shadow-sm">
          <div class="card-body items-center text-center">
            <div class="avatar placeholder mb-2">
              <div class="bg-neutral text-neutral-content rounded-full w-16">
                <span class="text-xl">FT</span>
              </div>
            </div>
            <h3 class="font-bold text-lg">
              Faizan Tanveer
            </h3>
            <div class="badge badge-ghost badge-sm mb-4">
              QA & Docs
            </div>
            <p class="text-sm opacity-70">
              Assisted with comprehensive API documentation and wrote robust unit tests.
            </p>
          </div>
        </div>
      </div>
    </section>

    <div class="card bg-base-200/50 border border-base-300">
      <div class="card-body">
        <h3 class="font-bold flex items-center gap-2 mb-4">
          <Terminal class="size-5" /> Tech Stack
        </h3>
        <div class="flex flex-wrap gap-2">
          <div class="badge badge-outline">
            Laravel 12
          </div>
          <div class="badge badge-outline">
            Vue.js 3
          </div>
          <div class="badge badge-outline">
            Pinia
          </div>
          <div class="badge badge-outline">
            Tailwind CSS
          </div>
          <div class="badge badge-outline">
            DaisyUI
          </div>
          <div class="badge badge-outline">
            Docker
          </div>
          <div class="badge badge-outline">
            Kamatera Cloud
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Info, Users, Terminal } from 'lucide-vue-next'
</script>