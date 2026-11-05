<template>
  <section class="mx-auto grid min-h-[min(760px,calc(100vh-8rem))] w-full max-w-4xl overflow-hidden rounded-3xl border border-[var(--color-line)] bg-white shadow-sm md:grid-cols-[0.9fr_1.1fr]">
    <div class="relative flex min-h-52 flex-col justify-between overflow-hidden bg-[var(--color-ink)] p-7 text-white sm:p-10 md:min-h-full">
      <router-link :to="{ name: 'home' }" class="relative flex items-center gap-2.5 text-white no-underline">
        <span class="grid size-9 place-items-center rounded-xl bg-[var(--color-brand)] text-sm font-black">m</span><span class="text-lg font-bold">microblog</span>
      </router-link>
      <div class="relative mt-12 md:mb-8">
        <p class="m-0 text-xs font-bold uppercase tracking-[0.16em] text-emerald-300">Make your space</p>
        <h1 class="mb-0 mt-3 max-w-sm text-3xl font-bold leading-tight tracking-tight sm:text-4xl">Good ideas grow together.</h1>
        <p class="mb-0 mt-4 max-w-sm text-sm leading-6 text-white/65">Create an account and join the conversation.</p>
      </div>
      <p class="relative mb-0 mt-8 hidden text-xs text-white/45 md:block">A thoughtful corner of the internet.</p>
    </div>
    <div class="flex items-center p-6 sm:p-10">
      <form ref="form" class="w-full space-y-4" @submit.prevent="validate">
        <div class="mb-5">
          <p class="m-0 text-xs font-bold uppercase tracking-[0.14em] text-[var(--color-brand)]">Get started</p>
          <h2 class="mb-0 mt-1 text-2xl font-bold tracking-tight text-[var(--color-ink)]">Create account</h2>
        </div>
        <div>
          <label for="register-name" class="mb-1.5 block text-sm font-semibold text-[var(--color-ink)]">Full name</label>
          <input id="register-name" v-model="form.name" name="name" type="text" maxlength="50" autocomplete="name" autofocus class="block h-11 w-full rounded-xl border border-[var(--color-line)] px-3 text-sm outline-none focus:border-[var(--color-brand)] focus:ring-2 focus:ring-[var(--color-highlight)]" />
        </div>
        <div>
          <label for="register-username" class="mb-1.5 block text-sm font-semibold text-[var(--color-ink)]">Username</label>
          <input id="register-username" v-model="form.username" name="username" type="text" maxlength="20" autocomplete="username" class="block h-11 w-full rounded-xl border border-[var(--color-line)] px-3 text-sm outline-none focus:border-[var(--color-brand)] focus:ring-2 focus:ring-[var(--color-highlight)]" />
        </div>
        <div>
          <label for="register-password" class="mb-1.5 block text-sm font-semibold text-[var(--color-ink)]">Password</label>
          <div class="relative">
            <input id="register-password" v-model="form.password" name="password" :type="showPassword ? 'text' : 'password'" autocomplete="new-password" class="block h-11 w-full rounded-xl border border-[var(--color-line)] px-3 pr-12 text-sm outline-none focus:border-[var(--color-brand)] focus:ring-2 focus:ring-[var(--color-highlight)]" />
            <button type="button" class="absolute inset-y-0 right-0 grid w-12 place-items-center text-[var(--color-muted)] hover:text-[var(--color-ink)]" :aria-label="showPassword ? 'Hide password' : 'Show password'" @click="showPassword = !showPassword"><Eye v-if="!showPassword" :size="17" /><EyeOff v-else :size="17" /></button>
          </div>
        </div>
        <div>
          <label for="register-confirm" class="mb-1.5 block text-sm font-semibold text-[var(--color-ink)]">Confirm password</label>
          <input id="register-confirm" v-model="form.password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="block h-11 w-full rounded-xl border border-[var(--color-line)] px-3 text-sm outline-none focus:border-[var(--color-brand)] focus:ring-2 focus:ring-[var(--color-highlight)]" />
        </div>
        <button type="submit" class="mt-2 h-11 w-full rounded-xl bg-[var(--color-brand)] text-sm font-bold text-white transition hover:bg-[var(--color-brand-dark)] disabled:opacity-60" :disabled="disableRegister">Create account</button>
        <p class="mb-0 pt-1 text-center text-sm text-[var(--color-muted)]">Already have an account? <router-link :to="{ name: 'login' }" class="font-bold text-[var(--color-brand)] no-underline hover:underline">Sign in</router-link></p>
      </form>
    </div>
  </section>
</template>

<script>
import { mapActions } from 'vuex';
import { userForm } from '../utils/rules';
import validateForm from '../utils/validation';
import { Eye, EyeOff } from '@lucide/vue';

export default {
components: { Eye, EyeOff },
data () {
  return {
    form: {
      name: '',
      username: '',
      password: '',
      password_confirmation: '',
    },
    showPassword: false,
    disableRegister: false,
    confirmPasswordRules: [
      value => value && value.length > 0 || 'Confirm password cannot be empty',
      value => this.form.password === value || `Password did not match`,
    ],
    rules: userForm,
  }
},
methods: {
  ...mapActions({
    setAlertSuccess: 'setAlertSuccess',
    setAlertError: 'setAlertError',
  }),
  validate () {
    this.disableRegister = true;

    const fields = { ...this.rules, password_confirmation: this.confirmPasswordRules };
    if (validateForm(this.$refs.form, fields, this.form)) {
      axios.post('/api/register', this.form)
        .then(res => {
          this.setAlertSuccess(res.data.message);
          this.$router.push({ name: 'login' });
        })
        .catch(err => {
          this.setAlertError(err.response.data);
          this.disableRegister = false;
        });
    } else {
      this.disableRegister = false;
    }
  },
}
}
</script>
