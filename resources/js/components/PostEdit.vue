<script setup>
import { Pencil } from '@lucide/vue';
import { ref } from 'vue';
import PostForm from './PostForm.vue';

defineProps({ post: { type: Object, required: true } });
const emit = defineEmits(['postUpdated']);
const dialog = ref(false);
</script>

<template>
  <button type="button"
    class="grid size-9 place-items-center rounded-full text-[var(--color-muted)] hover:bg-[var(--color-canvas)] hover:text-[var(--color-brand)]"
    aria-label="Edit post" title="Edit post" @click="dialog = true">
    <Pencil :size="17" aria-hidden="true" />
  </button>
  <Teleport to="body">
    <div v-if="dialog"
      class="fixed inset-0 z-[60] grid place-items-center overflow-y-auto bg-[var(--color-ink)]/45 p-4"
      @keydown.esc="dialog = false">
      <section class="my-auto w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl" role="dialog"
        aria-modal="true" aria-label="Edit post">
        <PostForm :post="post" @close-dialog="dialog = false" @post-saved="emit('postUpdated', $event)" />
      </section>
    </div>
  </Teleport>
</template>
