import { ref } from 'vue';

export function useFormValidation(values) {
  const formElement = ref(null);

  function validateForm(fieldRules, fieldValues = values) {
    for (const [fieldName, rules] of Object.entries(fieldRules)) {
      const input = formElement.value.elements.namedItem(fieldName);
      const error = rules.find(rule => rule(fieldValues[fieldName], fieldValues) !== true);

      if (typeof error === 'string') {
        input.setCustomValidity(error);
        input.reportValidity();
        input.setCustomValidity('');

        return false;
      }
    }

    return formElement.value.reportValidity();
  }

  return {
    formElement,
    validateForm,
  };
}
