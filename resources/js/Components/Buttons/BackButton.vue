<script setup>
defineProps({
    width: {
        type: String,
        default: 'w-32',
    },
});
</script>

<template>
    <a
    :class="width"
        class="inline-flex items-center justify-center gap-2 cursor-pointer font-medium rounded-md px-4 py-2 text-dark hover:font-bold transition"
    >
    <span class="material-symbols-outlined text-[20px] leading-none">arrow_left_alt</span>
        <span>Voltar</span>
    </a>
</template>
