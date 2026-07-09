<template>
  <v-container>
    <v-row
      justify="center"
      no-gutters
    >
      <v-icon
        color="black"
        size="100"
      >mdi-alpha-m-circle-outline</v-icon>
      <span class="text-h2 mt-4 ml-n2">icroblog</span>
    </v-row>
    <v-row>
      <v-col>
        <v-row
          justify="center"
          no-gutters
        >
          <profile-user-details :user="this.$store.state.user"></profile-user-details>
        </v-row>
      </v-col>
      <v-col>
        <v-row
          justify="center"
          no-gutters
          class="mb-10"
        >
          <post-form></post-form>
        </v-row>
        <v-row
          justify="center"
          no-gutters
        >
          <post-list></post-list>
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
  }
}
</script>
