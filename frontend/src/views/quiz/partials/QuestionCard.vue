<template>
  <div class="card bg-base-100 card-bordered shadow-sm">
    <div class="card-body">
      <div class="flex justify-between items-start mb-4">
        <h2 class="card-title text-2xl">{{ question.question_text }}</h2>
      </div>

      <div v-if="question.type === 'mcq'" class="flex flex-col gap-3">
        <button 
          v-for="(option, index) in question.options" 
          :key="index"
          @click="submitAnswer(option)"
          class="btn btn-outline btn-block justify-start font-normal text-lg h-auto py-4"
        >
          <span class="badge badge-neutral mr-2">{{ String.fromCharCode(65 + index) }}</span>
          {{ option }}
        </button>
      </div>

      <div v-else class="space-y-4">
        <div class="form-control">
          <input 
            v-model="textInput" 
            type="text" 
            placeholder="Type your answer here..." 
            class="input input-bordered input-primary w-full text-lg"
            @keyup.enter="submitAnswer(textInput)"
            ref="answerInput"
          />
        </div>
        <button 
          @click="submitAnswer(textInput)" 
          class="btn btn-primary btn-block"
          :disabled="!textInput.trim()"
        >
          Submit Answer
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, nextTick } from 'vue';

const props = defineProps(['question']);
const emit = defineEmits(['answered']);

const textInput = ref('');
const answerInput = ref(null);

const submitAnswer = (value) => {
  emit('answered', value);
  textInput.value = ''; // Reset for next question
};

// Auto-focus the input field whenever the question changes
watch(() => props.question, () => {
  nextTick(() => {
    if (answerInput.value) answerInput.value.focus();
  });
});
</script>