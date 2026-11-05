<template>
  <div
    class="grid shrink-0 place-items-center overflow-hidden rounded-2xl bg-[var(--color-highlight)] text-[var(--color-brand)]"
    :style="{ width: `${maxSize}px`, height: `${maxSize}px`, maxWidth: '100%' }"
  >
    <img v-if="image" class="h-full w-full object-cover" :src="image" alt="" />
    <UserRound v-else :size="Math.min(maxSize * 0.45, 56)" stroke-width="1.5" aria-hidden="true" />
  </div>
</template>

<script>
import { UserRound } from '@lucide/vue';

export default {
  components: { UserRound },
  props: {
    image: String,
    maxSize: Number,
  }
}
</script>
