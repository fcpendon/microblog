import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useNotificationStore = defineStore('notification', () => {
  const message = ref('');
  const type = ref('');
  const visible = ref(false);

  function success(notification) {
    message.value = notification;
    type.value = 'primary';
    visible.value = true;
  }

  function error(payload) {
    const notification = typeof payload === 'string'
      ? payload
      : Object.values(payload?.errors || {}).flat()[0] || payload?.message;

    message.value = notification || 'Something went wrong. Please try again.';
    type.value = 'error';
    visible.value = true;
  }

  function hide() {
    visible.value = false;
  }

  return {
    message,
    type,
    visible,
    success,
    error,
    hide,
  };
});
