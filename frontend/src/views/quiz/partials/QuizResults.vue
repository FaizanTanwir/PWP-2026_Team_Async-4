<template>
  <div class="flex flex-col gap-6 w-full max-w-2xl mx-auto">
    <div class="card bg-base-100 card-bordered shadow-lg text-center">
      <div class="card-body items-center">
        <h2 class="card-title text-3xl">
          Quiz Completed!
        </h2>
        
        <div class="stats shadow bg-base-200 my-6 w-full">
          <div class="stat">
            <div class="stat-title">
              Overall Accuracy
            </div>
            <div class="stat-value text-primary">
              {{ accuracy }}%
            </div>
            <div class="stat-desc text-secondary">
              Across {{ results.length }} questions
            </div>
          </div>
        </div>

        <div
          class="alert shadow-sm mb-4"
          :class="accuracy >= 50 ? 'alert-success' : 'alert-warning'"
        >
          <span>{{ feedbackMessage }}</span>
        </div>

        <div class="card-actions justify-center gap-4">
          <button
            class="btn btn-primary"
            @click="$emit('restart')"
          >
            Try Again
          </button>
        </div>
      </div>
    </div>

    <div class="flex flex-col gap-4">
      <h3 class="text-xl font-bold px-2">
        Review Answers
      </h3>
      
      <div
        v-for="(res, index) in results"
        :key="index" 
        class="card card-compact bg-base-100 card-bordered shadow-sm"
      >
        <div class="card-body">
          <div class="flex justify-between items-start gap-2">
            <span class="badge badge-neutral uppercase">Q{{ index + 1 }} : {{ res.type.replace(/_/g, ' ') }}</span>
            <span
              :class="res.is_passed ? 'text-success' : 'text-error'"
              class="text-sm font-bold uppercase"
            >
              {{ res.is_passed ? 'Correct' : 'Incorrect' }}
            </span>
          </div>
          
          <p class="font-medium text-lg mt-2">
            {{ res.question_text }}
          </p>
          
          <div class="mt-3 p-3 rounded-lg bg-base-200 flex flex-col gap-2 text-sm">
            <div>
              <span class="opacity-60 block">Your Answer:</span>
              <span
                :class="res.is_passed ? 'text-success' : 'text-error'"
                class="font-bold"
              >
                {{ res.provided_answer || '(Empty)' }}
              </span>
            </div>
            
            <div v-if="!res.is_passed">
              <span class="opacity-60 block">Correct Answer:</span>
              <span class="text-success font-bold">{{ res.correct_answer }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

// results is the array of responses from your POST /submissions endpoint
const props = defineProps(['accuracy', 'results']);
// eslint-disable-next-line no-unused-vars
const emit = defineEmits(['restart']);

const feedbackMessage = computed(() => {
  if (props.accuracy === 100) return "Perfect score! You're a master of this unit.";
  if (props.accuracy >= 70) return "Great job! You've grasped the core concepts.";
  if (props.accuracy >= 50) return "Good effort, but a little more review might help.";
  return "Don't give up! Review the unit and try again.";
});
</script>