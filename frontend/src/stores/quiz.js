import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useQuizStore = defineStore('quiz', () => {
    // State
    const questions = ref([]);
    const results = ref([]); // Stores the responses from the /submissions API
    const loading = ref(false);
    const finished = ref(false);

    // Getters
    const currentStep = computed(() => results.value.length);
    const totalQuestions = computed(() => questions.value.length);
    
    // Calculate average accuracy from the server responses
    const averageAccuracy = computed(() => {
        if (results.value.length === 0) return 0;
        const sum = results.value.reduce((acc, curr) => acc + curr.accuracy, 0);
        return Math.round(sum / results.value.length);
    });

    // Actions
    function startNewQuiz(newQuestions) {
        questions.value = newQuestions;
        results.value = []; // Reset score/results
        finished.value = false;
        loading.value = false;
    }

    function recordSubmission(apiResponse) {
        results.value.push(apiResponse);
        
        // If we've answered all questions, mark as finished
        if (results.value.length >= questions.value.length) {
            finished.value = true;
        }
    }

    return { 
        questions, 
        results, 
        loading, 
        finished,
        currentStep,
        totalQuestions,
        averageAccuracy,
        startNewQuiz,
        recordSubmission
    };
});