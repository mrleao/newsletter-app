<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type" class="w-full inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 text-base font-medium shadow-sm text-red-600 bg-white border border-red-600 hover:text-white hover:bg-red-600 font-bold disabled:opacity-25 transition">
        <slot />
    </button>
</template>
