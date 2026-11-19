<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import PostItem from './PostItem.vue';

const route = useRoute();
const posts = ref([]);
const isLoading = ref(true);
const isEmptyResult = ref(false);
const errorMessage = ref('');
const postsHasUnfetched = ref(true);
const postsPerFetch = 10;

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true });
  void fetchLatestPosts();
});

onBeforeUnmount(() => {
  window.removeEventListener('scroll', handleScroll);
  handleScroll.cancel();
});

async function fetchLatestPosts(reset = false) {
  isLoading.value = true;
  errorMessage.value = '';
  if (reset) {
    isEmptyResult.value = false;
  }

  try {
    const response = await axios.get(`/api/posts/${route.params.username ?? ''}`, {
      params: { offset: reset ? 0 : posts.value.length, limit: postsPerFetch },
    });
    const newPosts = response.data.data;

    isEmptyResult.value = newPosts.length === 0;
    postsHasUnfetched.value = newPosts.length === postsPerFetch;

    if (newPosts.length) {
      posts.value = reset ? newPosts : [...posts.value, ...newPosts];
    }
  } catch {
    errorMessage.value = 'Could not load posts. Check your connection and try again.';
  } finally {
    isLoading.value = false;
  }
}

function reloadPosts() {
  posts.value = [];
  postsHasUnfetched.value = true;
  void fetchLatestPosts(true);
};

const handleScroll = _.throttle(() => {
  const nearBottom = window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 200;

  if (nearBottom && postsHasUnfetched.value && !isEmptyResult.value && !isLoading.value) {
    void fetchLatestPosts();
  }
}, 250);

defineExpose({ reloadPosts });
</script>

<template>
  <div class="w-full">
    <div v-for="post in posts" :key="post.id">
      <PostItem :post="post" @postDeleted="reloadPosts" @postsChanged="reloadPosts" />
    </div>
    <div v-if="isLoading" class="flex justify-center py-10" role="status" aria-label="Loading posts">
      <span
        class="size-8 animate-spin rounded-full border-2 border-[var(--color-line)] border-t-[var(--color-brand)]"></span>
    </div>
    <p v-if="!postsHasUnfetched && posts.length"
      class="pb-8 text-center text-xs font-semibold uppercase tracking-wider text-[var(--color-muted)]">You're all
      caught up</p>
    <div v-if="errorMessage" class="px-5 py-8 text-center" role="alert">
      <p class="m-0 text-sm text-[var(--color-danger)]">{{ errorMessage }}</p>
      <button type="button"
        class="mt-3 rounded-lg px-3 py-2 text-sm font-bold text-[var(--color-brand)] hover:bg-[var(--color-highlight)]"
        @click="fetchLatestPosts">Try again</button>
    </div>
    <div v-if="isEmptyResult && !posts.length && !isLoading"
      class="my-6 rounded-2xl border border-dashed border-[var(--color-line)] bg-white px-6 py-12 text-center">
      <p class="m-0 text-sm font-semibold text-[var(--color-ink)]">Nothing here yet</p>
      <p class="mb-0 mt-1 text-sm text-[var(--color-muted)]">New posts will show up in this feed.</p>
    </div>
  </div>
</template>
