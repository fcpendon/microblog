<template>
  <v-card
    class="mb-10 pa-4"
    width="100%"
    tile
  >
    <v-row>
      <v-col cols="2">
        <router-link
          :to="{ name: 'profile', params: { username: post.author.username }}"
          class="text-decoration-none"
        >
          <image-placeholder
            :image="post.author.public_image_path"
            :maxSize="100"
          ></image-placeholder>
        </router-link>
      </v-col>
      <v-col cols="10">
        <v-card
          class="ml-4"
          max-width="500"
          flat
        >
          <v-card-subtitle class="pa-0">
            <router-link
              :to="{ name: 'profile', params: { username: post.author.username }}"
              class="text-decoration-none font-weight-bold"
            >{{ post.author.name }}</router-link>
            posted
            {{ post.posted_at }}
          </v-card-subtitle>
          <v-card-text class="pa-0 text-h5">{{ post.content }}</v-card-text>
          </v-card>
      </v-col>
    </v-row>
    <v-row v-if="post.images.length">
      <v-col>
        <v-img
          v-for="image in post.images"
          :key="image.id"
          :src="image.public_image_path"
          max-width="570"
          max-height="570"
        ></v-img>
      </v-col>
    </v-row>
    <v-card-actions class="mb-n4 mx-n4 justify-space-between">
      <tweet-post-like :post="post"></tweet-post-like>
      <div v-if="isOwnPost(post.author.username)">
        <tweet-post-edit :post="post"></tweet-post-edit>
        <tweet-post-delete :post="post"></tweet-post-delete>
      </div>
    </v-card-actions>
  </v-card>
</template>

<script>
import ImagePlaceholder from './ImagePlaceholder';
import TweetPostEdit from './TweetPostEdit';
import TweetPostDelete from './TweetPostDelete';
import TweetPostLike from './TweetPostLike';
import {mapActions} from 'vuex';

export default {
  components: {
    ImagePlaceholder,
    TweetPostEdit,
    TweetPostDelete,
    TweetPostLike,
  },
  props: ['post'],
  data () {
    return {
      editDialog: false,
      deleteDialog: false,
    }
  },
  methods: {
    ...mapActions({
      setAlertSuccess: 'setAlertSuccess',
      setAlertError: 'setAlertError',
    }),
    isOwnPost (username) {
      return this.$store.state.user.username === username;
    }
  }
}
</script>
