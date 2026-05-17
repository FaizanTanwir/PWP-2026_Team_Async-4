import { createApp } from 'vue'
import { createPinia } from 'pinia'
import './style.css'
import App from './App.vue'
import router from './router'

// Third-party notification library for user feedback
import Toast, { POSITION } from "vue-toastification";
import "vue-toastification/dist/index.css";

/**
 * Configuration for vue-toastification
 * Sets global defaults for all alert messages in the app
 */
const toastificationOptions = {
    timeout: 2000,
    position: POSITION.TOP_RIGHT,    
};

// Initialize Pinia for global state management
const pinia = createPinia()

// Initialize the Vue application instance
const app = createApp(App)
app.use(pinia)  // State management
app.use(router) // SPA Routing
app.use(Toast, toastificationOptions); // Global notification system 

// Mount the application to the DOM element with ID 'app'
app.mount('#app')

