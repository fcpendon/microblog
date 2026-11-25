<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useFormValidation } from '../composables/useFormValidation';
import { useAuthStore } from '../stores/auth';
import { useNotificationStore } from '../stores/notification';
import { required } from '../utils/validationRules';

const router = useRouter();
const auth = useAuthStore();
const notifications = useNotificationStore();
const form = reactive({ username: '', password: '' });
const { formElement, validateForm } = useFormValidation(form);
const disableLogin = ref(false);
const validationRules = {
  username: [required('Please enter your username')],
  password: [required('Please enter your password')],
};

async function login() {
  if (!validateForm(validationRules)) {
    return;
  }

  disableLogin.value = true;

  try {
    const response = await axios.post('/api/login', form);

    if (!await auth.fetchUser({ force: true })) {
      throw new Error('The login session could not be established.');
    }

    notifications.success(response.data.message);
    router.push({ name: 'home' });
  } catch (error) {
    notifications.error(error.response?.data || error.message);
  } finally {
    disableLogin.value = false;
  }
}
</script>

<template>
  <section
    class="mx-auto grid min-h-[min(720px,calc(100vh-8rem))] w-full max-w-4xl overflow-hidden rounded-3xl border border-[var(--color-line)] bg-white shadow-sm md:grid-cols-[0.9fr_1.1fr]">
    <div
      class="relative flex min-h-52 flex-col justify-between overflow-hidden bg-[var(--color-ink)] p-7 text-white sm:p-10 md:min-h-full">
      <RouterLink :to="{ name: 'home' }" class="relative flex items-center gap-2.5 text-white no-underline">
        <span class="grid size-9 place-items-center rounded-xl bg-[var(--color-brand)] text-sm font-black">m</span>
        <span class="text-lg font-bold">microblog</span>
      </RouterLink>
      <div class="relative mt-12 md:mb-8">
        <p class="m-0 text-xs font-bold uppercase tracking-[0.16em] text-emerald-300">Good to see you</p>
        <h1 class="mb-0 mt-3 max-w-sm text-3xl font-bold leading-tight tracking-tight sm:text-4xl">Pick up the
          conversation.</h1>
        <p class="mb-0 mt-4 max-w-sm text-sm leading-6 text-white/65">Sign in to share ideas and catch up with your
          community.</p>
      </div>
      <p class="relative mb-0 mt-8 hidden text-xs text-white/45 md:block">A thoughtful corner of the internet.</p>
    </div>
    <div class="flex items-center p-6 sm:p-10">
      <form ref="formElement" class="w-full space-y-5" @submit.prevent="login">
        <div class="mb-7">
          <p class="m-0 text-xs font-bold uppercase tracking-[0.14em] text-[var(--color-brand)]">Welcome back</p>
          <h2 class="mb-0 mt-1 text-2xl font-bold tracking-tight text-[var(--color-ink)]">Sign in</h2>
        </div>
        <div>
          <label for="login-username"
            class="mb-1.5 block text-sm font-semibold text-[var(--color-ink)]">Username</label>
          <input id="login-username" v-model="form.username" name="username" type="text" autocomplete="username"
            required autofocus
            class="block h-12 w-full rounded-xl border border-[var(--color-line)] px-3 text-sm outline-none focus:border-[var(--color-brand)] focus:ring-2 focus:ring-[var(--color-highlight)]" />
        </div>
        <div>
          <label for="login-password"
            class="mb-1.5 block text-sm font-semibold text-[var(--color-ink)]">Password</label>
          <div class="relative">
            <input id="login-password" v-model="form.password" name="password" type="password"
              autocomplete="current-password" required
              class="block h-12 w-full rounded-xl border border-[var(--color-line)] px-3 text-sm outline-none focus:border-[var(--color-brand)] focus:ring-2 focus:ring-[var(--color-highlight)]" />
          </div>
        </div>
        <p class="-mt-2 text-right text-sm">
          <RouterLink :to="{ name: 'forgot-password' }"
            class="font-semibold text-[var(--color-brand)] no-underline hover:underline">Forgot password?</RouterLink>
        </p>
        <button type="submit"
          class="mt-2 h-12 w-full rounded-xl bg-[var(--color-brand)] text-sm font-bold text-white transition hover:bg-[var(--color-brand-dark)] disabled:opacity-60"
          :disabled="disableLogin">Sign in</button>
        <p class="mb-0 pt-2 text-center text-sm text-[var(--color-muted)]">New here? <RouterLink
            :to="{ name: 'register' }" class="font-bold text-[var(--color-brand)] no-underline hover:underline">Create
            an account</RouterLink>
        </p>
      </form>
    </div>
  </section>
</template>
