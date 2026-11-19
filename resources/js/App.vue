<script setup>
import NavigationBar from './components/NavigationBar.vue';
import AlertMessage from './components/AlertMessage.vue';
import { storeToRefs } from 'pinia';
import { useAuthStore } from './stores/auth';

const auth = useAuthStore();
const { authenticated } = storeToRefs(auth);
</script>

<template>
  <div class="min-h-screen bg-[var(--color-canvas)]">
    <NavigationBar v-if="authenticated" />
    <main class="mx-auto min-h-[calc(100vh-4rem)] w-full max-w-7xl px-4 pb-16 pt-6 sm:px-6 lg:px-8">
      <AlertMessage />
      <RouterView :key="$route.path" />
    </main>
  </div>
</template>
