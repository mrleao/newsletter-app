<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
    link: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <button v-if="link == null" :type="type" class="cursor-pointer mb-2 md:mb-0 bg-white px-5 py-2 text-sm shadow-sm font-medium tracking-wider border text-gray-600 rounded-full hover:shadow-lg hover:bg-gray-100">
        <slot />
    </button>
    <a v-else :href="link" class="cursor-pointer text-center mb-2 md:mb-0 bg-white px-5 py-2 text-sm shadow-sm font-medium tracking-wider border text-gray-600 rounded-full hover:shadow-lg hover:bg-gray-100">
        <slot />
    </a>
</template>
