import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const instance = axios.create({
    baseURL: import.meta.env.VITE_API_URL
});

// Automatically add the Bearer token to every request
instance.interceptors.request.use((config) => {
    const auth = useAuthStore();
    if (auth.token) {
        config.headers.Authorization = `Bearer ${auth.token}`;
    }
    return config;
});

instance.interceptors.response.use(
    (response) => response,
    (error) => {
        const { status, config } = error.response || {};

        // Only redirect if it's a 401 AND we aren't already trying to login
        if (status === 401 && !config.url.includes('/login')) {
            const auth = useAuthStore();
            auth.logout();
            window.location.href = '/login';
        }

        // Always return the error so the component's catch block can read it
        return Promise.reject(error);
    }
);

export default instance;