export default function validateForm(form, fieldRules, values) {
  let isValid = true;

  Object.entries(fieldRules).forEach(([fieldName, rules]) => {
    const input = form.elements.namedItem(fieldName);
    if (!input || typeof input.setCustomValidity !== 'function') return;

    const error = rules.map(rule => rule(values[fieldName])).find(result => result !== true);
    input.setCustomValidity(typeof error === 'string' ? error : '');
    if (error) {
      const clearError = () => input.setCustomValidity('');
      input.addEventListener('input', clearError, { once: true });
      input.addEventListener('change', clearError, { once: true });
    }
    if (error) isValid = false;
  });

  return form.reportValidity() && isValid;
}