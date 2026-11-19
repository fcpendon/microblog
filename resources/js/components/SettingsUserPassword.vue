<script setup>
import { Eye, EyeOff } from '@lucide/vue';
import { reactive, ref } from 'vue';
import { useFormValidation } from '../composables/useFormValidation';
import { useNotificationStore } from '../stores/notification';
import {
  matches,
  matchesField,
  maxLength,
  minLength,
  required,
} from '../utils/validationRules';

const notifications = useNotificationStore();
const form = reactive({
  current_password: '',
  password: '',
  password_confirmation: '',
});
const { formElement, validateForm } = useFormValidation(form);
const isEditing = ref(false);
const showCurrentPassword = ref(false);
const showPassword = ref(false);
const disableUpdate = ref(false);
const validationRules = {
  current_password: [required('Current password cannot be empty')],
  password: [
    required(),
    minLength(8),
    maxLength(16),
    matches(/^(?=.*\d)(?=.*[a-zA-Z])[a-zA-Z0-9]+$/, 'Password must contain letters and numbers only.'),
  ],
  password_confirmation: [
    required('Confirm password cannot be empty'),
    matchesField('password', 'Password did not match'),
  ],
};

function resetDetails() {
  Object.assign(form, {
    current_password: '',
    password: '',
    password_confirmation: '',
  });
}

function toggleEditable() {
  if (isEditing.value) {
    resetDetails();
  }

  isEditing.value = !isEditing.value;
}

async function updatePassword() {
  if (!validateForm(validationRules)) {
    return;
  }

  disableUpdate.value = true;

  try {
    const response = await axios.post('/api/settings/password', form);

    notifications.success(response.data.message);
    toggleEditable();
  } catch (error) {
    notifications.error(error.response?.data || 'Could not update your password. Please try again.');
  } finally {
    disableUpdate.value = false;
  }
}
</script>

<template>
  <div>
    <form v-if="isEditing" ref="formElement" class="space-y-4" @submit.prevent="updatePassword">
      <label class="block text-sm font-semibold text-[var(--color-ink)]" for="current-password">Current password</label>
      <div class="relative">
        <input id="current-password" v-model="form.current_password" name="current_password"
          :type="showCurrentPassword ? 'text' : 'password'" autocomplete="current-password"
          class="block h-11 w-full rounded-xl border border-[var(--color-line)] bg-white px-3 pr-11 text-sm outline-none focus:border-[var(--color-brand)]" />
        <button type="button"
          class="absolute inset-y-0 right-0 grid w-11 place-items-center text-[var(--color-muted)] hover:text-[var(--color-ink)]"
          :aria-label="showCurrentPassword ? 'Hide current password' : 'Show current password'"
          @click="showCurrentPassword = !showCurrentPassword">
          <Eye v-if="!showCurrentPassword" :size="17" />
          <EyeOff v-else :size="17" />
        </button>
      </div>
      <label class="block text-sm font-semibold text-[var(--color-ink)]" for="new-password">New password</label>
      <div class="relative">
        <input id="new-password" v-model="form.password" name="password" :type="showPassword ? 'text' : 'password'"
          autocomplete="new-password"
          class="block h-11 w-full rounded-xl border border-[var(--color-line)] bg-white px-3 pr-11 text-sm outline-none focus:border-[var(--color-brand)]" />
        <button type="button"
          class="absolute inset-y-0 right-0 grid w-11 place-items-center text-[var(--color-muted)] hover:text-[var(--color-ink)]"
          :aria-label="showPassword ? 'Hide new password' : 'Show new password'" @click="showPassword = !showPassword">
          <Eye v-if="!showPassword" :size="17" />
          <EyeOff v-else :size="17" />
        </button>
      </div>
      <label class="block text-sm font-semibold text-[var(--color-ink)]" for="confirm-password">Confirm new
        password</label>
      <input id="confirm-password" v-model="form.password_confirmation" name="password_confirmation" type="password"
        autocomplete="new-password"
        class="block h-11 w-full rounded-xl border border-[var(--color-line)] bg-white px-3 text-sm outline-none focus:border-[var(--color-brand)]" />
      <div class="flex justify-end gap-2 pt-2">
        <button type="button"
          class="h-10 rounded-xl px-4 text-sm font-semibold text-[var(--color-muted)] hover:bg-[var(--color-canvas)]"
          @click="toggleEditable">Cancel</button>
        <button type="submit"
          class="h-10 rounded-xl bg-[var(--color-brand)] px-4 text-sm font-bold text-white hover:bg-[var(--color-brand-dark)] disabled:opacity-60"
          :disabled="disableUpdate">Update password</button>
      </div>
    </form>
    <div v-else class="flex justify-end">
      <button type="button"
        class="h-10 rounded-xl border border-[var(--color-line)] px-4 text-sm font-bold text-[var(--color-ink)] hover:bg-[var(--color-canvas)]"
        @click="toggleEditable">Change password</button>
    </div>
  </div>
</template>
