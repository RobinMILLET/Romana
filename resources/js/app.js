import { createApp } from 'vue'
import Conteneur from './Components/Conteneur.vue'

const app = createApp({})
app.component('Conteneur', Conteneur)
app.mount('#app')