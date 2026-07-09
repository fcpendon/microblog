<template>
  <div>
    <v-sheet width="600"></v-sheet>
    <v-row
      v-for="post in posts"
      :key="post.id"
      justify="center"
      no-gutters
    >
      <post-item :post="post"></post-item>
    </v-row>
    <v-row
      justify="center"
      v-if="isLoading"
      class="my-10"
    >
      <v-progress-circular
        :size="50"
        dark
        indeterminate
      ></v-progress-circular>
    </v-row>
    <v-row
      v-if="isEmptyResult"
      justify="center"
      align="center"
      class="mb-8"
      no-gutters
    >
      <v-divider></v-divider><span class="text-overline mx-4">No more posts</span><v-divider></v-divider>
    </v-row>
  </div>
</template>

<script>
import PostItem from './PostItem';

export default {
  components: {
    PostItem,
  },
  data () {
    return {
      posts: [],
      isLoading: true,
      isEmptyResult: false,
    }
  },
  created () {
    this.fetchLatestPosts();
  },
  mounted () {
    this.listenScroll();
    this.$root.$on('reloadPosts', () => {
      this.isEmptyResult = false;
      this.fetchLatestPosts(true);
    });
  },
  methods: {
    fetchLatestPosts (reset = false) {
      this.isLoading = true;

      axios.get('/api/posts/' + (this.$route.params.username ?? ''), {
        params: {
          offset: reset ? 0 : this.posts.length,
        }
      })
        .then(res => {
          let newPosts = res.data.data;
          if (newPosts.length === 0) {
            this.isEmptyResult = true;
          } else {
            this.posts = reset ? newPosts : this.posts.concat(newPosts);
          }
        })
        .catch(err => {
          console.log(err);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    listenScroll () {
      window.onscroll = () => {
        let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight;
        if (!this.isEmptyResult && !this.isLoading && bottomOfWindow) {
          this.fetchLatestPosts();
        }
      }
    }
  }
}
</script>
