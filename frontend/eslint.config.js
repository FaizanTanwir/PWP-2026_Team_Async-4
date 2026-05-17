import js from '@eslint/js';
import pluginVue from 'eslint-plugin-vue';
import globals from 'globals';

export default [
  // 1. Base JS rules
  js.configs.recommended,
  
  // 2. Vue 3 rules (Flat config version)
  ...pluginVue.configs['flat/recommended'],
  
  // 3. Custom settings & Globals
  {
    languageOptions: {
      ecmaVersion: 'latest',
      sourceType: 'module',
      globals: {
        ...globals.browser,
        ...globals.node,
      },
    },
    rules: {
      'vue/multi-word-component-names': 'off',
      'no-unused-vars': 'warn',
      'no-undef': 'error',
    },
  },
  
  // 4. Ignore build files
  {
    ignores: ['dist/**', 'node_modules/**', '.render/**'],
  },
];