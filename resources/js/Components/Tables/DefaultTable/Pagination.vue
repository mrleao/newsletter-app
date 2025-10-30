<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    data: Object,
});

const shouldHidePagination = computed(() => {
  return !props.data?.prev_page_url && !props.data?.next_page_url
})

const cleanLinks = computed(() => {
  const arr = [...(props.data?.links ?? [])]
  if (arr.length > 0) arr.shift()
  if (arr.length > 0) arr.pop()
  return arr
})

</script>

<template>
<div v-if="!shouldHidePagination">
    <div class="m-2 flex flex-row flex-nowrap justify-between md:justify-center items-center"  aria-label="Pagination">
        <Link :href="data.prev_page_url ? data.prev_page_url : '#'"
            class="flex mr-4 w-6 h-6 mx-1 justify-center items-center bg-white text-dark hover:bg-dark hover:text-white rounded-full border border-gray-200 hover:border-gray-300">
            <span class="material-symbols-outlined">
                arrow_left_alt
            </span>
        </Link>
        <Link v-for="link in cleanLinks" :key="link.label" :href="link.url"
        :class="{'bg-dark text-white' : link.active, 'bg-white text-dark hover:bg-dark hover:text-white' : !link.active}"
        class="md:flex w-7 h-7 text-center mx-1 justify-center items-center rounded-full border border-gray-200 hover:border-gray-300">
            {{ link.label }}
        </Link>
        <Link :href="data.next_page_url? data.next_page_url : '#'" 
            class="flex ml-4 w-6 h-6 mx-1 justify-center items-center bg-white text-dark hover:bg-dark hover:text-white rounded-full border border-gray-200 hover:border-gray-300">
            <span class="material-symbols-outlined">
                arrow_right_alt
            </span>
        </Link>
    </div>
</div>
<div v-if="!data?.total || data?.total == 0" class="text-center p-5 md:mt-12 md:mb-12 mb-8 mt-8">
    <div class="text-gray-500 text-3xl">EITA!</div>
    <div class="text-gray-500">Nenhum registro encontrado 😕</div>  
</div>
</template>
