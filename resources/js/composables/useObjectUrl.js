import { onBeforeUnmount, ref } from 'vue';

export function useObjectUrl(initialValue = null) {
  const objectUrl = ref(initialValue);

  function revokeObjectUrl() {
    if (objectUrl.value?.startsWith('blob:')) {
      URL.revokeObjectURL(objectUrl.value);
    }
  }

  function resetObjectUrl(value = null) {
    revokeObjectUrl();
    objectUrl.value = value;
  }

  function setObjectUrlFromFile(file) {
    resetObjectUrl(URL.createObjectURL(file));
  }

  onBeforeUnmount(revokeObjectUrl);

  return {
    objectUrl,
    resetObjectUrl,
    setObjectUrlFromFile,
  };
}
