<script setup>
import { reactive, ref } from 'vue';
import { useFormValidation } from '../composables/useFormValidation';
import { useNotificationStore } from '../stores/notification';
import { maxLength, required } from '../utils/validationRules';

const props = defineProps({ post: { type: Object, required: true } });
const emit = defineEmits(['commentCreated']);
const notifications = useNotificationStore();
const form = reactive({ content: '' });
const { formElement, validateForm } = useFormValidation(form);
const isSubmitting = ref(false);
const rules = { content: [required(), maxLength(150)] };

async function createComment() {
  if (!validateForm(rules)) {
    return;
  }

  isSubmitting.value = true;

  try {
    const response = await axios.post(`/api/posts/${props.post.id}/comments`, form);

    notifications.success('Comment posted successfully.');
    form.content = '';
    emit('commentCreated', response.data.data);
  } catch (error) {
    notifications.error(error.response?.data || 'Could not add the comment. Please try again.');
  } finally {
    isSubmitting.value = false;
  }
}
</script>

<template>
  <form ref="formElement" class="border-t border-[var(--color-line)] pt-3" @submit.prevent="createComment">
    <label class="sr-only" :for="`comment-input-${post.id}`">Write a comment</label>
    <textarea :id="`comment-input-${post.id}`" v-model="form.content" name="content"
      class="w-full resize-y rounded-xl border border-[var(--color-line)] bg-white px-3 py-2 text-sm text-[var(--color-ink)] outline-none focus:border-[var(--color-brand)]"
      rows="2" maxlength="150" placeholder="Write a comment..." :disabled="isSubmitting" />
    <div class="mt-2 flex items-center justify-between">
      <span class="text-xs text-[var(--color-muted)]">{{ form.content.length }}/150</span>
      <button type="submit"
        class="rounded-full bg-[var(--color-brand)] px-4 py-2 text-sm font-semibold text-white transition-opacity disabled:cursor-not-allowed disabled:opacity-50"
        :disabled="!form.content.trim() || isSubmitting">
        {{ isSubmitting ? 'Posting…' : 'Comment' }}
      </button>
    </div>
  </form>
</template>
