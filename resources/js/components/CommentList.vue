<script setup>
import { MessageCircle } from '@lucide/vue';
import { ref, watch } from 'vue';
import { useNotificationStore } from '../stores/notification';
import CommentForm from './CommentForm.vue';
import CommentItem from './CommentItem.vue';
import ImagePlaceholder from './ImagePlaceholder.vue';

const props = defineProps({ post: { type: Object, required: true } });
const notifications = useNotificationStore();
const isOpen = ref(false);
const isLoading = ref(false);
const errorMessage = ref('');
const comments = ref([]);
const commentsCount = ref(props.post.comments_count);
const latestComment = ref(props.post.latest_comment);
let commentsVersion = 0;

watch(
  () => props.post.comments_count,
  count => {
    commentsCount.value = count;
  },
);

watch(
  () => props.post.latest_comment,
  comment => {
    latestComment.value = comment;
  },
);

async function toggleComments() {
  isOpen.value = !isOpen.value;

  if (isOpen.value) {
    await fetchComments();
  }
}

async function fetchComments() {
  const version = commentsVersion;
  isLoading.value = true;
  errorMessage.value = '';

  try {
    const response = await axios.get(`/api/posts/${props.post.id}/comments`);

    if (version === commentsVersion) {
      comments.value = response.data.data;
    }
  } catch (error) {
    if (version !== commentsVersion) {
      return;
    }
    errorMessage.value = 'Could not load comments. Please try again.';
    notifications.error(error.response?.data || errorMessage.value);
  } finally {
    if (version === commentsVersion) {
      isLoading.value = false;
    }
  }
}

function addComment(comment) {
  commentsVersion++;
  isLoading.value = false;
  comments.value.push(comment);
  commentsCount.value++;
  latestComment.value = comment;
}

function updateComment(updatedComment) {
  commentsVersion++;
  isLoading.value = false;
  const index = comments.value.findIndex(comment => comment.id === updatedComment.id);

  if (index !== -1) {
    comments.value[index] = updatedComment;
  }

  if (latestComment.value?.id === updatedComment.id) {
    latestComment.value = updatedComment;
  }
}

function deleteComment(commentId) {
  const wasLatestComment = latestComment.value?.id === commentId;

  commentsVersion++;
  isLoading.value = false;
  comments.value = comments.value.filter(comment => comment.id !== commentId);
  commentsCount.value = Math.max(0, commentsCount.value - 1);

  if (wasLatestComment) {
    latestComment.value = comments.value[0] || null;
  }
}
</script>

<template>
  <div class="mt-3 border-t border-[var(--color-line)] pt-2">
    <div class="flex justify-between">
      <div class="flex items-center gap-1">
        <slot name="like" />
        <button type="button"
          class="inline-flex h-9 items-center gap-2 rounded-full px-3 text-sm font-semibold text-[var(--color-muted)] transition-colors hover:bg-[var(--color-canvas)] hover:text-[var(--color-brand)]"
          :aria-label="isOpen ? 'Hide comments' : 'Show comments'" :aria-expanded="isOpen"
          :aria-controls="`post-comments-${post.id}`" @click="toggleComments">
          <MessageCircle :size="17" aria-hidden="true" />
          <span>{{ commentsCount }}</span>
        </button>
      </div>
      <div class="flex items-center gap-1">
        <slot name="my-controls" />
      </div>
    </div>

    <div v-if="!isOpen && latestComment" class="mt-3 flex gap-3 break-words text-sm leading-5 text-[var(--color-ink)]">
      <ImagePlaceholder :image="latestComment.author.public_image_path" :max-size="36" />
      <div class="flex flex-wrap">
        <span class="font-semibold">{{ latestComment.author.name }}</span>
        <span class="mt-1">{{ latestComment.content }}</span>
      </div>
    </div>

    <section v-if="isOpen" :id="`post-comments-${post.id}`" class="mt-3 space-y-3"
      :aria-label="`Comments on post ${post.id}`">
      <CommentItem v-for="comment in comments" :key="comment.id" :comment="comment" @comment-deleted="deleteComment"
        @comment-updated="updateComment" />
      <div v-if="isLoading" class="py-3 text-center text-sm text-[var(--color-muted)]" role="status">
        Loading comments...
      </div>
      <div v-else-if="errorMessage" class="text-sm text-[var(--color-danger)]" role="alert">
        {{ errorMessage }}
        <button type="button" class="ml-2 font-semibold underline" @click="fetchComments">Try again</button>
      </div>
      <p v-else-if="!comments.length" class="m-0 py-2 text-center text-sm text-[var(--color-muted)]">
        No comments yet. Start the conversation.
      </p>
      <CommentForm v-if="!isLoading && !errorMessage" :post="post" @comment-created="addComment" />
    </section>
  </div>
</template>
