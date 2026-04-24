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