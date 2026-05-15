import { createApp } from 'vue'
import { createPinia } from 'pinia'
import './style.css'
import App from './App.vue'
import router from './router'
import Toast, { POSITION } from "vue-toastification";
import "vue-toastification/dist/index.css";

const toastificationOptions = {
    timeout: 2000,
    position: POSITION.TOP_RIGHT,    
};

const pinia = createPinia()

const app = createApp(App)
app.use(pinia)
app.use(router)
app.use(Toast, toastificationOptions);

app.mount('#app')

