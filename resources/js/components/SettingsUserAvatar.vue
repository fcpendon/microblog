<template>
  <v-card
    class="pa-4"
    flat
  >
    <v-card
      class="pa-1"
      outlined
    >
      <image-placeholder
        :image="imagePreview"
        :maxSize="300"
      ></image-placeholder>
    </v-card>
    <v-card
      class="pt-2"
      flat
      max-width="310"
    >
      <v-form
        ref="form"
        @submit.prevent="validate"
      >
        <v-file-input
          v-model="avatar"
          :rules="avatar ? rules.image : []"
          accept="image/gif, image/jpeg, image/jpg, image/png"
          label="Choose File"
          dense
          outlined
          show-size
          prepend-icon="mdi-camera"
          @change="onChange"
          @click:clear="resetAvatar"
        ></v-file-input>
        <v-btn
          type="submit"
          color="primary"
          :disabled="disableUpload"
          block
        >Upload</v-btn>
      </v-form>
    </v-card>
  </v-card>
</template>

<script>
import ImagePlaceholder from '../components/ImagePlaceholder';
import {postForm} from '../utils/rules';
import {mapActions} from 'vuex';

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

      if (this.$refs.form.validate()) {
        const formData = new FormData();
        formData.append('avatar', this.avatar);
        axios.post('/api/settings/upload-avatar', formData)
          .then(res => {
            this.getUser()
              .then(() => {
                this.setAlertSuccess(res.data.message);
                this.resetAvatar();
                this.disableUpload = false;
              });
          })
          .catch(err => {
            this.setAlertError(err.response.data);
            this.disableUpload = false;
          });
      } else {
        this.disableUpload = false;
      }
    },
    resetAvatar () {
      this.avatar = null;
      URL.revokeObjectURL(this.imagePreview);
      this.imagePreview = this.$store.state.user.public_image_path;
    }
  }
}
</script>
