<script setup>
import { ArrowRight, ArrowUpLeft, Search } from '@lucide/vue';
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useNotificationStore } from '../stores/notification';

const router = useRouter();
const notifications = useNotificationStore();
const searchString = ref('');
const searchResult = ref([]);
const isLoading = ref(false);
const searchError = ref('');
const showResult = computed(() => searchResult.value.length > 0);
const debouncedSearchUser = _.debounce(searchUser, 500);
let requestId = 0;

watch(searchString, value => {
  requestId++;

  if (value?.length >= 3) {
    debouncedSearchUser();
    return;
  }

  isLoading.value = false;
  searchResult.value = [];
});

onBeforeUnmount(() => {
  debouncedSearchUser.cancel();
});

async function searchUser() {
  const term = searchString.value;

  if (term.length < 3) {
    return;
  }

  const id = ++requestId;
  isLoading.value = true;
  searchError.value = '';

  try {
    const response = await axios.get('/api/search-user', {
      params: { keyword: term, limit: 10 },
    });

    if (id === requestId) {
      searchResult.value = Object.values(response.data.data).map(item => ({
        name: item.name,
        username: item.username,
        image: item.public_image_path || '/images/default-avatar.png',
      }));
    }
  } catch {
    if (id === requestId) {
      searchResult.value = [];
      searchError.value = 'Could not search people. Try again.';
    }
  } finally {
    if (id === requestId) {
      isLoading.value = false;
    }
  }
}

async function redirectToUserProfile(username) {
  if (!username) {
    return;
  }

  await router.push({ name: 'profile', params: { username: username } });
  clear();
}

async function redirectToSearchUser() {
  if (!searchString.value) {
    notifications.error('Please enter a name or username to search');
    return;
  }

  await router.push({ name: 'search-user', params: { username: searchString.value } });
  clear();
}

function clear() {
  searchString.value = '';
  searchResult.value = [];
}
</script>

<template>
  <form class="relative w-full max-w-md" role="search" @submit.prevent="redirectToSearchUser">
    <label class="sr-only" for="user-search">Search people</label>
    <div
      class="flex h-10 items-center gap-2 rounded-xl border border-[var(--color-line)] bg-[var(--color-canvas)] px-3 transition focus-within:border-[var(--color-brand)] focus-within:bg-white">
      <Search :size="17" class="shrink-0 text-[var(--color-muted)]" aria-hidden="true" />
      <input id="user-search" v-model="searchString" type="search" autocomplete="off" placeholder="Search people"
        class="h-full min-w-0 flex-1 border-0 bg-transparent p-0 text-sm text-[var(--color-ink)] outline-none placeholder:text-[var(--color-muted)] focus:outline-none"
        aria-autocomplete="list" :aria-expanded="showResult" :aria-busy="isLoading"
        aria-controls="user-search-results" />
      <span v-if="isLoading"
        class="size-4 animate-spin rounded-full border-2 border-[var(--color-line)] border-t-[var(--color-brand)]"
        aria-label="Searching"></span>
      <button v-else-if="searchString" type="submit"
        class="grid size-7 place-items-center rounded-lg text-[var(--color-muted)] hover:bg-white hover:text-[var(--color-brand)]"
        aria-label="Search">
        <ArrowRight :size="16" aria-hidden="true" />
      </button>
    </div>
    <ul v-if="showResult" id="user-search-results"
      class="absolute left-0 right-0 top-full z-50 mt-2 max-h-80 overflow-auto rounded-xl border border-[var(--color-line)] bg-white p-1.5 shadow-xl"
      role="listbox" aria-label="Search suggestions">
      <li v-for="result in searchResult" :key="result.username" role="option"
        :aria-label="`${result.name}, @${result.username}`">
        <button type="button"
          class="flex w-full items-center gap-3 rounded-lg px-2.5 py-2 text-left hover:bg-[var(--color-canvas)]"
          @click="redirectToUserProfile(result.username)">
          <img class="size-9 rounded-full object-cover" :src="result.image" alt="" />
          <span class="min-w-0 flex-1">
            <span class="block truncate text-sm font-semibold text-[var(--color-ink)]">{{ result.name }}</span>
            <span class="block truncate text-xs text-[var(--color-muted)]">@{{ result.username }}</span>
          </span>
          <ArrowUpLeft :size="15" class="text-[var(--color-muted)]" aria-hidden="true" />
        </button>
      </li>
      <li v-if="searchError" class="px-3 py-4 text-center text-sm text-[var(--color-danger)]" role="status">{{
        searchError }}</li>
      <li v-else-if="!isLoading && !showResult" class="px-3 py-4 text-center text-sm text-[var(--color-muted)]">No
        people found</li>
      <li v-if="searchString && searchString.length >= 3" class="border-t border-[var(--color-line)] px-2 pt-1.5">
        <button type="submit"
          class="w-full rounded-lg px-2 py-2 text-left text-xs font-semibold text-[var(--color-brand)] hover:bg-[var(--color-highlight)]">Search
          all results for “{{ searchString }}”</button>
      </li>
    </ul>
  </form>
</template>
