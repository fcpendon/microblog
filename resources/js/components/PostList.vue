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
    <div v-if="errorMessage" class="px-5 py-8 text-center" role="alert">
      <p class="m-0 text-sm text-[var(--color-danger)]">{{ errorMessage }}</p>
      <button type="button" class="mt-3 rounded-lg px-3 py-2 text-sm font-bold text-[var(--color-brand)] hover:bg-[var(--color-highlight)]" @click="fetchLatestPosts">Try again</button>
    </div>
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
      errorMessage: '',
      reloadPostsHandler: null,
      scrollHandler: null,
    }
  },
  created () {
    this.fetchLatestPosts();
  },
  mounted () {
    this.scrollHandler = () => {
      const nearBottom = window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 200;
      if (nearBottom && !this.isEmptyResult && !this.isLoading) {
        this.fetchLatestPosts();
      }
    };
    window.addEventListener('scroll', this.scrollHandler, { passive: true });

    this.reloadPostsHandler = () => {
      this.isEmptyResult = false;
      this.posts = [];
      this.fetchLatestPosts(true);
    };
    emitter.on('reloadPosts', this.reloadPostsHandler);
  },
  beforeUnmount () {
    if (this.scrollHandler) window.removeEventListener('scroll', this.scrollHandler);
    if (this.reloadPostsHandler) emitter.off('reloadPosts', this.reloadPostsHandler);
  },
  methods: {
    fetchLatestPosts (reset = false) {
      this.isLoading = true;
      this.errorMessage = '';
      if (reset) this.isEmptyResult = false;

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
        .catch(() => {
          this.errorMessage = 'Could not load posts. Check your connection and try again.';
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
  }
}
</script>
