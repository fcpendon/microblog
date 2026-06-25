<template>
  <div class="d-flex align-center">
    <v-btn
      v-if="isLiked"
      @click="unlikePost"
      plain
      fab
      small
    >
      <v-icon>mdi-thumb-up</v-icon>
    </v-btn>
    <v-btn
      v-else
      @click="likePost"
      plain
      fab
      small
    >
      <v-icon>mdi-thumb-up-outline</v-icon>
    </v-btn>
    <span>{{ likeCounter }}</span>
  </div>
</template>

<script>
export default {
  props: {
    post: Object,
  },
  data () {
    return {
      isLiked: this.post.is_liked,
      likeCounter: this.post.likes,
    }
  },
  methods: {
    likePost () {
      axios.post('/api/posts/' + this.post.id + '/like')
        .then(res => {
          this.isLiked = res.data.data.is_liked;
          this.likeCounter = res.data.data.likes;
        })
        .catch(err => {
          console.log(err);
        });
    },
    unlikePost () {
      axios.delete('/api/posts/' + this.post.id + '/like')
        .then(res => {
          this.isLiked = res.data.data.is_liked;
          this.likeCounter = res.data.data.likes;
        })
        .catch(err => {
          console.log(err);
        });
    }
  }
}
</script>
