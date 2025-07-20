<script setup>
import { computed } from 'vue'

const props = defineProps({
  reservation: {
    type: Object,
    required: true
  },
  statut: {
    type: Object,
    required: true
  },
  telephone: {
    type: String,
    required: false
  },
  personnel: {
    type: String,
    required: false
  }
})

const computedProps = computed(() => {
  if (props.reservation.reservation_horaire == null)
    return { "date": "Date", "time": "Heure", "tooltip": null }
  function pad2(input) {
    return input.toString().padStart(2, '0')
  }
  function strdt(dt) {
    var dt_str = new Date(dt)
    var date = pad2(dt_str.getDate())  + "/" + pad2(dt_str.getMonth()+1)
    var time = pad2(dt_str.getHours()) + ":" + pad2(dt_str.getMinutes())
    return date + " " + time
  }
  var datetime = strdt(props.reservation.reservation_horaire)
  var tooltip = strdt(props.reservation.reservation_creation)
  return {
    "date": datetime.split(" ")[0],
    "time": datetime.split(" ")[1],
    "tooltip": props.personnel ? `${props.personnel} (${tooltip})` : tooltip
  }
})

</script>

<template>
  <div class="reservation" :class="['statut'+(props.statut.statut_id ?? ''), props.statut.statut_id != 1 ? 'statut_hidden' : '']">
    <div :title="computedProps.tooltip" class="res_origin" tabindex="0">
      {{ props.personnel ? "👤" : "🌐" }}
    </div>
    <div class="res_num">
      {{ props.reservation.reservation_num }}
    </div>
    <div class="res_stat">
      <span :style="'margin: 0 5px; background-color:#'+props.statut.statut_hex"></span>
    </div>
    <div class="res_statut">
      {{ props.statut.statut_libelle }}
    </div>
    <div class="res_date">
       {{ computedProps.date}}
    </div>
    <div class="res_time">
       {{ computedProps.time}}
    </div>
    <div class="res_nb">
      {{ props.reservation.reservation_personnes }}
    </div>
    <div class="res_lastname">
      {{ props.reservation.reservation_nom }}
    </div>
    <div class="res_firstname">
      {{ props.reservation.reservation_prenom }}
    </div>
    <div class="res_phone">
      {{ props.telephone ?? props.reservation.reservation_telephone }}
    </div>
    <div class="res_more" v-if="props.reservation.reservation_commentaire">
      {{ props.reservation.reservation_id ? '...' : '' }}
    </div>
    <div class="res_comment" v-if="props.reservation.reservation_commentaire">
      {{ props.reservation.reservation_commentaire }}
    </div>
  </div>
</template>

<style scoped>
@media (pointer: coarse), (hover: none) {
  [title] {
    position: relative;
    display: inline-flex;
    justify-content: center;
    text-wrap: nowrap;
  }
  [title]:focus::after {
    content: attr(title);
    position: absolute;
    left: 25px;
    border: 0.5px solid;
    width: fit-content;
    padding: 3px;
  }
}
</style>
