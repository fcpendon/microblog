<template>
  <header class="sticky top-0 z-40 border-b border-[var(--color-line)] bg-white/95 backdrop-blur">
    <div class="mx-auto grid min-h-16 max-w-7xl grid-cols-[auto_minmax(0,1fr)_auto] grid-rows-[40px_40px] items-center gap-x-3 gap-y-2 px-4 py-2 sm:px-6 lg:px-8 md:relative md:flex md:h-16 md:justify-between md:gap-3 md:py-0">
      <router-link :to="{ name: 'home' }" class="col-start-1 row-start-1 flex shrink-0 items-center gap-2.5 text-[var(--color-ink)] no-underline" aria-label="Microblog home">
        <span class="grid size-9 place-items-center rounded-xl bg-[var(--color-brand)] text-sm font-black text-white">m</span>
        <span class="hidden text-lg font-bold tracking-tight sm:inline">microblog</span>
      </router-link>
      <navigation-bar-search-bar class="col-span-3 row-start-2 w-full justify-self-center md:absolute md:left-1/2 md:top-1/2 md:col-span-1 md:row-start-auto md:w-[min(28rem,calc(100%-20rem))] md:max-w-none md:-translate-x-1/2 md:-translate-y-1/2 lg:w-[min(28rem,calc(100%-41rem))] xl:w-[min(28rem,calc(100%-16rem))]" />
      <nav class="col-start-3 row-start-1 flex shrink-0 items-center justify-self-end gap-1 md:col-start-auto md:ml-auto md:gap-0 xl:gap-1" aria-label="Account navigation">
        <router-link
          :to="{ name: 'profile', params: { username: $store.state.user.username }}"
          class="inline-flex size-10 items-center justify-center gap-2 rounded-xl text-sm font-semibold text-[var(--color-muted)] no-underline hover:bg-[var(--color-canvas)] hover:text-[var(--color-ink)] sm:w-auto sm:px-3"
          aria-label="Profile"
          title="Profile"
        >
          <UserRound :size="18" aria-hidden="true" />
          <span class="hidden lg:inline">Profile</span>
        </router-link>
        <router-link
          :to="{ name: 'settings' }"
          class="inline-flex size-10 items-center justify-center gap-2 rounded-xl text-sm font-semibold text-[var(--color-muted)] no-underline hover:bg-[var(--color-canvas)] hover:text-[var(--color-ink)] sm:w-auto sm:px-3"
          aria-label="Settings"
          title="Settings"
        >
          <Settings2 :size="18" aria-hidden="true" />
          <span class="hidden lg:inline">Settings</span>
        </router-link>
        <navigation-bar-logout />
      </nav>
    </div>
  </header>
</template>

<script>
import NavigationBarSearchBar from './NavigationBarSearchBar.vue';
import NavigationBarLogout from './NavigationBarLogout.vue';
import { Settings2, UserRound } from '@lucide/vue';

export default {
  components: {
    NavigationBarSearchBar,
    NavigationBarLogout,
    Settings2,
    UserRound,
  }
}
</script>
