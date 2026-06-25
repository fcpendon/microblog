<template>
  <v-card flat>
    <v-card
      v-if="isEditing"
      class="ma-4 mt-0 pa-4"
      color="grey lighten-4"
      flat
    >
      <v-form
        ref="form"
        @submit.prevent="validate"
      >
        <v-text-field
          v-model="form.current_password"
          label="Current Password"
          :append-icon="showCurrentPassword ? 'mdi-eye' : 'mdi-eye-off'"
          :type="showCurrentPassword ? 'text' : 'password'"
          :rules="isEditing ? currentPasswordRules : []"
          :disabled="!isEditing"
          @click:append="showCurrentPassword = !showCurrentPassword"
        ></v-text-field>
        <v-text-field
          v-model="form.password"
          label="New Password"
          :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
          :type="showPassword ? 'text' : 'password'"
          :rules="isEditing ? rules.password : []"
          :disabled="!isEditing"
          @click:append="showPassword = !showPassword"
        ></v-text-field>
        <v-text-field
          v-model="form.password_confirmation"
          label="Confirm Password"
          type="password"
          :rules="isEditing ? confirmPasswordRules : []"
          :disabled="!isEditing"
        ></v-text-field>
        <div class="d-flex justify-end mt-4">
          <v-btn
            @click="toggleEditable"
            plain
          >Cancel</v-btn>
          <span class="mx-1"></span>
          <v-btn
            type="submit"
            color="primary"
            :disabled="disableUpdate"
          >Update</v-btn>
        </div>
      </v-form>
    </v-card>
    <v-card
      v-else
      class="d-flex justify-end mx-4"
      flat
    >
      <v-btn
        @click="toggleEditable"
        color="error"
      >Change Password</v-btn>
    </v-card>
  </v-card>
</template>

<script>
import {userForm} from '../utils/rules';
import {mapActions} from 'vuex';

export default {
  props: ['user'],
  data () {
    return {
      form: {
        current_password: '',
        password: '',
        password_confirmation: '',
      },
      currentPasswordRules: [
        value => value && value.length > 0 || 'Current password cannot be empty',
      ],
      confirmPasswordRules: [
        value => value && value.length > 0 || 'Confirm password cannot be empty',
        value => this.form.password === value || `Password did not match`,
      ],
      isEditing: false,
      showCurrentPassword: false,
      showPassword: false,
      disableUpdate: false,
      rules: userForm,
    }
  },
  methods: {
    ...mapActions({
      setAlertSuccess: 'setAlertSuccess',
      setAlertError: 'setAlertError',
    }),
    toggleEditable () {
      if (this.isEditing) {
        this.resetDetails();
      }
      this.isEditing = !this.isEditing;
    },
    resetDetails () {
      this.form = {
        current_password: '',
        password: '',
        password_confirmation: '',
      };
    },
    validate () {
      this.disableUpdate = true;

      if (this.$refs.form.validate()) {
        axios.post('/api/settings/password', this.form)
          .then(res => {
            this.setAlertSuccess(res.data.message);
            this.toggleEditable();
          })
          .catch(err => {
            this.setAlertError(err.response.data);
          })
          .finally(() => {
            this.disableUpdate = false;
          });
      } else {
        this.disableUpdate = false;
      }
    },
  }
}
</script>
