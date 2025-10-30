<script setup>
defineProps({
    value: String,
    required: {type: Boolean, default: false},
    display: {type: String, default: "block"},
});
</script>

<template>
    <label :class="display" class="mt-3 text-sm text-dark">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
        <span v-if="required" class="ml-1 text-sm text-red-400">*</span>
    </label>
</template>
