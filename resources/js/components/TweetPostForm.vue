<template>
  <v-card
    class="pa-4"
    width="100%"
    tile
  >
    <v-form
      ref="form"
      @submit.prevent="validate"
    >
      <v-textarea
        v-model="form.content"
        label="What's on your mind?"
        :rules="rules.content"
        @keypress="watchCharacterCount"
        rows="1"
        outlined
        counter
        auto-grow
      >
        <template v-slot:counter>
          <span class="text-caption">{{ charsEntered }} / {{ charsTotal }}</span>
        </template>
      </v-textarea>
      <v-file-input
        v-model="form.image"
        ref="file"
        :rules="form.image ? rules.image : []"
        accept="image/gif, image/jpeg, image/jpg, image/png"
        @change="onChange"
        class="d-none"
      ></v-file-input>
      <v-row
        v-if="imagePreview"
        justify="center"
        no-gutters
      >
        <v-card
          class="pa-1"
          outlined
        >
          <v-img
            :src="imagePreview"
            max-height="300"
            max-width="300"
          ></v-img>
        </v-card>
      </v-row>
      <v-card-actions class="pa-0 mt-4">
        <v-btn
          v-if="!imagePreview"
          plain
          @click="handleFileImport"
        >Attach Image</v-btn>
        <v-btn
          v-else
          plain
          @click="removeImage"
        >Remove Image</v-btn>
        <v-spacer></v-spacer>
        <v-btn
          v-if="this.isUpdate"
          @click="$emit('closeDialog')"
          plain
        >Cancel</v-btn>
        <v-btn
          type="submit"
          color="primary"
          :disabled="disablePost"
        >Post</v-btn>
      </v-card-actions>
    </v-form>
  </v-card>
</template>

<script>
import ImagePlaceholder from './ImagePlaceholder';
import {mapActions} from 'vuex';
import {postForm} from '../utils/rules';

export default {
  components: {ImagePlaceholder},
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
      return this.charsEntered === this.charsTotal ? $event.preventDefault() : true;
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

      if (this.$refs.form.validate()) {
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
            this.$root.$emit('reloadTweets');
          })
          .catch(err => {
            this.setAlertError(err.response.data);
          })
          .finally(() => {
            this.disablePost = false;
          });
      } else {
        this.disablePost = false;
      }
    },
    handleFileImport () {
      this.$refs.file.$refs.input.click();
    },
    removeImage () {
      this.form.image = null;
      this.form.oldImage = '';
      this.imagePreview = null;
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
