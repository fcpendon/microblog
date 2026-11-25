<script setup>
import { Pencil } from '@lucide/vue';
import { reactive, ref } from 'vue';
import { useFormValidation } from '../composables/useFormValidation';
import { useNotificationStore } from '../stores/notification';
import { maxLength, required } from '../utils/validationRules';

const props = defineProps({
  comment: { type: Object, required: true },
  target: { type: Object, required: true },
});
const emit = defineEmits(['editingChanged', 'commentUpdated']);
const notifications = useNotificationStore();
const form = reactive({ content: props.comment.content });
const { formElement, validateForm } = useFormValidation(form);
const isSaving = ref(false);
const isEditing = ref(false);
const rules = { content: [required(), maxLength(150)] };

function setEditing(value) {
  if (value) {
    form.content = props.comment.content;
  }

  isEditing.value = value;
  emit('editingChanged', value);
}

async function updateComment() {
  if (!validateForm(rules)) {
    return;
  }

  isSaving.value = true;

  try {
    const response = await axios.put(`/api/comments/${props.comment.id}`, form);

    notifications.success('Comment updated successfully.');
    setEditing(false);
    emit('commentUpdated', response.data.data);
  } catch (error) {
    notifications.error(error.response?.data || 'Could not update the comment. Please try again.');
  } finally {
    isSaving.value = false;
  }
}
</script>

<template>
  <button v-if="!isEditing" type="button"
    class="grid size-8 place-items-center rounded-full text-[var(--color-muted)] hover:bg-[var(--color-canvas)] hover:text-[var(--color-brand)]"
    aria-label="Edit comment" title="Edit comment" @click="setEditing(true)">
    <Pencil :size="15" aria-hidden="true" />
  </button>
  <Teleport v-if="isEditing" :to="target">
    <form ref="formElement" class="mt-2" @submit.prevent="updateComment">
      <label class="sr-only" :for="`comment-edit-${comment.id}`">Edit comment</label>
      <textarea :id="`comment-edit-${comment.id}`" v-model="form.content" name="content"
        class="w-full resize-y rounded-xl border border-[var(--color-line)] bg-white px-3 py-2 text-sm text-[var(--color-ink)] outline-none focus:border-[var(--color-brand)]"
        rows="2" maxlength="150" :disabled="isSaving" />
      <div class="mt-2 flex items-center justify-between">
        <span class="text-xs text-[var(--color-muted)]">{{ form.content.length }}/150</span>
        <div class="flex items-center gap-2">
          <button type="button" class="text-xs font-semibold text-[var(--color-muted)] hover:text-[var(--color-ink)]"
            :disabled="isSaving" @click="setEditing(false)">
            Cancel
          </button>
          <button type="submit" class="text-xs font-semibold text-[var(--color-brand)] disabled:opacity-50"
            :disabled="!form.content.trim() || isSaving">
            {{ isSaving ? 'Saving…' : 'Save' }}
          </button>
        </div>
      </div>
    </form>
  </Teleport>
</template>
