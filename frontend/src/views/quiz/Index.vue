<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import api from '@/utils/api';
import { useQuizStore } from '@/stores/quiz'; 
import QuizHeader from './partials/QuizHeader.vue';
import QuestionCard from './partials/QuestionCard.vue';
import QuizResults from './partials/QuizResults.vue';

const route = useRoute();
const quizStore = useQuizStore();

const questions = ref([]);
const currentIndex = ref(0);
const loading = ref(true);
const quizFinished = ref(false);

// We store the API responses here to calculate final stats
const results = ref([]); 

const fetchQuiz = async () => {
  quizStore.loading = true;
  try {
    const response = await api.get(`/units/${route.params.id}/quiz`);
    // Initialize the store with the 10 questions
    quizStore.startNewQuiz(response.data.questions.slice(0, 10));
  } catch (error) {
    console.error("Failed to load quiz", error);
  } finally {
    quizStore.loading = false;
  }
};

const handleAnswer = async (userAnswer) => {
  const q = quizStore.questions[quizStore.currentStep];
  
  try {
    const response = await api.post(`/units/${route.params.id}/submissions`, {
      type: q.type,
      question_text: q.question_text,
      provided_answer: userAnswer,
      correct_answer: q.correct_answer
    });

    // Save the server's calculated accuracy to the store
    quizStore.recordSubmission(response.data);
  } catch (error) {
    console.error("Submission error", error);
  }
};

onMounted(fetchQuiz);
</script>

<template>
  <div class="container mx-auto p-6 max-w-2xl">
    <div v-if="quizStore.loading" class="flex justify-center py-20">
      <span class="loading loading-spinner loading-lg text-primary"></span>
    </div>

    <div v-else>
      <div v-if="quizStore.finished">
        <QuizResults 
            v-if="quizStore.finished"
            :accuracy="quizStore.averageAccuracy" 
            :results="quizStore.results" 
            @restart="fetchQuiz" 
            />
      </div>

      <div v-else-if="quizStore.questions.length > 0">
        <QuizHeader 
          :currentIndex="quizStore.currentStep" 
          :total="quizStore.totalQuestions" 
          :unitId="$route.params.id"
        />

        <QuestionCard 
          :question="quizStore.questions[quizStore.currentStep]" 
          @answered="handleAnswer"
        />
      </div>
    </div>
  </div>
</template>