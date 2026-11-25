import js from '@eslint/js';
import pluginVue from 'eslint-plugin-vue';
import globals from 'globals';
import eslintConfigPrettier from 'eslint-config-prettier';

export default [
  // Ignore build output directories
  {
    ignores: ['dist/**'],
  },

  // Base ESLint recommended rules for JavaScript
  js.configs.recommended,

  // Vue 3 Strongly Recommended rules (includes base & essential)
  ...pluginVue.configs['flat/strongly-recommended'],

  // Browser environment and custom rule overrides
  {
    files: ['**/*.{js,mjs,vue}'],
    languageOptions: {
      ecmaVersion: 'latest',
      sourceType: 'module',
      globals: {
        ...globals.browser,
        axios: true,
        "_": "readonly",
      },
    },
    rules: {
      // Override or add custom rules here if needed
    },
  },
  eslintConfigPrettier,
];
