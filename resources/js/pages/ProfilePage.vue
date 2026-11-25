<script setup>
import { storeToRefs } from 'pinia';
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import ImagePlaceholder from '../components/ImagePlaceholder.vue';
import PostForm from '../components/PostForm.vue';
import PostList from '../components/PostList.vue';
import { useAuthStore } from '../stores/auth';

const route = useRoute();
const router = useRouter();
const auth = useAuthStore();
const { user: authenticatedUser } = storeToRefs(auth);
const user = ref(null);
const postList = ref(null);

onMounted(loadProfile);

function addPost(post) {
  postList.value?.addPost(post);
}

async function loadProfile() {
  if (route.params.username === authenticatedUser.value.username) {
    user.value = authenticatedUser.value;
    return;
  }
  try {
    const response = await axios.get(`/api/profiles/${route.params.username}`);

    user.value = response.data.data;
  } catch {
    router.push({ name: '404' });
  }
}
</script>

<template>
  <div v-if="user" class="mx-auto grid w-full max-w-6xl grid-cols-1 gap-6">
    <section
      class="mx-auto w-full max-w-2xl overflow-hidden border-y border-[var(--color-line)] bg-white sm:rounded-2xl sm:border">
      <header class="border-b border-[var(--color-line)] py-4 px-6">
        <div class="flex items-center gap-4">
          <ImagePlaceholder :image="user.public_image_path" :maxSize="96" />
          <div class="min-w-0">
            <p class="m-0 text-xs font-bold uppercase tracking-[0.14em] text-[var(--color-brand)]">Profile</p>
            <h1 class="mb-0 mt-1 text-2xl font-bold tracking-tight text-[var(--color-ink)]">{{ user.name }}</h1>
            <p class="mb-0 mt-1 truncate text-sm text-[var(--color-muted)]">@{{ user.username }}</p>
          </div>
        </div>
      </header>
      <PostForm v-if="user?.username === authenticatedUser.username" class="border-b border-[var(--color-line)]"
        @post-saved="addPost" />
      <PostList ref="postList" :user="user" />
    </section>
  </div>
</template>
