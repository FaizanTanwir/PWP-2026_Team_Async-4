<template>
  <div 
    role="alert" 
    :class="['alert shadow-sm transition-all duration-300 alert-soft', alertConfig.class]"
  >
    <!-- Dynamic Icon Component -->
    <component :is="alertConfig.icon" class="w-5 h-5 shrink-0" />
    
    <div class="flex flex-col">
      <span v-if="message" class="text-sm font-medium">{{ message }}</span>
      <!-- Slot allows for custom HTML content if the message isn't enough -->
      <slot v-else />
    </div>

    <!-- Optional: Close button if you want to dismiss it -->
    <button v-if="dismissible" @click="$emit('close')" class="btn btn-ghost btn-xs btn-circle">
      <X class="w-4 h-4" />
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { 
  CheckCircle, 
  AlertCircle, 
  Info, 
  TriangleAlert, 
  X 
} from 'lucide-vue-next';

const props = defineProps({
  type: {
    type: String,
    default: 'info', // success, error, warning, info
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
  },
  message: {
    type: String,
    default: ''
  },
  dismissible: {
    type: Boolean,
    default: false
  }
});

defineEmits(['close']);

// Map the "type" prop to DaisyUI classes and Lucide icons
const alertConfig = computed(() => {
  const configs = {
    success: {
      class: 'alert-success',
      icon: CheckCircle
    },
    error: {
      class: 'alert-error',
      icon: AlertCircle
    },
    warning: {
      class: 'alert-warning',
      icon: TriangleAlert
    },
    info: {
      class: 'alert-info',
      icon: Info
    }
  };
  
  return configs[props.type] || configs.info;
});
</script>