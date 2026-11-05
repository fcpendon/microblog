<template>
  <article class="mb-4 w-full border-b border-[var(--color-line)] bg-white px-4 py-5 sm:px-6">
    <div class="flex items-start gap-3.5">
      <router-link :to="{ name: 'profile', params: { username: post.author.username }}" class="shrink-0 rounded-full focus-visible:outline-offset-4">
        <image-placeholder :image="post.author.public_image_path" :maxSize="44" />
      </router-link>
      <div class="min-w-0 flex-1">
        <div class="flex min-w-0 items-start justify-between gap-3">
          <div class="min-w-0">
            <div class="flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
              <router-link :to="{ name: 'profile', params: { username: post.author.username }}" class="truncate text-sm font-bold text-[var(--color-ink)] no-underline hover:underline">{{ post.author.name }}</router-link>
              <router-link :to="{ name: 'profile', params: { username: post.author.username }}" class="truncate text-xs text-[var(--color-muted)] no-underline hover:underline">@{{ post.author.username }}</router-link>
            </div>
            <time class="mt-0.5 block text-xs text-[var(--color-muted)]">{{ post.posted_at }}</time>
          </div>
          <div v-if="isOwnPost(post.author.username)" class="flex shrink-0 items-center gap-1">
        <post-edit :post="post"></post-edit>
        <post-delete :post="post"></post-delete>
      </div>
        </div>
        <p class="mb-0 mt-3 whitespace-pre-wrap break-words text-[15px] leading-6 text-[var(--color-ink)]">{{ post.content }}</p>
        <div v-if="post.images.length" class="mt-4 space-y-2 overflow-hidden rounded-2xl">
          <img
            v-for="image in post.images"
            :key="image.id"
            class="max-h-[34rem] w-full bg-[var(--color-canvas)] object-contain"
            :src="image.public_image_path"
            alt="Image attached to post"
            loading="lazy"
          />
        </div>
        <div class="mt-3 flex items-center justify-between border-t border-[var(--color-line)] pt-2">
          <post-like :post="post"></post-like>
        </div>
      </div>
    </div>
  </article>
</template>

<script>
import ImagePlaceholder from './ImagePlaceholder.vue';
import PostEdit from './PostEdit.vue';
import PostDelete from './PostDelete.vue';
import PostLike from './PostLike.vue';

export default {
  components: {
    ImagePlaceholder,
    PostEdit,
    PostDelete,
    PostLike,
  },
  props: ['post'],
  methods: {
    isOwnPost (username) {
      return this.$store.state.user.username === username;
    }
  }
}
</script>
