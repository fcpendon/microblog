<script setup>
import { Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import { useNotificationStore } from '../stores/notification';

const props = defineProps({ comment: { type: Object, required: true } });
const emit = defineEmits(['commentDeleted']);
const notifications = useNotificationStore();
const dialog = ref(false);
const disableDelete = ref(false);

async function deleteComment() {
  disableDelete.value = true;

  try {
    const response = await axios.delete(`/api/comments/${props.comment.id}`);
    notifications.success(response.data.message);
    dialog.value = false;
    emit('commentDeleted', props.comment.id);
  } catch (error) {
    notifications.error(error.response?.data || 'Could not delete the comment. Please try again.');
  } finally {
    disableDelete.value = false;
  }
}
</script>

<template>
  <button type="button"
    class="grid size-8 place-items-center rounded-full text-[var(--color-muted)] hover:bg-red-50 hover:text-[var(--color-danger)]"
    aria-label="Delete comment" title="Delete comment" @click="dialog = true">
    <Trash2 :size="15" aria-hidden="true" />
  </button>
  <Teleport to="body">
    <div v-if="dialog" class="fixed inset-0 z-[60] grid place-items-center bg-[var(--color-ink)]/45 p-4"
      @keydown.esc="dialog = false">
      <section class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" role="dialog" aria-modal="true"
        aria-labelledby="delete-comment-title">
        <h2 id="delete-comment-title" class="m-0 text-lg font-bold text-[var(--color-ink)]">Delete comment?</h2>
        <p class="mb-0 mt-2 text-sm leading-6 text-[var(--color-muted)]">This comment will be permanently removed.
          This action cannot be undone.</p>
        <div class="mt-6 flex justify-end gap-2">
          <button type="button"
            class="h-10 rounded-xl px-4 text-sm font-semibold text-[var(--color-muted)] hover:bg-[var(--color-canvas)]"
            @click="dialog = false">
            Cancel
          </button>
          <button type="button"
            class="h-10 rounded-xl bg-[var(--color-danger)] px-4 text-sm font-semibold text-white hover:bg-red-800 disabled:opacity-60"
            :disabled="disableDelete" @click="deleteComment">
            Delete
          </button>
        </div>
      </section>
    </div>
  </Teleport>
</template>
