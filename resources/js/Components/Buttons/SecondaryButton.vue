<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template> 

    <button :type="type" class="w-full inline-flex items-center justify-center whitespace-nowrap rounded-md border border-transparent bg-black px-4 py-2 text-base font-medium text-main shadow-sm hover:bg-white hover:border hover:border-main font-bold transition">
        <slot />
    </button>
</template>
