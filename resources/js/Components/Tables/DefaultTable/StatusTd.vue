<script setup>
defineProps({
    inactive: {
        type: Boolean,
        default: false,
    },
    active: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div v-if="active" class="inline-flex items-center px-3 py-1 rounded-full gap-x-2 text-emerald-500 bg-emerald-100/60 dark:bg-gray-800">
        <font-awesome-icon icon="check" />
        <h2 class="text-sm font-normal"><slot /></h2>
    </div>
    <div v-else-if="inactive" class="inline-flex items-center px-3 py-1 rounded-full gap-x-2 text-red-500 bg-red-100/60 dark:bg-gray-800">
        <font-awesome-icon icon="xmark" />
        <h2 class="text-sm font-normal"><slot /></h2>
    </div>
</template>
