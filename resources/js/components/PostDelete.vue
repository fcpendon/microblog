<template>
  <div class="contents">
    <button type="button" class="grid size-9 place-items-center rounded-full text-[var(--color-muted)] hover:bg-red-50 hover:text-[var(--color-danger)]" aria-label="Delete post" title="Delete post" @click="dialog = true">
      <Trash2 :size="17" aria-hidden="true" />
    </button>
    <Teleport to="body">
      <div v-if="dialog" class="fixed inset-0 z-[60] grid place-items-center bg-[var(--color-ink)]/45 p-4" @click.self="dialog = false" @keydown.esc="dialog = false">
        <section class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="delete-post-title">
          <h2 id="delete-post-title" class="m-0 text-lg font-bold text-[var(--color-ink)]">Delete post?</h2>
          <p class="mb-0 mt-2 text-sm leading-6 text-[var(--color-muted)]">This post will be permanently removed. This action cannot be undone.</p>
          <div class="mt-6 flex justify-end gap-2">
            <button type="button" class="h-10 rounded-xl px-4 text-sm font-semibold text-[var(--color-muted)] hover:bg-[var(--color-canvas)]" @click="dialog = false">Cancel</button>
            <button type="button" class="h-10 rounded-xl bg-[var(--color-danger)] px-4 text-sm font-semibold text-white hover:bg-red-800 disabled:opacity-60" :disabled="disableDelete" @click="deletePost">Delete</button>
          </div>
        </section>
      </div>
    </Teleport>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import { Trash2 } from '@lucide/vue';
import emitter from '../utils/events';

export default {
  components: { Trash2 },
  props: ['post'],
  data () {
    return {
      dialog: false,
      disableDelete: false,
    }
  },
  methods: {
    ...mapActions({
      setAlertSuccess: 'setAlertSuccess',
      setAlertError: 'setAlertError',
    }),
    deletePost () {
      this.disableDelete = true;

      axios.delete('/api/posts/' + this.post.id)
        .then(res => {
          this.setAlertSuccess(res.data.message);
          this.dialog = false;
          emitter.emit('reloadPosts');
        })
        .catch(err => {
          this.setAlertError(err.response?.data || 'Could not delete the post. Please try again.');
        })
        .finally(() => {
          this.disableDelete = false;
        });
    },
  }
}
</script>
