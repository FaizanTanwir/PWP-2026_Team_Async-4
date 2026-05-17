import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { UserRole } from '@/constants/roles';

const allRoles = Object.values(UserRole);
const teacherAndAdminRoles = [UserRole.ADMIN, UserRole.TEACHER];

const routes = [
  // --- Guest Routes (No Sidebar) ---
  { 
    path: '/login', 
    name: 'Login', 
    component: () => import('@/views/auth/Login.vue'),
  },
  { 
    path: '/register', 
    name: 'Register', 
    component: () => import('@/views/auth/Register.vue'),
  },

  // --- Authenticated Dashboard Routes (With Sidebar) ---
  {
    path: '/',
    component: () => import('@/layouts/DashboardLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '', // Matches "/"
        name: 'Dashboard',
        component: () => import('@/views/Dashboard.vue'),
        meta: { roles: allRoles }
      },
      // Student & Teacher Accessible
      {
        path: 'languages/:id/courses',
        name: 'Courses',
        component: () => import('@/views/course/Index.vue'),
        meta: { roles: allRoles }
      },
      {
        path: 'courses/:id/units',
        name: 'Units',
        component: () => import('@/views/unit/Index.vue'),
        meta: { roles: allRoles }
      },
      {
        path: 'units/:id/sentences',
        name: 'Sentences',
        component: () => import('@/views/sentence/Index.vue'),
        meta: { roles: allRoles }
      },
      // {
      //   path: 'courses/:id/units',
      //   name: 'CourseUnits',
      //   component: () => import('@/views/CourseUnits.vue'),
      //   meta: { roles: allRoles }
      // },
      // {
      //   path: 'units/:id/practice',
      //   name: 'Practice',
      //   component: () => import('@/views/Practice.vue'),
      //   meta: { roles: allRoles }
      // },
      // Teacher/Admin Only Routes
      // {
      //   path: 'teacher/add-course',
      //   component: () => import('@/views/teacher/AddCourse.vue'),
      //   meta: { roles: teacherAndAdminRoles }
      // },
      // {
      //   path: 'teacher/edit-course/:id',
      //   component: () => import('@/views/teacher/EditCourse.vue'),
      //   meta: { roles: teacherAndAdminRoles }
      // },
      // {
      //   path: 'teacher/add-unit',
      //   component: () => import('@/views/teacher/AddUnit.vue'),
      //   meta: { roles: teacherAndAdminRoles }
      // },
      // {
      //   path: 'teacher/edit-unit/:id',
      //   component: () => import('@/views/teacher/EditUnit.vue'),
      //   meta: { roles: teacherAndAdminRoles }
      // },
      // {
      //   path: 'teacher/add-sentence',
      //   component: () => import('@/views/teacher/AddSentence.vue'),
      //   meta: { roles: teacherAndAdminRoles }
      // },
      // {
      //   path: 'teacher/edit-sentence/:id',
      //   component: () => import('@/views/teacher/EditSentence.vue'),
      //   meta: { roles: teacherAndAdminRoles }
      // }
    ]
  },
  
  // 404 Catch-all (Optional but recommended)
  { 
    path: '/:pathMatch(.*)*', 
    redirect: '/' 
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// --- Navigation Guard ---
router.beforeEach((to, from, next) => {
  const auth = useAuthStore()
  const userRoles = auth.user?.roles || [] // Assuming roles is an array like ['Teacher']

  // 1. Check Auth Requirement
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return next('/login')
  }

  // 2. Check Guest-Only Pages (Prevent logged-in users from seeing Login/Register)
  if (to.meta.guestOnly && auth.isAuthenticated) {
    return next('/')
  }

  // 3. Role Validation
  if (to.meta.roles) {
    const hasAccess = to.meta.roles.some(role => userRoles.includes(role))
    if (!hasAccess) {
      // Redirect to dashboard if they don't have permission
      return next({ name: 'Dashboard' }) 
    }
  }

  next()
})

export default router