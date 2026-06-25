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
          <v-card-title class="text-h2">Login</v-card-title>
        </v-card>
        <v-form
          ref="form"
          @submit.prevent="validate"
          class="pa-4"
        >
          <v-text-field
            :rules="usernameRules"
            v-model="form.username"
            label="Username"
            autofocus
          ></v-text-field>
          <v-text-field
            :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
            :rules="passwordRules"
            :type="showPassword ? 'text' : 'password'"
            v-model="form.password"
            label="Password"
            @click:append="showPassword = !showPassword"
          ></v-text-field>
          <div class="d-flex justify-space-between mt-4">
            <router-link
              :to="{ name: 'register' }"
              class="text-decoration-none"
            >
              <v-btn plain>Register</v-btn>
            </router-link>
            <v-btn
              type="submit"
              color="primary"
              :disabled="disableLogin"
            >Login
            </v-btn>
          </div>
        </v-form>
      </v-card>
    </v-row>
  </v-container>
</template>

<script>
import { mapActions } from 'vuex';

export default {
  data () {
    return {
      form: {
        username: '',
        password: ''
      },
      showPassword: false,
      disableLogin: false,
      usernameRules: [
        value => !!value || 'Please enter your username',
      ],
      passwordRules: [
        value => !!value || 'Please enter your password',
      ],
    }
  },
  methods: {
    ...mapActions({
      getUser: 'getUser',
      setAlertSuccess: 'setAlertSuccess',
      setAlertError: 'setAlertError',
    }),
    validate () {
      this.disableLogin = true;

      if (this.$refs.form.validate()) {
        axios.post('/api/login', this.form)
          .then(res => {
            this.getUser()
              .then(() => {
                this.setAlertSuccess(res.data.message);
                this.$router.push({ name: 'home' });
              });
          })
          .catch(err => {
            this.setAlertError(err.response.data);
            this.disableLogin = false;
          });
      } else {
        this.disableLogin = false;
      }
    },
  }
}
</script>
