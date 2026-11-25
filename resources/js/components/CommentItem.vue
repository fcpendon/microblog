<script setup>
import { ref } from 'vue';
import ImagePlaceholder from './ImagePlaceholder.vue';
import CommentDelete from './CommentDelete.vue';
import CommentEdit from './CommentEdit.vue';
import CommentLike from './CommentLike.vue';

defineProps({ comment: { type: Object, required: true } });
const emit = defineEmits(['commentDeleted', 'commentUpdated']);
const isEditing = ref(false);
const editTarget = ref(null);

function updateComment(comment) {
  isEditing.value = false;
  emit('commentUpdated', comment);
}
</script>

<template>
  <article class="flex items-start gap-3">
    <RouterLink :to="{ name: 'profile', params: { username: comment.author.username } }"
      class="shrink-0 rounded-full focus-visible:outline-offset-4">
      <ImagePlaceholder :image="comment.author.public_image_path" :max-size="36" />
    </RouterLink>
    <div class="min-w-0 flex-1">
      <div class="flex flex-wrap items-baseline gap-x-2">
        <RouterLink :to="{ name: 'profile', params: { username: comment.author.username } }"
          class="text-sm font-semibold text-[var(--color-ink)] no-underline hover:underline">
          {{ comment.author.name }}
        </RouterLink>
        <RouterLink :to="{ name: 'profile', params: { username: comment.author.username } }"
          class="text-xs text-[var(--color-muted)] no-underline hover:underline">
          @{{ comment.author.username }}
        </RouterLink>
        <time class="ml-auto mt-0.5 block text-xs text-[var(--color-muted)]">{{ comment.created_at }}</time>
      </div>
      <p v-if="!isEditing" class="mb-0 mt-1 whitespace-pre-wrap break-words text-sm leading-5 text-[var(--color-ink)]">
        {{ comment.content }}
      </p>
      <div ref="editTarget"></div>
      <div class="mt-1 flex items-center justify-between gap-2">
        <CommentLike :comment="comment" />
        <div v-if="comment.is_mine" class="flex items-center gap-1">
          <CommentEdit v-if="editTarget" :comment="comment" :target="editTarget" @editing-changed="isEditing = $event"
            @comment-updated="updateComment" />
          <CommentDelete v-if="!isEditing" :comment="comment" @comment-deleted="emit('commentDeleted', $event)" />
        </div>
      </div>
    </div>
  </article>
</template>
