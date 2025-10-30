<script setup>
const props = defineProps({
  as: { type: String, default: 'td' }
})
</script>

<template>
  <component :is="as" class="px-4 py-3 text-sm text-gray-700 align-middle">
    <slot />
  </component>
</template>
