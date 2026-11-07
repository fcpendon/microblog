<template>
  <form ref="form" class="w-full bg-white p-4 sm:p-5" @submit.prevent="validate">
    <label class="sr-only" for="post-content">What's on your mind?</label>
    <textarea
      id="post-content"
      v-model="form.content"
      name="content"
      maxlength="150"
      rows="3"
      placeholder="What's on your mind?"
      class="min-h-24 w-full resize-y border-0 bg-transparent p-0 text-base leading-6 text-[var(--color-ink)] outline-none placeholder:text-[var(--color-muted)] focus:ring-0"
      @keypress="watchCharacterCount"
    ></textarea>
    <div class="mt-2 flex items-center justify-between border-t border-[var(--color-line)] pt-3">
      <div class="flex items-center gap-2">
        <input ref="file" class="sr-only" type="file" name="image" accept="image/gif, image/jpeg, image/jpg, image/png" @change="onChange($event.target.files[0])" />
        <button v-if="!imagePreview" type="button" class="inline-flex h-9 items-center gap-2 rounded-full px-3 text-sm font-semibold text-[var(--color-brand)] hover:bg-[var(--color-highlight)]" @click="handleFileImport">
          <ImagePlus :size="17" aria-hidden="true" /> Add image
        </button>
        <button v-else type="button" class="inline-flex h-9 items-center gap-2 rounded-full px-3 text-sm font-semibold text-[var(--color-danger)] hover:bg-red-50" @click="removeImage">
          <X :size="17" aria-hidden="true" /> Remove image
        </button>
        <span class="text-xs tabular-nums text-[var(--color-muted)]">{{ charsEntered }}/{{ charsTotal }}</span>
      </div>
      <div class="flex items-center gap-2">
        <button v-if="isUpdate" type="button" class="h-9 rounded-full px-3 text-sm font-semibold text-[var(--color-muted)] hover:bg-[var(--color-canvas)]" @click="$emit('closeDialog')">Cancel</button>
        <button type="submit" class="inline-flex h-9 items-center gap-2 rounded-full bg-[var(--color-brand)] px-4 text-sm font-bold text-white transition hover:bg-[var(--color-brand-dark)] disabled:cursor-not-allowed disabled:opacity-50" :disabled="disablePost">
          <Send :size="15" aria-hidden="true" /> {{ isUpdate ? 'Save' : 'Post' }}
        </button>
      </div>
    </div>
    <div v-if="imagePreview" class="relative mt-4 overflow-hidden rounded-2xl border border-[var(--color-line)] bg-[var(--color-canvas)]">
      <img class="mx-auto max-h-80 w-full object-contain" :src="imagePreview" alt="Post image preview" />
      <button type="button" class="absolute right-2 top-2 grid size-8 place-items-center rounded-full bg-white/90 text-[var(--color-ink)] shadow" aria-label="Remove attached image" @click="removeImage">
        <X :size="16" aria-hidden="true" />
      </button>
    </div>
  </form>
</template>

<script>
import { mapActions } from 'vuex';
import { postForm } from '../utils/rules';
import validateForm from '../utils/validation';
import emitter from '../utils/events';
import { ImagePlus, Send, X } from '@lucide/vue';

export default {
  components: { ImagePlus, Send, X },
  props: ['post'],
  data () {
    return {
      form: {
        content: '',
        image: null,
        oldImage: '',
      },
      imagePreview: null,
      disablePost: false,
      charsTotal: 150,
      isUpdate: false,
      rules: postForm,
    }
  },
  created () {
    this.loadPostData();
  },
  computed: {
    charsEntered () {
      return this.form.content.length;
    }
  },
  methods: {
    ...mapActions({
      setAlertSuccess: 'setAlertSuccess',
      setAlertError: 'setAlertError',
    }),
    watchCharacterCount ($event) {
      return this.charsEntered >= this.charsTotal ? $event.preventDefault() : true;
    },
    onChange (file) {
      if (!file || !file.size) {
        return;
      }

      this.form.image = file;
      this.imagePreview = URL.createObjectURL(file);
    },
    validate () {
      this.disablePost = true;

      const fields = { content: this.rules.content };
      if (this.form.image) fields.image = this.rules.image;
      if (validateForm(this.$refs.form, fields, this.form)) {
        const formData = new FormData();
        formData.append('content', this.form.content);
        if (this.form.image) {
          formData.append('image', this.form.image);
        }
        if (this.isUpdate) {
          formData.append('_method', 'PUT');
          formData.append('old_image', this.form.oldImage);
        }
        (this.isUpdate ?
          axios.post('/api/posts/' + this.post.id, formData) :
          axios.post('/api/post', formData))
          .then(res => {
            this.setAlertSuccess(res.data.message);
            this.resetForm();
            emitter.emit('reloadPosts');
          })
          .catch(err => {
            this.setAlertError(err.response?.data || 'Could not save your post. Please try again.');
          })
          .finally(() => {
            this.disablePost = false;
          });
      } else {
        this.disablePost = false;
      }
    },
    handleFileImport () {
      this.$refs.file.click();
    },
    removeImage () {
      this.form.image = null;
      this.form.oldImage = '';
      if (this.imagePreview && this.imagePreview.startsWith('blob:')) URL.revokeObjectURL(this.imagePreview);
      this.imagePreview = null;
      if (this.$refs.file) this.$refs.file.value = '';
    },
    resetForm () {
      this.form.content = '';
      this.removeImage();
      this.$emit('closeDialog');
    },
    loadPostData () {
      if (this.post) {
        this.isUpdate = true;
        this.form.content = this.post.content;
        this.form.oldImage = this.post.images.length ? this.post.images[0].public_image_path : '';
        this.imagePreview = this.form.oldImage;
      }
    }
  }
}
</script>
