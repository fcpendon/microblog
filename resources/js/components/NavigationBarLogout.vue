<template>
  <div>
    <button type="button" class="inline-flex size-10 items-center justify-center gap-2 rounded-xl text-sm font-semibold text-[var(--color-muted)] no-underline hover:bg-[var(--color-canvas)] hover:text-[var(--color-ink)] sm:w-auto sm:px-3" aria-label="Logout" title="Logout" @click="dialog = true">
      <LogOut :size="18" aria-hidden="true" />
      <span class="hidden lg:inline">Logout</span>
    </button>
    <Teleport to="body">
      <div v-if="dialog" class="fixed inset-0 z-[60] grid place-items-center bg-[var(--color-ink)]/45 p-4" @click.self="dialog = false" @keydown.esc="dialog = false">
        <section class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="logout-title">
          <h2 id="logout-title" class="m-0 text-lg font-bold text-[var(--color-ink)]">Sign out?</h2>
          <p class="mb-0 mt-2 text-sm leading-6 text-[var(--color-muted)]">You can sign in again at any time.</p>
          <div class="mt-6 flex justify-end gap-2">
            <button type="button" class="h-10 rounded-xl px-4 text-sm font-semibold text-[var(--color-muted)] hover:bg-[var(--color-canvas)]" @click="dialog = false">Cancel</button>
            <button type="button" class="h-10 rounded-xl bg-[var(--color-ink)] px-4 text-sm font-semibold text-white hover:bg-black disabled:opacity-60" :disabled="disableLogout" @click="logout">Sign out</button>
          </div>
        </section>
      </div>
    </Teleport>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import { LogOut } from '@lucide/vue';

export default {
  components: { LogOut },
  data () {
    return {
      dialog: false,
      disableLogout: false
    }
  },
  methods: {
    ...mapActions({
      getUser: 'getUser',
      setAlertSuccess: 'setAlertSuccess',
    }),
    logout () {
      this.disableLogout = true;

      axios.post('/api/logout')
        .then(res => {
          this.dialog = false;
          this.getUser({ force: true })
            .then(() => {
              this.setAlertSuccess(res.data.message);
              this.$router.push({name: 'login'});
            });
        })
        .catch(err => {
          console.log(err);
          this.disableLogout = false;
        });
    },
  }
}
</script>
