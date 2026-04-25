import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const routes = [
    { path: '/login', name: 'Login', component: () => import('@/views/auth/Login.vue') },
    { path: '/register', name: 'Register', component: () => import('@/views/auth/Register.vue') },
    {
        path: '/',
        name: 'Dashboard',
        component: () => import('@/views/Dashboard.vue'),
        meta: { requiresAuth: true } // Custom property to protect the route
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
    {
        path: '/courses/:id/units',
        component: () => import('@/views/Units.vue')
    },
    {
        path: '/units/:id/sentences',
        component: () => import('@/views/Sentences.vue')
    },
    {
        path: '/units/:id/practice',
        component: () => import('@/views/Practice.vue')
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// The Navigation Guard
router.beforeEach((to, from, next) => {
    const auth = useAuthStore();

    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        next('/login');
    } else if ((to.name === 'Login' || to.name === 'Register') && auth.isAuthenticated) {
        next('/'); // Redirect to dashboard if already logged in
    } else {
        next();
    }
});

export default router;