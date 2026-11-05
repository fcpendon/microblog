<template>
  <div class="inline-flex items-center gap-1.5">
    <button
      type="button"
      class="inline-flex h-9 items-center gap-2 rounded-full px-3 text-sm font-semibold transition-colors"
      :class="isLiked ? 'bg-[var(--color-highlight)] text-[var(--color-brand-dark)]' : 'text-[var(--color-muted)] hover:bg-[var(--color-canvas)] hover:text-[var(--color-brand)]'"
      :aria-label="isLiked ? 'Unlike post' : 'Like post'"
      :aria-pressed="isLiked"
      @click="isLiked ? unlikePost() : likePost()"
    >
      <Heart :size="17" :fill="isLiked ? 'currentColor' : 'none'" aria-hidden="true" />
      <span>{{ likeCounter }}</span>
    </button>
  </div>
</template>

<script>
import { Heart } from '@lucide/vue';

export default {
  components: { Heart },
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
