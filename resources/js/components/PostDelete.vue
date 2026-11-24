<script setup>
import { Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import { useNotificationStore } from '../stores/notification';

const props = defineProps({ post: { type: Object, required: true } });
const emit = defineEmits(['postDeleted']);
const notifications = useNotificationStore();
const dialog = ref(false);
const disableDelete = ref(false);

async function deletePost() {
  disableDelete.value = true;
  try {
    const response = await axios.delete(`/api/posts/${props.post.id}`);
    notifications.success(response.data.message);
    dialog.value = false;
    emit('postDeleted');
  } catch (error) {
    notifications.error(error.response?.data || 'Could not delete the post. Please try again.');
  } finally {
    disableDelete.value = false;
  }
}
</script>

<template>
  <div class="contents">
    <button type="button"
      class="grid size-9 place-items-center rounded-full text-[var(--color-muted)] hover:bg-red-50 hover:text-[var(--color-danger)]"
      aria-label="Delete post" title="Delete post" @click="dialog = true">
      <Trash2 :size="17" aria-hidden="true" />
    </button>
    <Teleport to="body">
      <div v-if="dialog" class="fixed inset-0 z-[60] grid place-items-center bg-[var(--color-ink)]/45 p-4"
        @keydown.esc="dialog = false">
        <section class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" role="dialog" aria-modal="true"
          aria-labelledby="delete-post-title">
          <h2 id="delete-post-title" class="m-0 text-lg font-bold text-[var(--color-ink)]">Delete post?</h2>
          <p class="mb-0 mt-2 text-sm leading-6 text-[var(--color-muted)]">This post will be permanently removed. This
            action cannot be undone.</p>
          <div class="mt-6 flex justify-end gap-2">
            <button type="button"
              class="h-10 rounded-xl px-4 text-sm font-semibold text-[var(--color-muted)] hover:bg-[var(--color-canvas)]"
              @click="dialog = false">Cancel</button>
            <button type="button"
              class="h-10 rounded-xl bg-[var(--color-danger)] px-4 text-sm font-semibold text-white hover:bg-red-800 disabled:opacity-60"
              :disabled="disableDelete" @click="deletePost">Delete</button>
          </div>
        </section>
      </div>
    </Teleport>
  </div>
</template>
