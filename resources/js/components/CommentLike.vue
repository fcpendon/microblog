<script setup>
import { Heart } from '@lucide/vue';
import { toRef } from 'vue';
import { useLike } from '../composables/useLike';

const props = defineProps({ comment: { type: Object, required: true } });
const { isLiked, likeCount, isLoading, toggleLike } = useLike(
  toRef(props, 'comment'),
  comment => `/api/comments/${comment.id}/like`,
  'Could not update the comment like. Please try again.',
);
</script>

<template>
  <button type="button"
    class="inline-flex h-9 items-center gap-2 rounded-full px-3 text-sm font-semibold transition-colors disabled:cursor-wait disabled:opacity-60"
    :class="isLiked ? 'bg-[var(--color-highlight)] text-[var(--color-brand-dark)]' : 'text-[var(--color-muted)] hover:bg-[var(--color-canvas)] hover:text-[var(--color-brand)]'"
    :aria-label="isLiked ? 'Unlike comment' : 'Like comment'" :aria-pressed="isLiked" :disabled="isLoading"
    @click="toggleLike">
    <Heart :size="15" :fill="isLiked ? 'currentColor' : 'none'" aria-hidden="true" />
    <span>{{ likeCount }}</span>
  </button>
</template>
