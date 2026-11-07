export const userForm = {
  name: [
    value => value && value.length >= 4 || 'Please input at least 4 characters',
    value => value && value.length <= 100 || 'Please input at most 100 characters',
    value => /^[a-zA-Z\s.']+$/.test(value) || 'Name format is invalid. Input must be alphabetic with \' and .',
  ],
  username: [
    value => value && value.length >= 4 || 'Please input at least 4 characters',
    value => value && value.length <= 20 || 'Please input at most 20 characters',
    value => /^[a-zA-Z0-9]+$/.test(value) || 'Username should only be alphabetic or alphanumeric',
  ],
  email: [
    value => !!value || 'Please enter an email address',
    value => value && value.length <= 100 || 'Please input at most 100 characters',
    value => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) || 'Please enter a valid email address',
  ],
  password: [
    value => value && value.length >= 8 || 'Please input at least 8 characters',
    value => value && value.length <= 16 || 'Please input at most 16 characters',
    value => /^(?=.*\d)(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/.test(value) || 'Password should be alphanumeric',
  ],
}

export const postForm = {
  content: [
    value => value && value.length > 0 || 'This field is required',
    value => value && value.length <= 150 || 'Please input at most 150 characters',
  ],
  image: [
    value => value && value.size > 0 || 'Image cannot be empty',
    value => value && value.size < 5000000 || 'Image file size should be less than 5 MB',
  ],
}
