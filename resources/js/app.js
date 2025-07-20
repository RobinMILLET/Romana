import { createApp } from 'vue'
import Conteneur from './Components/Conteneur.vue'
import Reservation from './Components/Reservation.vue'

const app = createApp({})
app.component('Conteneur', Conteneur)
app.component('Reservation', Reservation)
app.mount('#app')