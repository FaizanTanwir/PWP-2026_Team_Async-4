import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  { path: '/login', name: 'Login', component: () => import('@/views/auth/Login.vue') },
  { path: '/register', name: 'Register', component: () => import('@/views/auth/Register.vue') },

  {
    path: '/',
    name: 'Dashboard',
    component: () => import('@/views/Dashboard.vue'),
    meta: { requiresAuth: true }
  },

  {
    path: '/teacher/add-course',
    component: () => import('@/views/teacher/AddCourse.vue')
  },

  {
    path: '/teacher/edit-course/:id',
    component: () => import('@/views/teacher/EditCourse.vue')
  },

  {
    path: '/teacher/add-unit',
    component: () => import('@/views/teacher/AddUnit.vue')
  },

  {
    path: '/teacher/edit-unit/:id',
    component: () => import('@/views/teacher/EditUnit.vue')
  },

  // ✅ ONLY ONE UNIT ROUTE (FIXED)
  {
    path: '/courses/:id/units',
    component: () => import('@/views/CourseUnits.vue'),
    meta: { requiresAuth: true }
  },

  {
    path: '/teacher/add-sentence',
    component: () => import('@/views/teacher/AddSentence.vue')
  },

  {
    path: '/units/:id/practice',
    component: () => import('@/views/Practice.vue')
  },
  {
    path: '/teacher/edit-sentence/:id',
    component: () => import('@/views/teacher/EditSentence.vue'),
    meta: { requiresAuth: true }
}
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    next('/login')
  } else {
    next()
  }
})

export default router