<script setup>
import { computed } from 'vue'

const props = defineProps({
  conteneur: {
    type: Object,
    required: true
  },
  notext: {
    type: Boolean,
    required: false,
    default: false
  }
})

const computedClass = computed(() => {
  var classes = "conteneur"

  if (props.notext) classes += " notext natural"

  return classes
})

const computedStyle = computed(() => {
  const c = props.conteneur

  const style = {
    backgroundColor: c.conteneur_fond ? `#${c.conteneur_fond}` : 'transparent',
    borderRadius: c.conteneur_rayon || '0px',
    color: `#${c.conteneur_couleur}`,
    textAlign: computeTextAlign(c.conteneur_aligne),
    fontFamily: c.conteneur_police_texte || 'inherit',
    padding: c.conteneur_marges || '0.1em',
    justifyContent: computeJustify(c.conteneur_aligne),
    alignItems: computeAlign(c.conteneur_aligne),
  }

  return style
})

const computedImage = computed(() => {
  const c = props.conteneur
  const notext = props.notext

  const style = {
    borderRadius: c.conteneur_rayon || '0px',
    width: c.conteneur_largeur || '100%',
    justifyContent: computeJustify(c.conteneur_aligne),
    alignItems: computeAlign(c.conteneur_aligne),
  }

  if (c.conteneur_ombre) {
    style.boxShadow = `0 4px 8px #${c.conteneur_ombre}`
  }

  if (c.conteneur_bordure) {
    style.border = `1px solid #${c.conteneur_bordure}`
  }

  if (c.conteneur_photo_url) {
    style.backgroundImage = `url('/photos/${c.conteneur_photo_url}')`
    style.backgroundSize = 'cover'
    style.backgroundPosition = 'center'
    if (notext) {
      var image = new Image()
      image.src = `/photos/${c.conteneur_photo_url}`
      var width = image.naturalWidth
      var height = image.naturalHeight
      style.aspectRatio = `${width} / ${height}`
    }
  }

  return style
})

function computeTextAlign(aligne) {
  aligne = Number(aligne)
  if ([1, 4, 7].includes(aligne)) return 'left'
  if ([2, 5, 8].includes(aligne)) return 'center'
  if ([3, 6, 9].includes(aligne)) return 'right'
  return 'center'
}

function computeJustify(aligne) {
  aligne = Number(aligne)
  if ([1, 2, 3].includes(aligne)) return 'flex-start'
  if ([4, 5, 6].includes(aligne)) return 'center'
  if ([7, 8, 9].includes(aligne)) return 'flex-end'
  return 'center'
}

function computeAlign(aligne) {
  aligne = Number(aligne)
  if ([1, 4, 7].includes(aligne)) return 'flex-start'
  if ([2, 5, 8].includes(aligne)) return 'center'
  if ([3, 6, 9].includes(aligne)) return 'flex-end'
  return 'center'
}
</script>

<template>
  <div :class="computedClass" :style="computedImage">
    <div :style="computedStyle">
      <slot></slot>
    </div>
  </div>
</template>

<style scoped>
.conteneur {
  width: 100%;
  word-wrap: break-word;
}
</style>
