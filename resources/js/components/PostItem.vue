<script setup>
import { computed } from 'vue';
import { storeToRefs } from 'pinia';
import ImagePlaceholder from './ImagePlaceholder.vue';
import PostComment from './PostComment.vue';
import PostDelete from './PostDelete.vue';
import PostEdit from './PostEdit.vue';
import PostLike from './PostLike.vue';
import { useAuthStore } from '../stores/auth';

const props = defineProps({ post: { type: Object, required: true } });
const emit = defineEmits(['postDeleted', 'postsChanged']);
const auth = useAuthStore();
const { user } = storeToRefs(auth);
const isOwnPost = computed(() => user.value.username === props.post.author.username);
</script>

<template>
  <article class="mb-4 w-full border-b border-[var(--color-line)] bg-white px-4 py-5 sm:px-6">
    <div class="flex items-start gap-3.5">
      <RouterLink :to="{ name: 'profile', params: { username: post.author.username } }"
        class="shrink-0 rounded-full focus-visible:outline-offset-4">
        <ImagePlaceholder :image="post.author.public_image_path" :maxSize="44" />
      </RouterLink>
      <div class="min-w-0 flex-1">
        <div class="flex min-w-0 items-start justify-between gap-3">
          <div class="min-w-0">
            <div class="flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
              <RouterLink :to="{ name: 'profile', params: { username: post.author.username } }"
                class="truncate text-sm font-bold text-[var(--color-ink)] no-underline hover:underline">{{
                  post.author.name }}</RouterLink>
              <RouterLink :to="{ name: 'profile', params: { username: post.author.username } }"
                class="truncate text-xs text-[var(--color-muted)] no-underline hover:underline">@{{ post.author.username
                }}</RouterLink>
            </div>
            <time class="mt-0.5 block text-xs text-[var(--color-muted)]">{{ post.created_at }}</time>
          </div>
          <div v-if="isOwnPost" class="flex shrink-0 items-center gap-1">
            <PostEdit :post="post" @postsChanged="emit('postsChanged')" />
            <PostDelete :post="post" @postDeleted="emit('postDeleted')" />
          </div>
        </div>
        <p class="mb-0 mt-3 whitespace-pre-wrap break-words text-[15px] leading-6 text-[var(--color-ink)]">{{
          post.content }}</p>
        <div v-if="post.images.length" class="mt-4 space-y-2 overflow-hidden rounded-2xl">
          <img v-for="image in post.images" :key="image.id"
            class="max-h-[34rem] w-full bg-[var(--color-canvas)] object-contain" :src="image.public_image_path"
            alt="Image attached to post" loading="lazy" />
        </div>
        <PostComment :post="post">
          <template #like>
            <PostLike :post="post" />
          </template>
        </PostComment>
      </div>
    </div>
  </article>
</template>
