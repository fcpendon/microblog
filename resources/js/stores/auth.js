import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useAuthStore = defineStore('auth', () => {
  const authenticated = ref(false);
  const initialized = ref(false);
  const user = ref({});

  async function fetchUser({ force = false } = {}) {
    if (initialized.value && !force) {
      return authenticated.value;
    }

    try {
      const response = await axios.get('/api/user');

      user.value = response.data || {};
      authenticated.value = Boolean(response.data);

      return authenticated.value;
    } catch {
      user.value = {};
      authenticated.value = false;

      return false;
    } finally {
      initialized.value = true;
    }
  }

  return {
    authenticated,
    initialized,
    user,
    fetchUser,
  };
});
