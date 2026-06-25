<template>
  <form
    @submit.prevent="redirectToSearchUser"
    class="mt-7 align-center"
  >
    <v-autocomplete
      color="white"
      label="Search User"
      append-icon=""
      append-outer-icon="mdi-account-search"
      v-model="user"
      dense
      solo-inverted
      @click:append-outer="redirectToSearchUser"
      @input="redirectToUserProfile"
      no-filter
      :loading="isLoading"
      :items="searchResult"
      :search-input.sync="searchString"
      :hide-no-data="hideNoData"
    >
      <template v-slot:item="data">
        <v-list-item-avatar
          tile
          fill-height
          height="60"
          width="60"
        >
          <v-img :src="data.item.image"></v-img>
        </v-list-item-avatar>
        <v-list-item-content>
          <v-list-item-title>{{ data.item.text }}</v-list-item-title>
          <v-list-item-subtitle>{{ data.item.value }}</v-list-item-subtitle>
        </v-list-item-content>
      </template>
    </v-autocomplete>
  </form>
</template>

<script>
import {mapActions} from 'vuex';

export default {
  data () {
    return {
      user: '',
      searchString: '',
      searchResult: [],
      isLoading: false,
      hideNoData: true,
    }
  },
  watch: {
    searchString(text) {
      text && text.length >= 3 && this.debouncedSearchUser();
    }
  },
  methods: {
    ...mapActions({
      setAlertError: 'setAlertError',
    }),
    searchUser () {
      if (!this.searchString) {
        return;
      }
      this.isLoading = true;

      axios.get('/api/search-user', {
        params: {
          name: this.searchString,
          limit: 10,
        }
      })
        .then(res => {
          if (res.data.data.length !== 0) {
            this.searchResult = Object.values(res.data.data).map(user => {
              return {
                value: user.username,
                text: user.name,
                image: user.public_image_path ? user.public_image_path : '/images/default-avatar.png',
              }
            });
          } else {
            this.searchResult = [];
          }
          this.isLoading = false;
          this.hideNoData = false;
        })
        .catch(err => {
          console.log(err);
        });
    },
    redirectToUserProfile () {
      if (!this.user) {
        return;
      }
      this.$router.push({
        name: 'profile',
        params: {username: this.user},
      }).catch(() => {})
        .finally(() => {
          this.user = null;
          this.searchResult = [];
        });
    },
    redirectToSearchUser () {
      if (!this.searchString) {
        this.setAlertError('Please enter a name or username to search');
        return;
      }
      this.$router.push({
        name: 'search-user',
        params: {username: this.searchString},
      }).catch(() => {})
        .finally(() => {
          this.searchString = null;
          this.searchResult = [];
          this.hideNoData = true;
        });
    }
  },
  created () {
    this.debouncedSearchUser = _.debounce(this.searchUser, 500)
  },
}
</script>
