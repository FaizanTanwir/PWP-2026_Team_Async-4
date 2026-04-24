import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '@/utils/axios';

export const useAuthStore = defineStore('auth', () => {
    // State
    const user = ref(JSON.parse(localStorage.getItem('user')) || null);
    const token = ref(localStorage.getItem('token') || null);

    // Getters
    const isAuthenticated = computed(() => !!token.value);
    const getUser = computed(() => user.value);

    // Actions
    function setAuth(data) {
        user.value = data.user;
        token.value = data.token;

        // Persist to localStorage so sessions survive refreshes
        localStorage.setItem('user', JSON.stringify(data.user));
        localStorage.setItem('token', data.token);
    }

    async function logout() {
        try {
            // Tell Laravel to revoke the token
            await api.post('/logout');
        } catch (error) {
            console.error("Backend logout failed, but clearing local session anyway", error);
        } finally {
            // Always clear local data
            user.value = null;
            token.value = null;
            localStorage.removeItem('user');
            localStorage.removeItem('token');
            
            // Navigate to login
            window.location.href = '/login';
        }
    }

    return { 
        user, 
        token, 
        isAuthenticated, 
        getUser, 
        setAuth, 
        logout 
    };
});