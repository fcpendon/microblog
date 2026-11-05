<template>
  <div class="contents">
    <button type="button" class="grid size-9 place-items-center rounded-full text-[var(--color-muted)] hover:bg-[var(--color-canvas)] hover:text-[var(--color-brand)]" aria-label="Edit post" title="Edit post" @click="dialog = true">
      <Pencil :size="17" aria-hidden="true" />
    </button>
    <Teleport to="body">
      <div v-if="dialog" class="fixed inset-0 z-[60] grid place-items-center overflow-y-auto bg-[var(--color-ink)]/45 p-4" @click.self="closeDialog" @keydown.esc="closeDialog">
        <section class="my-auto w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl" role="dialog" aria-modal="true" aria-label="Edit post">
          <post-form :post="post" @closeDialog="closeDialog"></post-form>
        </section>
      </div>
    </Teleport>
  </div>
</template>

<script>
import PostForm from './PostForm.vue';
import { Pencil } from '@lucide/vue';

export default {
  components: {
    PostForm,
    Pencil,
  },
  props: ['post'],
  data () {
    return {
      dialog: false,
    }
  },
  methods: {
    closeDialog () {
      this.dialog = false;
    }
  }
}
</script>
