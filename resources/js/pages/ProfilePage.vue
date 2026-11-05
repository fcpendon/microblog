<template>
  <div v-if="user" class="mx-auto grid w-full max-w-6xl grid-cols-1 gap-6 lg:grid-cols-[17rem_minmax(0,1fr)]">
    <aside class="self-start">
      <profile-user-details :user="user" />
    </aside>
    <section class="mx-auto w-full max-w-2xl overflow-hidden border-y border-[var(--color-line)] bg-white sm:rounded-2xl sm:border">
      <header class="border-b border-[var(--color-line)] px-5 py-4 sm:px-6">
        <p class="m-0 text-xs font-bold uppercase tracking-[0.14em] text-[var(--color-brand)]">Profile</p>
        <h1 class="mb-0 mt-1 text-2xl font-bold tracking-tight text-[var(--color-ink)]">{{ user.name }}</h1>
      </header>
      <post-form v-if="user.username === $store.state.user.username" class="border-b border-[var(--color-line)]" />
      <post-list :user="user" />
    </section>
  </div>
</template>

<script>
import ProfileUserDetails from '../components/ProfileUserDetails.vue';
import PostForm from '../components/PostForm.vue';
import PostList from '../components/PostList.vue';

export default {
  components: {
    ProfileUserDetails,
    PostForm,
    PostList,
  },
  data () {
    return {
      user: null
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
