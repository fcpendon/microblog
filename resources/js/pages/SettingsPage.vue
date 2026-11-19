<script setup>
import { computed } from 'vue';
import { storeToRefs } from 'pinia';
import SettingsUserAvatar from '../components/SettingsUserAvatar.vue';
import SettingsUserDetails from '../components/SettingsUserDetails.vue';
import SettingsUserPassword from '../components/SettingsUserPassword.vue';
import { useAuthStore } from '../stores/auth';

const auth = useAuthStore();
const { user: authenticatedUser } = storeToRefs(auth);
const user = computed(() => ({
  ...authenticatedUser.value,
  image: authenticatedUser.value.public_image_path,
}));
</script>

<template>
  <section class="mx-auto w-full max-w-4xl">
    <header class="mb-6">
      <p class="m-0 text-xs font-bold uppercase tracking-[0.14em] text-[var(--color-brand)]">Account</p>
      <h1 class="mb-0 mt-1 text-2xl font-bold tracking-tight text-[var(--color-ink)]">Settings</h1>
      <p class="mb-0 mt-2 text-sm text-[var(--color-muted)]">Manage your profile and sign-in details.</p>
    </header>
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-[18rem_minmax(0,1fr)]">
      <section class="self-start rounded-2xl border border-[var(--color-line)] bg-white p-5">
        <h2 class="mb-4 mt-0 text-sm font-bold text-[var(--color-ink)]">Profile photo</h2>
        <SettingsUserAvatar :image="user.image" />
      </section>
      <div class="space-y-5">
        <section class="rounded-2xl border border-[var(--color-line)] bg-white p-5 sm:p-6">
          <h2 class="mb-4 mt-0 text-sm font-bold text-[var(--color-ink)]">Personal details</h2>
          <SettingsUserDetails :user="user" />
        </section>
        <section class="rounded-2xl border border-[var(--color-line)] bg-white p-5 sm:p-6">
          <h2 class="mb-4 mt-0 text-sm font-bold text-[var(--color-ink)]">Password</h2>
          <SettingsUserPassword />
        </section>
      </div>
    </div>
  </section>
</template>
