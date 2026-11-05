<template>
  <section class="mx-auto w-full max-w-2xl overflow-hidden border-y border-[var(--color-line)] bg-white sm:rounded-2xl sm:border">
    <header class="border-b border-[var(--color-line)] px-5 py-5 sm:px-6">
      <p class="m-0 text-xs font-bold uppercase tracking-[0.14em] text-[var(--color-brand)]">People</p>
      <h1 class="mb-0 mt-1 break-words text-xl font-bold tracking-tight text-[var(--color-ink)]">Results for “{{ searchString }}”</h1>
    </header>
    <div v-if="searchResult.length === 0" class="px-6 py-12 text-center">
      <p class="m-0 text-sm text-[var(--color-muted)]">{{ statusMessage }}</p>
    </div>
    <ul v-else class="m-0 list-none divide-y divide-[var(--color-line)] p-0">
      <li v-for="user in searchResult" :key="user.username">
        <router-link :to="{ name: 'profile', params: { username: user.username }}" class="flex items-center gap-4 px-5 py-4 text-[var(--color-ink)] no-underline transition-colors hover:bg-[var(--color-canvas)] sm:px-6">
          <image-placeholder :image="user.public_image_path" :maxSize="56" />
          <span class="min-w-0 flex-1">
            <span class="block truncate text-sm font-bold">{{ user.name }}</span>
            <span class="mt-0.5 block truncate text-sm text-[var(--color-muted)]">@{{ user.username }}</span>
          </span>
          <ArrowUpRight :size="18" class="text-[var(--color-muted)]" aria-hidden="true" />
        </router-link>
      </li>
    </ul>
  </section>
</template>

<script>
import ImagePlaceholder from '../components/ImagePlaceholder.vue';
import { ArrowUpRight } from '@lucide/vue';

export default {
  components: {
    ImagePlaceholder,
    ArrowUpRight,
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
  }
}
</script>
