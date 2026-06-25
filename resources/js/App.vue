<template>
  <v-app style="background: beige">
    <navigation-bar v-if="$store.state.authenticated"></navigation-bar>
    <v-main>
      <alert-message></alert-message>
      <router-view :key="$route.path"></router-view>
    </v-main>
  </v-app>
</template>

<script>
import NavigationBar from './components/NavigationBar';
import AlertMessage from './components/AlertMessage';

export default {
  components: {
    NavigationBar,
    AlertMessage
  }
}
</script>
