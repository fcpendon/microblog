<script setup>
import { ImagePlus, Send, X } from '@lucide/vue';
import { computed, reactive, ref } from 'vue';
import { useFormValidation } from '../composables/useFormValidation';
import { useObjectUrl } from '../composables/useObjectUrl';
import { useNotificationStore } from '../stores/notification';
import { fileSize, maxLength, required } from '../utils/validationRules';

const props = defineProps({ post: { type: Object, default: null } });
const emit = defineEmits(['closeDialog', 'postsChanged']);
const notifications = useNotificationStore();
const form = reactive({
  content: props.post?.content || '',
  image: null,
  oldImage: props.post?.images?.[0]?.public_image_path || '',
});
const { formElement, validateForm } = useFormValidation(form);
const { objectUrl: imagePreview, resetObjectUrl, setObjectUrlFromFile } = useObjectUrl(form.oldImage || null);
const imageInput = ref(null);
const disablePost = ref(false);
const isUpdate = computed(() => Boolean(props.post));
const charsEntered = computed(() => form.content.length);
const charsTotal = 150;

function clearForm() {
  form.content = '';
  removeImage();
}

function onChange(file) {
  if (!file?.size) {
    return;
  }

  form.image = file;
  setObjectUrlFromFile(file);
}

function handleFileImport() {
  imageInput.value?.click();
}

function removeImage() {
  form.image = null;
  form.oldImage = '';
  resetObjectUrl();

  if (imageInput.value) {
    imageInput.value.value = '';
  }
}

async function savePost() {
  const validationRules = {
    content: [required(), maxLength(150)],
    ...(form.image && {
      image: [
        required('Image cannot be empty'),
        fileSize(5_000_000, 'Image file size must be less than 5 MB.'),
      ],
    }),
  };

  if (!validateForm(validationRules)) {
    return;
  }

  disablePost.value = true;

  try {
    const data = new FormData();

    data.append('content', form.content);

    if (form.image) {
      data.append('image', form.image);
    }

    if (isUpdate.value) {
      data.append('_method', 'PUT');
      data.append('old_image', form.oldImage);
    }

    await axios.post('/api/posts' + (isUpdate.value ? `/${props.post.id}` : ''), data);

    notifications.success((isUpdate.value ? 'Edit' : 'Create') + ' post successful!');
    clearForm();
    emit('closeDialog');
    emit('postsChanged');
  } catch (error) {
    notifications.error(error.response?.data || 'Could not save your post. Please try again.');
  } finally {
    disablePost.value = false;
  }
}
</script>

<template>
  <form ref="formElement" class="w-full bg-white p-4 sm:p-5" @submit.prevent="savePost">
    <label class="sr-only" for="post-content">What's on your mind?</label>
    <textarea id="post-content" v-model="form.content" name="content" maxlength="150" rows="3"
      placeholder="What's on your mind?"
      class="min-h-24 w-full resize-y border-0 bg-transparent p-0 text-base leading-6 text-[var(--color-ink)] outline-none placeholder:text-[var(--color-muted)] focus:ring-0"></textarea>
    <div class="mt-2 flex items-center justify-between border-t border-[var(--color-line)] pt-3">
      <div class="flex items-center gap-2">
        <input ref="imageInput" class="sr-only" type="file" name="image" accept="image/jpg, image/jpeg, image/png, image/webp"
          @change="onChange($event.target.files[0])" />
        <button v-if="!imagePreview" type="button"
          class="inline-flex h-9 items-center gap-2 rounded-full px-3 text-sm font-semibold text-[var(--color-brand)] hover:bg-[var(--color-highlight)]"
          @click="handleFileImport">
          <ImagePlus :size="17" aria-hidden="true" /> Add image
        </button>
        <button v-else type="button"
          class="inline-flex h-9 items-center gap-2 rounded-full px-3 text-sm font-semibold text-[var(--color-danger)] hover:bg-red-50"
          @click="removeImage">
          <X :size="17" aria-hidden="true" /> Remove image
        </button>
        <span class="text-xs tabular-nums text-[var(--color-muted)]">{{ charsEntered }}/{{ charsTotal }}</span>
      </div>
      <div class="flex items-center gap-2">
        <button v-if="isUpdate" type="button"
          class="h-9 rounded-full px-3 text-sm font-semibold text-[var(--color-muted)] hover:bg-[var(--color-canvas)]"
          @click="$emit('closeDialog')">Cancel</button>
        <button type="submit"
          class="inline-flex h-9 items-center gap-2 rounded-full bg-[var(--color-brand)] px-4 text-sm font-bold text-white transition hover:bg-[var(--color-brand-dark)] disabled:cursor-not-allowed disabled:opacity-50"
          :disabled="disablePost">
          <Send :size="15" aria-hidden="true" /> {{ isUpdate ? 'Save' : 'Post' }}
        </button>
      </div>
    </div>
    <div v-if="imagePreview"
      class="relative mt-4 overflow-hidden rounded-2xl border border-[var(--color-line)] bg-[var(--color-canvas)]">
      <img class="mx-auto max-h-80 w-full object-contain" :src="imagePreview" alt="Post image preview" />
      <button type="button"
        class="absolute right-2 top-2 grid size-8 place-items-center rounded-full bg-white/90 text-[var(--color-ink)] shadow"
        aria-label="Remove attached image" @click="removeImage">
        <X :size="16" aria-hidden="true" />
      </button>
    </div>
  </form>
</template>
