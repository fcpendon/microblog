<template>
  <v-app-bar
    app
    dark
  >
    <v-col>
      <router-link
        :to="{ name: 'home' }"
        class="text-decoration-none"
      >
        <v-app-bar-nav-icon>
          <v-icon
            color="white"
            x-large
          >mdi-alpha-m-circle-outline
          </v-icon>
        </v-app-bar-nav-icon>
      </router-link>
    </v-col>
    <v-col>
      <navigation-bar-search-bar></navigation-bar-search-bar>
    </v-col>
    <v-col>
      <v-row justify="end">
        <router-link
          :to="{ name: 'profile', params: { username: this.$store.state.user.username }}"
          class="text-decoration-none"
        >
          <v-btn plain>
            <v-icon color="white">mdi-account</v-icon>
            <div class="hidden-md-and-down">Profile</div>
          </v-btn>
        </router-link>
        <router-link
          :to="{ name: 'settings' }"
          class="text-decoration-none"
        >
          <v-btn plain>
            <v-icon color="white">mdi-cog</v-icon>
            <div class="hidden-md-and-down">Settings</div>
          </v-btn>
        </router-link>
        <navigation-bar-logout></navigation-bar-logout>
      </v-row>
    </v-col>
  </v-app-bar>
</template>

<script>
import NavigationBarSearchBar from './NavigationBarSearchBar.vue';
import NavigationBarLogout from './NavigationBarLogout';

export default {
  components: {
    NavigationBarSearchBar,
    NavigationBarLogout
  },
  methods: {
    redirect(page) {
      window.location.href = page;
    }
  }
}
</script>
