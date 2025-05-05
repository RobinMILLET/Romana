import { createApp } from 'vue'
import Welcome from './Pages/Welcome.vue'

const app = createApp({})
app.component('Welcome', Welcome)
app.mount('#app')