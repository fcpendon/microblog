<script setup>
import { ArrowUpRight } from '@lucide/vue';
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import ImagePlaceholder from '../components/ImagePlaceholder.vue';

const route = useRoute();
const searchString = ref(route.params.username);
const searchResult = ref([]);
const statusMessage = ref('');
const errorMessage = ref('');

onMounted(searchUser);

async function searchUser() {
  statusMessage.value = 'Fetching search results...';
  errorMessage.value = '';

  try {
    const response = await axios.get('/api/search-user', {
      params: { keyword: searchString.value, limit: 10 },
    });

    searchResult.value = response.data.data;

    if (!searchResult.value.length) {
      statusMessage.value = 'No results found.';
    }
  } catch {
    errorMessage.value = 'Could not load search results. Check your connection and try again.';
  }
}
</script>

<template>
  <section
    class="mx-auto w-full max-w-2xl overflow-hidden border-y border-[var(--color-line)] bg-white sm:rounded-2xl sm:border">
    <header class="border-b border-[var(--color-line)] px-5 py-5 sm:px-6">
      <p class="m-0 text-xs font-bold uppercase tracking-[0.14em] text-[var(--color-brand)]">People</p>
      <h1 class="mb-0 mt-1 break-words text-xl font-bold tracking-tight text-[var(--color-ink)]">Results for “{{
        searchString }}”</h1>
    </header>
    <div v-if="searchResult.length === 0" class="px-6 py-12 text-center" :role="errorMessage ? 'alert' : 'status'">
      <p class="m-0 text-sm" :class="errorMessage ? 'text-[var(--color-danger)]' : 'text-[var(--color-muted)]'">{{
        errorMessage || statusMessage }}</p>
      <button v-if="errorMessage" type="button"
        class="mt-3 rounded-lg px-3 py-2 text-sm font-bold text-[var(--color-brand)] hover:bg-[var(--color-highlight)]"
        @click="searchUser">Try again</button>
    </div>
    <ul v-else class="m-0 list-none divide-y divide-[var(--color-line)] p-0">
      <li v-for="user in searchResult" :key="user.username">
        <RouterLink :to="{ name: 'profile', params: { username: user.username } }"
          class="flex items-center gap-4 px-5 py-4 text-[var(--color-ink)] no-underline transition-colors hover:bg-[var(--color-canvas)] sm:px-6">
          <ImagePlaceholder :image="user.public_image_path" :maxSize="56" />
          <span class="min-w-0 flex-1">
            <span class="block truncate text-sm font-bold">{{ user.name }}</span>
            <span class="mt-0.5 block truncate text-sm text-[var(--color-muted)]">@{{ user.username }}</span>
          </span>
          <ArrowUpRight :size="18" class="text-[var(--color-muted)]" aria-hidden="true" />
        </RouterLink>
      </li>
    </ul>
  </section>
</template>
