<script setup>
import { storeToRefs } from 'pinia';
import { ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { useNotificationStore } from '../stores/notification';

const router = useRouter();
const auth = useAuthStore();
const notifications = useNotificationStore();
const { user } = storeToRefs(auth);
const email = ref('');
const isSaving = ref(false);
const isSending = ref(false);

watch(
  () => user.value.email,
  value => {
    email.value = value || '';
  },
  { immediate: true },
);

async function updateEmail() {
  isSaving.value = true;

  try {
    const response = await axios.post('/api/settings/details', {
      name: user.value.name,
      username: user.value.username,
      email: email.value,
    });

    await auth.fetchUser({ force: true });
    notifications.success(`${response.data.message} Check your inbox for the verification link.`);
  } catch (error) {
    notifications.error(error.response?.data || 'Could not update your email. Please try again.');
  } finally {
    isSaving.value = false;
  }
}

async function resend() {
  isSending.value = true;

  try {
    const response = await axios.post('/api/email/verification-notification');

    notifications.success(response.data.message);
  } catch (error) {
    notifications.error(error.response?.data || 'Could not send a verification link. Please try again.');
  } finally {
    isSending.value = false;
  }
}

async function logout() {
  try {
    await axios.post('/api/logout');
    await auth.fetchUser({ force: true });
    router.push({ name: 'login' });
  } catch (error) {
    notifications.error(error.response?.data || 'Could not sign out. Please try again.');
  }
}
</script>

<template>
  <section class="mx-auto w-full max-w-lg rounded-3xl border border-[var(--color-line)] bg-white p-6 shadow-sm sm:p-10">
    <p class="m-0 text-xs font-bold uppercase tracking-[0.14em] text-[var(--color-brand)]">Verify your account</p>
    <h1 class="mb-0 mt-1 text-2xl font-bold tracking-tight text-[var(--color-ink)]">Check your email</h1>
    <p class="mt-3 text-sm leading-6 text-[var(--color-muted)]">
      Confirm your email address to start using Microblog. We sent a verification link to
      <strong class="text-[var(--color-ink)]">{{ user.email || 'your email address' }}</strong>.
    </p>

    <form class="mt-6 space-y-3" @submit.prevent="updateEmail">
      <label for="verification-email" class="block text-sm font-semibold text-[var(--color-ink)]">
        {{ user.email ? 'Change email address' : 'Add an email address' }}
      </label>
      <input id="verification-email" v-model="email" type="email" autocomplete="email" required maxlength="100"
        class="block h-11 w-full rounded-xl border border-[var(--color-line)] px-3 text-sm outline-none focus:border-[var(--color-brand)]" />
      <button type="submit"
        class="h-11 w-full rounded-xl border border-[var(--color-line)] text-sm font-bold text-[var(--color-ink)] hover:bg-[var(--color-canvas)] disabled:opacity-60"
        :disabled="isSaving || email === user.email">
        {{ isSaving ? 'Saving…' : 'Update email and send link' }}
      </button>
    </form>

    <button type="button"
      class="mt-3 h-11 w-full rounded-xl bg-[var(--color-brand)] text-sm font-bold text-white hover:bg-[var(--color-brand-dark)] disabled:opacity-60"
      :disabled="isSending || !user.email" @click="resend">
      {{ isSending ? 'Sending…' : 'Resend verification link' }}
    </button>
    <button type="button"
      class="mt-4 w-full text-center text-sm font-semibold text-[var(--color-muted)] hover:text-[var(--color-ink)]"
      @click="logout">
      Sign out
    </button>
  </section>
</template>
