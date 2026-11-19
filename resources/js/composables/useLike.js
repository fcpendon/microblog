import { ref, watch } from 'vue';
import { useNotificationStore } from '../stores/notification';

export function useLike(likeable, getEndpoint, errorMessage) {
  const notifications = useNotificationStore();
  const isLiked = ref(likeable.value.is_liked);
  const likeCount = ref(likeable.value.likes_count);
  const isLoading = ref(false);

  function syncLikeState(item) {
    isLiked.value = item.is_liked;
    likeCount.value = item.likes_count;
  }

  watch(likeable, syncLikeState);

  async function toggleLike() {
    if (isLoading.value) {
      return;
    }

    isLoading.value = true;

    try {
      const response = isLiked.value
        ? await axios.delete(getEndpoint(likeable.value))
        : await axios.post(getEndpoint(likeable.value));

      syncLikeState(response.data.data);
    } catch (error) {
      notifications.error(error.response?.data || errorMessage);
    } finally {
      isLoading.value = false;
    }
  }

  return {
    isLiked,
    likeCount,
    isLoading,
    toggleLike,
  };
}
