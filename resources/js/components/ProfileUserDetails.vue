<template>
  <router-link
    :to="{ name: 'profile', params: { username: user.username }}"
    class="text-decoration-none"
  >
    <v-card
      width="300"
      tile
    >
      <image-placeholder
        :image="user.public_image_path"
        :maxSize="300"
      ></image-placeholder>
      <v-card-title class="justify-center">{{ user.name }}</v-card-title>
      <v-card-subtitle class="text-center">{{ user.username }}</v-card-subtitle>
    </v-card>
  </router-link>
</template>

<script>
import ImagePlaceholder from './ImagePlaceholder';

export default {
  components: {
    ImagePlaceholder,
  },
  props: ['user'],
}
</script>
