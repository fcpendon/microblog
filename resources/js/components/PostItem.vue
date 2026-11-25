<script setup>
import ImagePlaceholder from './ImagePlaceholder.vue';
import CommentList from './CommentList.vue';
import PostDelete from './PostDelete.vue';
import PostEdit from './PostEdit.vue';
import PostLike from './PostLike.vue';

defineProps({ post: { type: Object, required: true } });
const emit = defineEmits(['postUpdated', 'postDeleted']);
</script>

<template>
  <article class="w-full border-b border-[var(--color-line)] bg-white px-4 py-5 sm:px-6">
    <div class="flex items-start gap-3.5">
      <RouterLink :to="{ name: 'profile', params: { username: post.author.username } }"
        class="shrink-0 rounded-full focus-visible:outline-offset-4">
        <ImagePlaceholder :image="post.author.public_image_path" :maxSize="44" />
      </RouterLink>
      <div class="min-w-0 flex-1">
        <div class="flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
          <RouterLink :to="{ name: 'profile', params: { username: post.author.username } }"
            class="text-sm font-bold text-[var(--color-ink)] no-underline hover:underline">{{
              post.author.name }}</RouterLink>
          <RouterLink :to="{ name: 'profile', params: { username: post.author.username } }"
            class="text-xs text-[var(--color-muted)] no-underline hover:underline">@{{ post.author.username
            }}</RouterLink>
          <time class="ml-auto mt-0.5 block text-xs text-[var(--color-muted)]">{{ post.created_at }}</time>
        </div>
        <p class="mb-0 mt-3 whitespace-pre-wrap break-words text-[15px] leading-6 text-[var(--color-ink)]">{{
          post.content }}</p>
        <div v-if="post.images.length" class="mt-4 space-y-2 overflow-hidden rounded-2xl">
          <img v-for="image in post.images" :key="image.id"
            class="max-h-[34rem] w-full bg-[var(--color-canvas)] object-contain" :src="image.public_image_path"
            alt="Image attached to post" loading="lazy" />
        </div>
        <CommentList :post="post">
          <template #like>
            <PostLike :post="post" />
          </template>
          <template v-if="post.is_mine" #my-controls>
            <PostEdit :post="post" @post-updated="emit('postUpdated', $event)" />
            <PostDelete :post="post" @post-deleted="emit('postDeleted', $event)" />
          </template>
        </CommentList>
      </div>
    </div>
  </article>
</template>
