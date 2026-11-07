<template>
  <div>
    <div class="mx-auto mb-5 grid size-40 place-items-center overflow-hidden rounded-2xl bg-[var(--color-canvas)] sm:mx-0">
      <image-placeholder :image="imagePreview" :maxSize="160" />
    </div>
    <form ref="form" class="space-y-3" @submit.prevent="validate">
      <label for="avatar-upload" class="block text-xs font-bold uppercase tracking-wider text-[var(--color-muted)]">Choose image</label>
      <input id="avatar-upload" ref="file" name="avatar" type="file" accept="image/gif, image/jpeg, image/jpg, image/png" class="block w-full text-sm text-[var(--color-muted)] file:mr-3 file:rounded-lg file:border-0 file:bg-[var(--color-highlight)] file:px-3 file:py-2 file:text-xs file:font-bold file:text-[var(--color-brand-dark)] hover:file:bg-emerald-100" @change="onChange($event.target.files[0])" />
      <p v-if="avatar" class="m-0 truncate text-xs text-[var(--color-muted)]">{{ avatar.name }} · {{ (avatar.size / 1024 / 1024).toFixed(2) }} MB</p>
      <div class="flex gap-2">
        <button type="submit" class="h-10 flex-1 rounded-xl bg-[var(--color-brand)] px-4 text-sm font-bold text-white hover:bg-[var(--color-brand-dark)] disabled:opacity-60" :disabled="disableUpload || !avatar">Upload photo</button>
        <button v-if="avatar" type="button" class="h-10 rounded-xl px-3 text-sm font-semibold text-[var(--color-muted)] hover:bg-[var(--color-canvas)]" @click="resetAvatar">Cancel</button>
      </div>
    </form>
  </div>
</template>

<script>
import ImagePlaceholder from '../components/ImagePlaceholder.vue';
import { postForm } from '../utils/rules';
import { mapActions } from 'vuex';
import validateForm from '../utils/validation';

export default {
  components: {
    ImagePlaceholder
  },
  props: ['image'],
  data () {
    return {
      avatar: null,
      imagePreview: this.image,
      disableUpload: false,
      rules: postForm,
    }
  },
  methods: {
    ...mapActions({
      getUser: 'getUser',
      setAlertSuccess: 'setAlertSuccess',
      setAlertError: 'setAlertError',
    }),
    onChange (file) {
      if (!file || !file.size) {
        return;
      }

      this.avatar = file;
      this.imagePreview = URL.createObjectURL(file);
    },
    validate () {
      this.disableUpload = true;

      if (validateForm(this.$refs.form, { avatar: this.rules.image }, { avatar: this.avatar })) {
        const formData = new FormData();
        formData.append('avatar', this.avatar);
        axios.post('/api/settings/upload-avatar', formData)
          .then(res => {
            this.getUser({ force: true })
              .then(() => {
                this.setAlertSuccess(res.data.message);
                this.resetAvatar();
                this.disableUpload = false;
              });
          })
          .catch(err => {
            this.setAlertError(err.response?.data || 'Could not upload the photo. Please try again.');
            this.disableUpload = false;
          });
      } else {
        this.disableUpload = false;
      }
    },
    resetAvatar () {
      this.avatar = null;
      if (this.imagePreview && this.imagePreview.startsWith('blob:')) URL.revokeObjectURL(this.imagePreview);
      this.imagePreview = this.$store.state.user.public_image_path;
      if (this.$refs.file) this.$refs.file.value = '';
    }
  }
}
</script>
