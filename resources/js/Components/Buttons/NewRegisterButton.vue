<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <a :type="type" class="sm:w-64 w-full inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 text-base font-medium shadow-sm text-dark bg-white border border-dark hover:text-white hover:bg-dark font-bold disabled:opacity-25 transition">
        <font-awesome-icon class="mr-2 text-main" icon="circle-plus" /> Cadastrar
    </a>
</template>
