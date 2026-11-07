<template>
  <div>
    <form v-if="isEditing" ref="form" class="space-y-4" @submit.prevent="validate">
      <label class="block text-sm font-semibold text-[var(--color-ink)]" for="current-password">Current password</label>
      <div class="relative">
        <input id="current-password" v-model="form.current_password" name="current_password" :type="showCurrentPassword ? 'text' : 'password'" autocomplete="current-password" class="block h-11 w-full rounded-xl border border-[var(--color-line)] bg-white px-3 pr-11 text-sm outline-none focus:border-[var(--color-brand)]" />
        <button type="button" class="absolute inset-y-0 right-0 grid w-11 place-items-center text-[var(--color-muted)] hover:text-[var(--color-ink)]" :aria-label="showCurrentPassword ? 'Hide current password' : 'Show current password'" @click="showCurrentPassword = !showCurrentPassword"><Eye v-if="!showCurrentPassword" :size="17" /><EyeOff v-else :size="17" /></button>
      </div>
      <label class="block text-sm font-semibold text-[var(--color-ink)]" for="new-password">New password</label>
      <div class="relative">
        <input id="new-password" v-model="form.password" name="password" :type="showPassword ? 'text' : 'password'" autocomplete="new-password" class="block h-11 w-full rounded-xl border border-[var(--color-line)] bg-white px-3 pr-11 text-sm outline-none focus:border-[var(--color-brand)]" />
        <button type="button" class="absolute inset-y-0 right-0 grid w-11 place-items-center text-[var(--color-muted)] hover:text-[var(--color-ink)]" :aria-label="showPassword ? 'Hide new password' : 'Show new password'" @click="showPassword = !showPassword"><Eye v-if="!showPassword" :size="17" /><EyeOff v-else :size="17" /></button>
      </div>
      <label class="block text-sm font-semibold text-[var(--color-ink)]" for="confirm-password">Confirm new password</label>
      <input id="confirm-password" v-model="form.password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="block h-11 w-full rounded-xl border border-[var(--color-line)] bg-white px-3 text-sm outline-none focus:border-[var(--color-brand)]" />
      <div class="flex justify-end gap-2 pt-2">
        <button type="button" class="h-10 rounded-xl px-4 text-sm font-semibold text-[var(--color-muted)] hover:bg-[var(--color-canvas)]" @click="toggleEditable">Cancel</button>
        <button type="submit" class="h-10 rounded-xl bg-[var(--color-brand)] px-4 text-sm font-bold text-white hover:bg-[var(--color-brand-dark)] disabled:opacity-60" :disabled="disableUpdate">Update password</button>
      </div>
    </form>
    <div v-else class="flex justify-end">
      <button type="button" class="h-10 rounded-xl border border-[var(--color-line)] px-4 text-sm font-bold text-[var(--color-ink)] hover:bg-[var(--color-canvas)]" @click="toggleEditable">Change password</button>
    </div>
  </div>
</template>

<script>
import { userForm } from '../utils/rules';
import { mapActions } from 'vuex';
import validateForm from '../utils/validation';
import { Eye, EyeOff } from '@lucide/vue';

export default {
  components: { Eye, EyeOff },
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

      const fields = {
        current_password: this.currentPasswordRules,
        password: this.rules.password,
        password_confirmation: this.confirmPasswordRules,
      };
      if (validateForm(this.$refs.form, fields, this.form)) {
        axios.post('/api/settings/password', this.form)
          .then(res => {
            this.setAlertSuccess(res.data.message);
            this.toggleEditable();
          })
          .catch(err => {
            this.setAlertError(err.response?.data || 'Could not update your password. Please try again.');
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
