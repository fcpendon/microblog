<template>
  <form ref="form" class="space-y-4" @submit.prevent="validate">
    <label class="block text-sm font-semibold text-[var(--color-ink)]" for="settings-name">Name</label>
    <input id="settings-name" v-model="form.name" name="name" type="text" maxlength="50" :readonly="!isEditing" :class="fieldClass" />
    <label class="block text-sm font-semibold text-[var(--color-ink)]" for="settings-username">Username</label>
    <input id="settings-username" v-model="form.username" name="username" type="text" maxlength="20" :readonly="!isEditing || user.accountType === 1" :class="fieldClass" />
    <div class="flex justify-end gap-2 pt-2">
      <template v-if="isEditing">
        <button type="button" class="h-10 rounded-xl px-4 text-sm font-semibold text-[var(--color-muted)] hover:bg-[var(--color-canvas)]" @click="toggleEditable">Cancel</button>
        <button type="submit" class="h-10 rounded-xl bg-[var(--color-brand)] px-4 text-sm font-bold text-white hover:bg-[var(--color-brand-dark)] disabled:opacity-60" :disabled="disableUpdate">Update details</button>
      </template>
      <button v-else type="button" class="h-10 rounded-xl bg-[var(--color-ink)] px-4 text-sm font-bold text-white hover:bg-black" @click="toggleEditable">Edit details</button>
    </div>
  </form>
</template>

<script>
import { userForm } from '../utils/rules';
import { mapActions } from 'vuex';
import validateForm from '../utils/validation';

export default {
  props: ['user'],
  data () {
    return {
      form: {
        name: this.user.name,
        username: this.user.username,
      },
      isEditing: false,
      disableUpdate: false,
      rules: userForm,
    }
  },
  computed: {
    fieldClass () {
      return 'mt-2 block h-11 w-full rounded-xl border border-[var(--color-line)] bg-white px-3 text-sm text-[var(--color-ink)] outline-none focus:border-[var(--color-brand)] read-only:bg-[var(--color-canvas)] read-only:text-[var(--color-muted)]';
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

      if (validateForm(this.$refs.form, this.rules, this.form)) {
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
