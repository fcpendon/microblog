<template>
  <div class="mx-auto grid w-full max-w-7xl grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_minmax(0,42rem)_minmax(0,1fr)]">
    <section class="mx-auto w-full max-w-2xl overflow-hidden border-y border-[var(--color-line)] bg-white sm:rounded-2xl sm:border xl:col-start-2">
      <header class="border-b border-[var(--color-line)] px-5 py-4 sm:px-6">
        <p class="m-0 text-xs font-bold uppercase tracking-[0.14em] text-[var(--color-brand)]">Your network</p>
        <h1 class="mb-0 mt-1 text-2xl font-bold tracking-tight text-[var(--color-ink)]">Home</h1>
      </header>
      <post-form class="border-b border-[var(--color-line)]" />
      <post-list />
    </section>
    <aside class="hidden self-start xl:col-start-3 xl:block xl:w-full xl:max-w-[17rem] xl:justify-self-end">
      <div class="rounded-2xl border border-[var(--color-line)] bg-white p-5">
        <h2 class="m-0 text-sm font-bold text-[var(--color-ink)]">Your account</h2>
        <router-link :to="{ name: 'profile', params: { username: $store.state.user.username }}" class="mt-4 flex items-center gap-3 rounded-xl p-2 text-[var(--color-ink)] no-underline hover:bg-[var(--color-canvas)]">
          <image-placeholder :image="$store.state.user.public_image_path" :maxSize="44" />
          <span class="min-w-0 flex-1">
            <span class="block truncate text-sm font-bold">{{ $store.state.user.name }}</span>
            <span class="mt-0.5 block truncate text-xs text-[var(--color-muted)]">@{{ $store.state.user.username }}</span>
          </span>
          <ArrowRight :size="16" class="text-[var(--color-muted)]" aria-hidden="true" />
        </router-link>
        <router-link :to="{ name: 'settings' }" class="mt-2 block border-t border-[var(--color-line)] px-2 pt-3 text-xs font-bold text-[var(--color-brand)] no-underline hover:underline">Account settings</router-link>
      </div>
    </aside>
  </div>
</template>

<script>
import PostForm from '../components/PostForm.vue';
import PostList from '../components/PostList.vue';
import ImagePlaceholder from '../components/ImagePlaceholder.vue';
import { ArrowRight } from '@lucide/vue';

export default {
  components: {
    PostForm,
    PostList,
    ImagePlaceholder,
    ArrowRight,
  }
}
</script>
