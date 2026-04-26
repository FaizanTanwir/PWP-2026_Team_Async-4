import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '@/utils/axios';

export const useLanguageStore = defineStore('language', () => {
    // State
    const languages = ref(JSON.parse(localStorage.getItem('languages')) || null);

    // Getters
    const languagesList = computed(() => languages.value || []);

    // Actions
    function setLanguages(data) {
        languages.value = data;
        localStorage.setItem('languages', JSON.stringify(data));
    }

    async function getLanguages() {
        try {
            const response = await api.get('/languages');
            setLanguages(response.data);
        } catch (error) {
            console.error("Failed to fetch languages", error);
        }
    }

    return { 
        languagesList,
        getLanguages
    };
});