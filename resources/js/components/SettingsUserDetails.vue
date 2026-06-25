<template>
  <v-card
    class="pa-4"
    flat
  >
    <v-form
      ref="form"
      @submit.prevent="validate"
    >
      <v-text-field
        v-model="form.name"
        label="Name"
        :rules="rules.name"
        :readonly="!isEditing"
        :filled="isEditing"
      ></v-text-field>
      <v-text-field
        v-model="form.username"
        label="Username"
        :rules="rules.username"
        :readonly="!isEditing || user.accountType === 1"
        :filled="isEditing"
      ></v-text-field>
      <div class="d-flex justify-end mt-4">
        <template v-if="isEditing">
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
        </template>
        <template v-else>
          <v-btn
            @click="toggleEditable"
            color="primary"
          >Edit</v-btn>
        </template>
      </div>
    </v-form>
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
        name: this.user.name,
        username: this.user.username,
        accountType: this.user.accountType,
      },
      isEditing: false,
      disableUpdate: false,
      rules: userForm,
    }
  },
  methods: {
    ...mapActions({
      getUser: 'getUser',
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
      this.form.name = this.$store.state.user.name;
      this.form.username = this.$store.state.user.username;
    },
    validate () {
      this.disableUpdate = true;

      if (this.$refs.form.validate()) {
        axios.post('/api/settings/details', this.form)
          .then(res => {
            this.getUser()
              .then(() => {
                this.setAlertSuccess(res.data.message);
                this.toggleEditable();
                this.disableUpdate = false;
              });
          })
          .catch(err => {
            this.setAlertError(err.response.data);
            this.disableUpdate = false;
          });
      } else {
        this.disableUpdate = false;
      }
    },
  }
}
</script>
