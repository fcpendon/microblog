<script setup>
import { storeToRefs } from 'pinia';
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useFormValidation } from '../composables/useFormValidation';
import { useAuthStore } from '../stores/auth';
import { useNotificationStore } from '../stores/notification';
import {
  email,
  matches,
  maxLength,
  minLength,
  required,
} from '../utils/validationRules';

const router = useRouter();
const props = defineProps({ user: { type: Object, required: true } });
const auth = useAuthStore();
const { user: authenticatedUser } = storeToRefs(auth);
const notifications = useNotificationStore();
const form = reactive({
  name: props.user.name,
  username: props.user.username,
  email: props.user.email,
});
const { formElement, validateForm } = useFormValidation(form);
const isEditing = ref(false);
const disableUpdate = ref(false);
const fieldClass = 'mt-2 block h-11 w-full rounded-xl border border-[var(--color-line)] bg-white px-3 text-sm text-[var(--color-ink)] outline-none focus:border-[var(--color-brand)] read-only:bg-[var(--color-canvas)] read-only:text-[var(--color-muted)]';
const validationRules = {
  name: [
    required(),
    minLength(4),
    maxLength(100),
    matches(/^[a-zA-Z\s.']+$/, 'Name may contain only letters, spaces, apostrophes, and periods.'),
  ],
  username: [
    required(),
    minLength(4),
    maxLength(20),
    matches(/^[a-zA-Z0-9]+$/, 'Username may contain only letters and numbers.'),
  ],
  email: [
    required('Please enter an email address'),
    maxLength(100),
    email(),
  ],
};

function resetDetails() {
  Object.assign(form, {
    name: authenticatedUser.value.name,
    username: authenticatedUser.value.username,
    email: authenticatedUser.value.email,
  });
}

function toggleEditable() {
  if (isEditing.value) {
    resetDetails();
  }

  isEditing.value = !isEditing.value;
}

async function updateDetails() {
  if (!validateForm(validationRules)) {
    return;
  }

  disableUpdate.value = true;

  try {
    const response = await axios.post('/api/settings/details', form);

    await auth.fetchUser({ force: true });
    notifications.success(response.data.message);
    toggleEditable();

    if (!authenticatedUser.value.email_verified_at) {
      router.push({ name: 'verify-email' });
    }
  } catch (error) {
    notifications.error(error.response?.data || 'Could not update your details. Please try again.');
  } finally {
    disableUpdate.value = false;
  }
}
</script>

<template>
  <form ref="formElement" class="space-y-4" @submit.prevent="updateDetails">
    <label class="block text-sm font-semibold text-[var(--color-ink)]" for="settings-name">Name</label>
    <input id="settings-name" v-model="form.name" name="name" type="text" maxlength="100" :readonly="!isEditing"
      :class="fieldClass" />
    <label class="block text-sm font-semibold text-[var(--color-ink)]" for="settings-username">Username</label>
    <input id="settings-username" v-model="form.username" name="username" type="text" maxlength="20"
      :readonly="!isEditing || user.accountType === 1" :class="fieldClass" />
    <label class="block text-sm font-semibold text-[var(--color-ink)]" for="settings-email">Email</label>
    <input id="settings-email" v-model="form.email" name="email" type="email" maxlength="100" autocomplete="email"
      :readonly="!isEditing" :class="fieldClass" />
    <div class="flex justify-end gap-2 pt-2">
      <template v-if="isEditing">
        <button type="button"
          class="h-10 rounded-xl px-4 text-sm font-semibold text-[var(--color-muted)] hover:bg-[var(--color-canvas)]"
          @click="toggleEditable">Cancel</button>
        <button type="submit"
          class="h-10 rounded-xl bg-[var(--color-brand)] px-4 text-sm font-bold text-white hover:bg-[var(--color-brand-dark)] disabled:opacity-60"
          :disabled="disableUpdate">Update details</button>
      </template>
      <button v-else type="button"
        class="h-10 rounded-xl bg-[var(--color-ink)] px-4 text-sm font-bold text-white hover:bg-black"
        @click="toggleEditable">Edit details</button>
    </div>
  </form>
</template>
