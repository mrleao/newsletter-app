<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type" class="disabled:bg-gray-300 w-42 hover:cursor-pointer font-medium text-center rounded-md px-4 py-2 rounded-3xl bg-secondy hover:bg-opacity-80 text-md text-white hover:text-opacity-85 hover:font-bold rounded-xl transition duration-200">
        <slot />
    </button>
</template>
