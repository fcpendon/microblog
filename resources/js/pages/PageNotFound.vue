<template>
  <section class="mx-auto flex min-h-[60vh] max-w-xl flex-col items-center justify-center px-4 text-center">
    <p class="m-0 text-sm font-bold uppercase tracking-[0.18em] text-[var(--color-brand)]">404</p>
    <h1 class="mb-0 mt-3 text-3xl font-bold tracking-tight text-[var(--color-ink)]">Page not found</h1>
    <p class="mb-0 mt-3 text-sm leading-6 text-[var(--color-muted)]">The page may have moved, or the address may be incorrect.</p>
    <router-link :to="{ name: 'home' }" class="mt-6 inline-flex h-10 items-center rounded-xl bg-[var(--color-brand)] px-4 text-sm font-bold text-white no-underline hover:bg-[var(--color-brand-dark)]">Back to home</router-link>
  </section>
</template>

<script>
export default {
}
</script>
