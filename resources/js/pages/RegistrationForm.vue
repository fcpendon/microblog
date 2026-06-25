<template>
  <v-container fill-height>
    <v-row justify="center">
      <v-card
        width="500"
        tile
      >
        <v-card
          dark
          tile
          flat
          height="200"
          class="d-flex justify-center"
        >
          <v-card-title class="text-h2">Register</v-card-title>
        </v-card>
        <v-form
          ref="form"
          @submit.prevent="validate"
          class="pa-4"
        >
          <v-text-field
            :rules="rules.name"
            v-model="form.name"
            label="Full Name"
            autofocus
          ></v-text-field>
          <v-text-field
            :rules="rules.username"
            v-model="form.username"
            label="Username"
          ></v-text-field>
          <v-text-field
            :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
            :rules="rules.password"
            :type="showPassword ? 'text' : 'password'"
            v-model="form.password"
            label="Password"
            @click:append="showPassword = !showPassword"
          ></v-text-field>
          <v-text-field
            :rules="confirmPasswordRules"
            type="password"
            v-model="form.password_confirmation"
            label="Confirm Password"
          ></v-text-field>
          <div class="d-flex justify-space-between mt-4">
            <router-link
              :to="{ name: 'login' }"
              class="text-decoration-none"
            >
              <v-btn plain>Login</v-btn>
            </router-link>
            <v-btn
              type="submit"
              color="primary"
              :disabled="disableRegister"
            >Register</v-btn>
          </div>
        </v-form>
      </v-card>
    </v-row>
  </v-container>
</template>

<script>
import {mapActions} from 'vuex';
import {userForm} from '../utils/rules';

export default {
data () {
  return {
    form: {
      name: '',
      username: '',
      password: '',
      password_confirmation: '',
    },
    showPassword: false,
    disableRegister: false,
    confirmPasswordRules: [
      value => value && value.length > 0 || 'Confirm password cannot be empty',
      value => this.form.password === value || `Password did not match`,
    ],
    rules: userForm,
  }
},
methods: {
  ...mapActions({
    setAlertSuccess: 'setAlertSuccess',
    setAlertError: 'setAlertError',
  }),
  validate () {
    this.disableRegister = true;

    if (this.$refs.form.validate()) {
      axios.post('/api/register', this.form)
        .then(res => {
          this.setAlertSuccess(res.data.message);
          this.$router.push({ name: 'login' });
        })
        .catch(err => {
          this.setAlertError(err.response.data);
          this.disableRegister = false;
        });
    } else {
      this.disableRegister = false;
    }
  },
}
}
</script>
