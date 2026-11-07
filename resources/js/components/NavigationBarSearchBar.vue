<template>
  <form class="relative w-full max-w-md" role="search" @submit.prevent="redirectToSearchUser">
    <label class="sr-only" for="user-search">Search people</label>
    <div class="flex h-10 items-center gap-2 rounded-xl border border-[var(--color-line)] bg-[var(--color-canvas)] px-3 transition focus-within:border-[var(--color-brand)] focus-within:bg-white">
      <Search :size="17" class="shrink-0 text-[var(--color-muted)]" aria-hidden="true" />
      <input
        id="user-search"
        v-model="searchString"
        type="search"
        autocomplete="off"
        placeholder="Search people"
        class="h-full min-w-0 flex-1 border-0 bg-transparent p-0 text-sm text-[var(--color-ink)] outline-none placeholder:text-[var(--color-muted)] focus:outline-none"
        aria-autocomplete="list"
        :aria-expanded="!hideNoData"
        :aria-busy="isLoading"
        aria-controls="user-search-results"
      />
      <span v-if="isLoading" class="size-4 animate-spin rounded-full border-2 border-[var(--color-line)] border-t-[var(--color-brand)]" aria-label="Searching"></span>
      <button v-else-if="searchString" type="submit" class="grid size-7 place-items-center rounded-lg text-[var(--color-muted)] hover:bg-white hover:text-[var(--color-brand)]" aria-label="Search">
        <ArrowRight :size="16" aria-hidden="true" />
      </button>
    </div>
    <ul v-if="!hideNoData" id="user-search-results" class="absolute left-0 right-0 top-full z-50 mt-2 max-h-80 overflow-auto rounded-xl border border-[var(--color-line)] bg-white p-1.5 shadow-xl" role="listbox" aria-label="Search suggestions">
      <li v-for="result in searchResult" :key="result.value" role="option" :aria-label="`${result.title}, @${result.value}`">
        <button type="button" class="flex w-full items-center gap-3 rounded-lg px-2.5 py-2 text-left hover:bg-[var(--color-canvas)]" @click="redirectToUserProfile(result.value)">
          <img class="size-9 rounded-full object-cover" :src="result.image" alt="" />
          <span class="min-w-0 flex-1">
            <span class="block truncate text-sm font-semibold text-[var(--color-ink)]">{{ result.title }}</span>
            <span class="block truncate text-xs text-[var(--color-muted)]">@{{ result.value }}</span>
          </span>
          <ArrowUpLeft :size="15" class="text-[var(--color-muted)]" aria-hidden="true" />
        </button>
      </li>
      <li v-if="searchError" class="px-3 py-4 text-center text-sm text-[var(--color-danger)]" role="status">{{ searchError }}</li>
      <li v-else-if="!isLoading && !searchResult.length" class="px-3 py-4 text-center text-sm text-[var(--color-muted)]">No people found</li>
      <li v-if="searchString && searchString.length >= 3" class="border-t border-[var(--color-line)] px-2 pt-1.5">
        <button type="submit" class="w-full rounded-lg px-2 py-2 text-left text-xs font-semibold text-[var(--color-brand)] hover:bg-[var(--color-highlight)]">Search all results for “{{ searchString }}”</button>
      </li>
    </ul>
  </form>
</template>

<script>
import { mapActions } from 'vuex';
import { ArrowRight, ArrowUpLeft, Search } from '@lucide/vue';

export default {
  components: { ArrowRight, ArrowUpLeft, Search },
  data () {
    return {
      user: '',
      searchString: '',
      searchResult: [],
      isLoading: false,
      hideNoData: true,
      searchError: '',
      searchRequestId: 0,
    }
  },
  watch: {
    searchString(text) {
      this.searchRequestId++;
      this.searchError = '';
      if (text && text.length >= 3) {
        this.hideNoData = false;
        this.debouncedSearchUser();
      } else {
        this.hideNoData = true;
        this.searchResult = [];
        this.isLoading = false;
      }
    }
  },
  methods: {
    ...mapActions({
      setAlertError: 'setAlertError',
    }),
    searchUser () {
      const searchTerm = this.searchString;
      if (!searchTerm || searchTerm.length < 3) {
        return;
      }
      const requestId = ++this.searchRequestId;
      this.isLoading = true;
      this.searchError = '';

      axios.get('/api/search-user', {
        params: {
          keyword: searchTerm,
          limit: 10,
        }
      })
        .then(res => {
          if (requestId !== this.searchRequestId) return;
          if (res.data.data.length !== 0) {
            this.searchResult = Object.values(res.data.data).map(user => {
              return {
                title: user.name,
                value: user.username,
                image: user.public_image_path ? user.public_image_path : '/images/default-avatar.png',
              }
            });
          } else {
            this.searchResult = [];
          }
          this.hideNoData = false;
        })
        .catch(() => {
          if (requestId !== this.searchRequestId) return;
          this.searchResult = [];
          this.searchError = 'Could not search people. Try again.';
        })
        .finally(() => {
          if (requestId === this.searchRequestId) this.isLoading = false;
        });
    },
    redirectToUserProfile (username) {
      const selectedUser = username || this.user;
      if (!selectedUser) {
        return;
      }
      this.$router.push({
        name: 'profile',
        params: {username: selectedUser},
      }).catch(() => {})
        .finally(() => {
          this.user = null;
          this.searchResult = [];
          this.hideNoData = true;
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
