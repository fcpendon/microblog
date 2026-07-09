<template>
  <v-container>
    <v-row v-if="user" class="mt-8">
      <v-col>
        <v-row
          justify="center"
          no-gutters
        >
          <profile-user-details :user="user"></profile-user-details>
        </v-row>
      </v-col>
      <v-col>
        <v-row
          v-if="user.username === $store.state.user.username"
          justify="center"
          class="mb-10"
          no-gutters
        >
          <post-form></post-form>
        </v-row>
        <v-row
          justify="center"
          no-gutters
        >
          <post-list :user="user"></post-list>
        </v-row>
      </v-col>
      <v-col></v-col>
    </v-row>
  </v-container>
</template>

<script>
import ProfileUserDetails from '../components/ProfileUserDetails';
import PostForm from '../components/PostForm';
import PostList from '../components/PostList';

export default {
  components: {
    ProfileUserDetails,
    PostForm,
    PostList,
  },
  data () {
    return {
      user: null,
      username: 'test',
    }
  },
  created () {
    this.loadProfile();
  },
  methods: {
    loadProfile () {
      let profile = this.$route.params.username;
      if (profile === this.$store.state.user.username) {
        return this.user = this.$store.state.user;
      }
      axios.get('/api/profiles/' + this.$route.params.username)
        .then(res => {
          this.user = res.data.data;
        })
        .catch(err => {
          this.$router.push({ name: '404' });
        });
    }
  }
}
</script>
