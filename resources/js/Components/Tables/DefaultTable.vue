<script setup>
import Pagination from '@/Components/Tables/DefaultTable/Pagination.vue';

const props =  defineProps({
    titles: {
        type: Array,
        default: []
    },
    tableFilds: {
        type: Array,
        default: []
    },
    data: {
        type: Object,
        default: []
    }
});

</script>

<template>
 <section class="mx-auto">
    <div v-show="data.data.length > 0" class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full py-2">
                <div class="overflow-hidden border border-gray-200 dark:border-gray-700 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-500">
                            <tr>
                                <th v-for="(item, index) in titles" :key="index" scope="col" class="px-2 py-3.5 text-sm font-normal text-left text-white">
                                    {{ item }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <slot name="td"/>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <Pagination :data="data"></Pagination>
</section>
</template>