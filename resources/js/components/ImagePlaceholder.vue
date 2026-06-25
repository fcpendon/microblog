<template>
  <v-sheet
    :color="image ? 'black' : 'transparent'"
    :width="maxSize"
    tile
  >
    <v-img
      v-if="image"
      :src="image"
      :width="maxSize"
      :height="maxSize"
      contain
    ></v-img>
    <v-icon
      v-else
      :size="maxSize"
    >mdi-account-box</v-icon>
  </v-sheet>
</template>

<script>
export default {
  props: {
    image: String,
    maxSize: Number,
  }
}
</script>
