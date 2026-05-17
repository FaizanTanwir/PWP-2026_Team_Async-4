module.exports = {
  env: {
    node: true,
  },
  extends: [
    'eslint:recommended',
    'plugin:vue/vue3-recommended', // Strictest rules for Vue 3
  ],
  rules: {
    // Add custom rules here
    'vue/multi-word-component-names': 'off', // Allows names like 'Home.vue' instead of 'HomePage.vue'
    'no-unused-vars': 'warn',
  }
}