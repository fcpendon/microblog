<template>
  <section class="mx-auto w-full max-w-lg rounded-3xl border border-[var(--color-line)] bg-white p-6 text-center shadow-sm sm:p-10">
    <div class="mx-auto grid size-12 place-items-center rounded-full bg-[var(--color-highlight)] text-xl font-bold text-[var(--color-brand)]" aria-hidden="true">✓</div>
    <p class="mb-0 mt-5 text-xs font-bold uppercase tracking-[0.14em] text-[var(--color-brand)]">Email confirmed</p>
    <h1 class="mb-0 mt-1 text-2xl font-bold tracking-tight text-[var(--color-ink)]">You're all set</h1>
    <p class="mt-3 text-sm leading-6 text-[var(--color-muted)]">Your email address has been verified. You can now sign in and join the conversation.</p>
    <router-link :to="{ name: $store.state.authenticated ? 'home' : 'login' }" class="mt-6 inline-flex h-11 items-center justify-center rounded-xl bg-[var(--color-brand)] px-6 text-sm font-bold text-white no-underline hover:bg-[var(--color-brand-dark)]">
      {{ $store.state.authenticated ? 'Go to Microblog' : 'Sign in' }}
    </router-link>
  </section>
</template>

<script>
export default {
  mounted () {
    this.$store.dispatch('getUser');
  },
}
</script>
