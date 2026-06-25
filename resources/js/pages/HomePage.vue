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
          <tweet-post-form></tweet-post-form>
        </v-row>
        <v-row
          justify="center"
          no-gutters
        >
          <tweet-list></tweet-list>
        </v-row>
      </v-col>
      <v-col></v-col>
    </v-row>
  </v-container>
</template>

<script>
import ProfileUserDetails from '../components/ProfileUserDetails';
import TweetPostForm from '../components/TweetPostForm';
import TweetList from '../components/TweetList';

export default {
  components: {
    ProfileUserDetails,
    TweetPostForm,
    TweetList,
  }
}
</script>
