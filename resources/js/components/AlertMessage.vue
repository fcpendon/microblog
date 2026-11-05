<template>
  <Transition enter-active-class="transition duration-200" enter-from-class="translate-y-2 opacity-0" leave-active-class="transition duration-150" leave-to-class="translate-y-2 opacity-0">
    <div
      v-if="$store.state.alert.show"
      class="fixed right-4 top-20 z-50 flex max-w-[calc(100vw-2rem)] items-start gap-4 rounded-xl border border-[var(--color-line)] bg-white px-4 py-3.5 shadow-xl sm:right-6 sm:max-w-md"
      :class="$store.state.alert.type === 'error' ? 'border-l-4 border-l-[var(--color-danger)]' : 'border-l-4 border-l-[var(--color-brand)]'"
      role="status"
      aria-live="polite"
    >
      <p class="m-0 flex-1 text-sm font-medium leading-5 text-[var(--color-ink)]">{{ $store.state.alert.message }}</p>
      <button class="-mr-1 -mt-1 rounded-lg p-1.5 text-[var(--color-muted)] hover:bg-[var(--color-canvas)]" type="button" aria-label="Dismiss notification" @click="hideAlert">
        <X :size="18" aria-hidden="true" />
      </button>
    </div>
  </Transition>
</template>

<script>
import { mapActions } from 'vuex';
import { X } from '@lucide/vue';

export default {
  methods: {
    ...mapActions({
      hideAlert: 'hideAlert',
    }),
  },
  components: { X },
}
</script>
