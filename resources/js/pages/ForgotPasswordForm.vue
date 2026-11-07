<template>
  <section class="mx-auto w-full max-w-lg rounded-3xl border border-[var(--color-line)] bg-white p-6 shadow-sm sm:p-10">
    <p class="m-0 text-xs font-bold uppercase tracking-[0.14em] text-[var(--color-brand)]">Account recovery</p>
    <h1 class="mb-0 mt-1 text-2xl font-bold tracking-tight text-[var(--color-ink)]">Forgot your password?</h1>
    <p class="mt-3 text-sm leading-6 text-[var(--color-muted)]">Enter the email address linked to your account. If it exists, we'll send a reset link.</p>
    <form class="mt-6 space-y-4" @submit.prevent="sendResetLink">
      <div>
        <label for="reset-email" class="mb-1.5 block text-sm font-semibold text-[var(--color-ink)]">Email</label>
        <input id="reset-email" v-model="email" type="email" autocomplete="email" required maxlength="100" class="block h-11 w-full rounded-xl border border-[var(--color-line)] px-3 text-sm outline-none focus:border-[var(--color-brand)]" />
      </div>
      <button type="submit" class="h-11 w-full rounded-xl bg-[var(--color-brand)] text-sm font-bold text-white hover:bg-[var(--color-brand-dark)] disabled:opacity-60" :disabled="isSubmitting">
        {{ isSubmitting ? 'Sending…' : 'Send reset link' }}
      </button>
      <p v-if="message" class="m-0 text-sm text-[var(--color-muted)]" role="status">{{ message }}</p>
      <p class="mb-0 text-center text-sm"><router-link :to="{ name: 'login' }" class="font-semibold text-[var(--color-brand)] no-underline hover:underline">Back to sign in</router-link></p>
    </form>
  </section>
</template>

<script>
import { mapActions } from 'vuex';

export default {
  data () {
    return {
      email: '',
      isSubmitting: false,
      message: '',
    }
  },
  methods: {
    ...mapActions({
      setAlertError: 'setAlertError',
    }),
    sendResetLink () {
      this.isSubmitting = true;
      this.message = '';

      axios.post('/api/forgot-password', { email: this.email })
        .then(res => {
          this.message = res.data.message;
        })
        .catch(err => {
          this.setAlertError(err.response?.data || 'Could not send a reset link. Please try again.');
        })
        .finally(() => {
          this.isSubmitting = false;
        });
    },
  },
}
</script>
