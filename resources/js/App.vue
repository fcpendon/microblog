<template>
  <div class="min-h-screen bg-[var(--color-canvas)]">
    <navigation-bar v-if="$store.state.authenticated"></navigation-bar>
    <main class="mx-auto min-h-[calc(100vh-4rem)] w-full max-w-7xl px-4 pb-16 pt-6 sm:px-6 lg:px-8">
      <alert-message></alert-message>
      <router-view :key="$route.path"></router-view>
    </main>
  </div>
</template>

<script>
import NavigationBar from './components/NavigationBar.vue';
import AlertMessage from './components/AlertMessage.vue';

export default {
  components: {
    NavigationBar,
    AlertMessage
  }
}
</script>
