<script setup>
defineProps({
  conteneur: {
    type: Object,
    required: true
  }
})
</script>

<template>
    <div class="conteneur" :style="{
      backgroundColor: `#${conteneur.conteneur_fond}`,
      color: `#${conteneur.conteneur_hex}`,
      textAlign: conteneur.conteneur_centre ? 'center' : 'left'
    }">
      <slot></slot>
    </div>
</template>

<style scoped>
.conteneur {
  padding: 1rem;
  border-radius: 0.5rem;
}
</style>