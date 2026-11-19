<script setup>
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useNotificationStore } from '../stores/notification';

const route = useRoute();
const router = useRouter();
const notifications = useNotificationStore();
const form = ref({
  email: route.query.email || '',
  password: '',
  password_confirmation: '',
});
const isSubmitting = ref(false);

async function resetPassword() {
  if (form.value.password !== form.value.password_confirmation) {
    notifications.error('The passwords do not match.');
    return;
  }

  isSubmitting.value = true;

  try {
    const response = await axios.post('/api/reset-password', {
      ...form.value,
      token: route.params.token,
    });

    notifications.success(response.data.message);
    router.push({ name: 'login' });
  } catch (error) {
    notifications.error(error.response?.data || 'Could not reset the password. Request a new link and try again.');
  } finally {
    isSubmitting.value = false;
  }
}
</script>

<template>
  <section class="mx-auto w-full max-w-lg rounded-3xl border border-[var(--color-line)] bg-white p-6 shadow-sm sm:p-10">
    <p class="m-0 text-xs font-bold uppercase tracking-[0.14em] text-[var(--color-brand)]">Account recovery</p>
    <h1 class="mb-0 mt-1 text-2xl font-bold tracking-tight text-[var(--color-ink)]">Choose a new password</h1>
    <form class="mt-6 space-y-4" @submit.prevent="resetPassword">
      <div>
        <label for="reset-email" class="mb-1.5 block text-sm font-semibold text-[var(--color-ink)]">Email</label>
        <input id="reset-email" v-model="form.email" type="email" autocomplete="email" required maxlength="100"
          class="block h-11 w-full rounded-xl border border-[var(--color-line)] px-3 text-sm outline-none focus:border-[var(--color-brand)]" />
      </div>
      <div>
        <label for="reset-password" class="mb-1.5 block text-sm font-semibold text-[var(--color-ink)]">New
          password</label>
        <input id="reset-password" v-model="form.password" type="password" autocomplete="new-password" required
          minlength="8" maxlength="16"
          class="block h-11 w-full rounded-xl border border-[var(--color-line)] px-3 text-sm outline-none focus:border-[var(--color-brand)]" />
      </div>
      <div>
        <label for="reset-password-confirmation"
          class="mb-1.5 block text-sm font-semibold text-[var(--color-ink)]">Confirm new password</label>
        <input id="reset-password-confirmation" v-model="form.password_confirmation" type="password"
          autocomplete="new-password" required minlength="8" maxlength="16"
          class="block h-11 w-full rounded-xl border border-[var(--color-line)] px-3 text-sm outline-none focus:border-[var(--color-brand)]" />
      </div>
      <button type="submit"
        class="h-11 w-full rounded-xl bg-[var(--color-brand)] text-sm font-bold text-white hover:bg-[var(--color-brand-dark)] disabled:opacity-60"
        :disabled="isSubmitting">
        {{ isSubmitting ? 'Updating…' : 'Reset password' }}
      </button>
      <p class="mb-0 text-center text-sm">
        <RouterLink :to="{ name: 'login' }"
          class="font-semibold text-[var(--color-brand)] no-underline hover:underline">Back to sign in</RouterLink>
      </p>
    </form>
  </section>
</template>
