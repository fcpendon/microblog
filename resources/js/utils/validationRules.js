export const required = (message = 'This field is required.') => value => {
  if (Array.isArray(value)) {
    return value.length > 0 || message;
  }

  if (typeof value === 'string') {
    return value.trim().length > 0 || message;
  }

  return (value !== null && value !== undefined && value !== '') || message;
};

export const minLength = (min, message) => value => {
  if (!value) {
    return true;
  }

  return value.length >= min || message || `Must be at least ${min} characters.`;
};

export const maxLength = (max, message) => value => {
  if (!value) {
    return true;
  }

  return value.length <= max || message || `Must be at most ${max} characters.`;
};

export const email = (message = 'Please enter a valid email address.') => value => {
  if (!value) {
    return true;
  }

  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) || message;
};

export const matches = (pattern, message = 'The value has an invalid format.') => value => {
  if (!value) {
    return true;
  }

  return pattern.test(value) || message;
};

export const matchesField = (targetField, message = 'Fields do not match.') => (value, formData) => {
  return value === formData[targetField] || message;
};

export const fileSize = (max, message) => value => {
  if (!value) {
    return true;
  }

  return value.size < max || message || `File size must be less than ${max} bytes.`;
};
