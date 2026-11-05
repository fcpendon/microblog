<template>
  <section class="w-full border-b border-[var(--color-line)] bg-white px-5 py-6 sm:rounded-2xl sm:border sm:p-6">
    <div class="flex items-center gap-4">
      <image-placeholder :image="user.public_image_path" :maxSize="76" />
      <div class="min-w-0">
        <h1 class="m-0 truncate text-xl font-bold tracking-tight text-[var(--color-ink)]">{{ user.name }}</h1>
        <p class="mb-0 mt-1 truncate text-sm text-[var(--color-muted)]">@{{ user.username }}</p>
      </div>
    </div>
  </section>
</template>

<script>
import ImagePlaceholder from './ImagePlaceholder.vue';

export default {
  components: {
    ImagePlaceholder,
  },
  props: ['user'],
}
</script>
