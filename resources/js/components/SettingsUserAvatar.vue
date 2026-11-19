<script setup>
import { storeToRefs } from 'pinia';
import { ref } from 'vue';
import ImagePlaceholder from '../components/ImagePlaceholder.vue';
import { useFormValidation } from '../composables/useFormValidation';
import { useObjectUrl } from '../composables/useObjectUrl';
import { useAuthStore } from '../stores/auth';
import { useNotificationStore } from '../stores/notification';
import { fileSize, required } from '../utils/validationRules';

const props = defineProps({ image: { type: String, default: '' } });
const auth = useAuthStore();
const { user } = storeToRefs(auth);
const notifications = useNotificationStore();
const avatar = ref(null);
const { objectUrl: imagePreview, resetObjectUrl, setObjectUrlFromFile } = useObjectUrl(props.image);
const { formElement, validateForm } = useFormValidation(avatar);
const disableUpload = ref(false);
const validationRules = {
  avatar: [
    required('Image cannot be empty'),
    fileSize(5000000, 'Image file size must be less than 5 MB.'),
  ],
};

function onChange(file) {
  if (!file?.size) {
    return;
  }

  avatar.value = file;
  setObjectUrlFromFile(file);
}

function resetAvatar() {
  avatar.value = null;
  resetObjectUrl(user.value.public_image_path);
  document.querySelector('#avatar-upload').value = '';
}

async function uploadAvatar() {
  if (!validateForm(validationRules)) {
    return;
  }

  disableUpload.value = true;

  try {
    const data = new FormData();

    data.append('avatar', avatar.value);

    const response = await axios.post('/api/settings/upload-avatar', data);

    await auth.fetchUser({ force: true });
    notifications.success(response.data.message);
    resetAvatar();
  } catch (error) {
    notifications.error(error.response?.data || 'Could not upload the photo. Please try again.');
  } finally {
    disableUpload.value = false;
  }
}
</script>

<template>
  <div>
    <div
      class="mx-auto mb-5 grid size-40 place-items-center overflow-hidden rounded-2xl bg-[var(--color-canvas)] sm:mx-0">
      <ImagePlaceholder :image="imagePreview" :maxSize="160" />
    </div>
    <form ref="formElement" class="space-y-3" @submit.prevent="uploadAvatar">
      <label for="avatar-upload"
        class="block text-xs font-bold uppercase tracking-wider text-[var(--color-muted)]">Choose image</label>
      <input id="avatar-upload" ref="file" name="avatar" type="file"
        accept="image/gif, image/jpeg, image/jpg, image/png"
        class="block w-full text-sm text-[var(--color-muted)] file:mr-3 file:rounded-lg file:border-0 file:bg-[var(--color-highlight)] file:px-3 file:py-2 file:text-xs file:font-bold file:text-[var(--color-brand-dark)] hover:file:bg-emerald-100"
        @change="onChange($event.target.files[0])" />
      <p v-if="avatar" class="m-0 truncate text-xs text-[var(--color-muted)]">{{ avatar.name }} · {{ (avatar.size / 1024
        / 1024).toFixed(2) }} MB</p>
      <div class="flex gap-2">
        <button type="submit"
          class="h-10 flex-1 rounded-xl bg-[var(--color-brand)] px-4 text-sm font-bold text-white hover:bg-[var(--color-brand-dark)] disabled:opacity-60"
          :disabled="disableUpload || !avatar">Upload photo</button>
        <button v-if="avatar" type="button"
          class="h-10 rounded-xl px-3 text-sm font-semibold text-[var(--color-muted)] hover:bg-[var(--color-canvas)]"
          @click="resetAvatar">Cancel</button>
      </div>
    </form>
  </div>
</template>
