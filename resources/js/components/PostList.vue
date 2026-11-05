<template>
  <div class="w-full">
    <div
      v-for="post in posts"
      :key="post.id"
    >
      <post-item :post="post"></post-item>
    </div>
    <div v-if="isLoading" class="flex justify-center py-10" role="status" aria-label="Loading posts">
      <span class="size-8 animate-spin rounded-full border-2 border-[var(--color-line)] border-t-[var(--color-brand)]"></span>
    </div>
    <p v-if="isEmptyResult && posts.length" class="pb-8 text-center text-xs font-semibold uppercase tracking-wider text-[var(--color-muted)]">You're all caught up</p>
    <div v-if="isEmptyResult && !posts.length && !isLoading" class="my-6 rounded-2xl border border-dashed border-[var(--color-line)] bg-white px-6 py-12 text-center">
      <p class="m-0 text-sm font-semibold text-[var(--color-ink)]">Nothing here yet</p>
      <p class="mb-0 mt-1 text-sm text-[var(--color-muted)]">New posts will show up in this feed.</p>
    </div>
  </div>
</template>

<script>
import PostItem from './PostItem.vue';
import emitter from '../utils/events';

export default {
  components: {
    PostItem,
  },
  data () {
    return {
      posts: [],
      isLoading: true,
      isEmptyResult: false,
    }
  },
  created () {
    this.fetchLatestPosts();
  },
  mounted () {
    this.listenScroll();
    emitter.on('reloadPosts', () => {
      this.isEmptyResult = false;
      this.fetchLatestPosts(true);
    });
  },
  methods: {
    fetchLatestPosts (reset = false) {
      this.isLoading = true;

      axios.get('/api/posts/' + (this.$route.params.username ?? ''), {
        params: {
          offset: reset ? 0 : this.posts.length,
        }
      })
        .then(res => {
          let newPosts = res.data.data;
          if (newPosts.length === 0) {
            this.isEmptyResult = true;
          } else {
            this.posts = reset ? newPosts : this.posts.concat(newPosts);
          }
        })
        .catch(err => {
          console.log(err);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    listenScroll () {
      window.onscroll = () => {
        let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight;
        if (!this.isEmptyResult && !this.isLoading && bottomOfWindow) {
          this.fetchLatestPosts();
        }
      }
    }
  }
}
</script>
