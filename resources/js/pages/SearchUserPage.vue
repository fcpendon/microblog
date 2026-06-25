<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="auto">
        <v-row no-gutters>
          <v-card-text class="text-h5 text-center">
              Search Results for
              <span class="font-italic">&ldquo;{{ this.searchString }}&rdquo;</span> :
            </v-card-text>
        </v-row>
        <v-divider class="my-4"></v-divider>
        <v-sheet width="600"></v-sheet>
        <v-row
          v-if="searchResult.length == 0"
          no-gutters
        >
          <v-card-text class="text-h6 font-weight-regular text-center">{{ this.statusMessage }}</v-card-text>
        </v-row>
        <v-row
          v-for="user in searchResult"
          :key="user.username"
        >
          <v-col>
            <router-link
              :to="{ name: 'profile', params: { username: user.username }}"
              class="text-decoration-none"
            >
              <v-hover v-slot="{hover}">
                <v-card
                  width="100%"
                  class="transition-swing"
                  :color="hover ? 'grey lighten-4' : ''"
                  tile
                >
                  <v-row no-gutters>
                    <v-col cols="auto">
                      <image-placeholder
                        :image="user.public_image_path"
                        :maxSize="128"
                      ></image-placeholder>
                    </v-col>
                    <v-col>
                      <v-card-title>{{ user.name }}</v-card-title>
                      <v-card-subtitle>{{ user.username }}</v-card-subtitle>
                    </v-col>
                  </v-row>
                </v-card>
              </v-hover>
            </router-link>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import ImagePlaceholder from '../components/ImagePlaceholder';

export default {
  components: {
    ImagePlaceholder,
  },
  data () {
    return {
      searchString: '',
      searchResult: [],
      statusMessage: '',
    }
  },
  created () {
    this.searchString = this.$route.params.username;
    this.searchUser();
  },
  methods: {
    searchUser () {
      this.statusMessage = 'Fetching search results...';

      axios.get('/api/search-user', {
        params: {
          name: this.searchString,
        }
      })
        .then(res => {
          if (res.data.data.length !== 0) {
            this.searchResult = res.data.data;
          } else {
            this.statusMessage = 'No results found.';
          }
        })
        .catch(err => {
          console.log(err);
        });
    },
    redirectToUserProfile () {
      this.$router.push({
        name: 'profile',
        params: {username: this.user},
      });
    },
  }
}
</script>
