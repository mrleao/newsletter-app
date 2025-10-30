<script setup>
import { ref } from 'vue'
import ModalConfirmDelete from '@/Components/Modals/ModalConfirmDelete.vue';
import ModalConfirmButton from '@/Components/Buttons/ModalConfirmDeleteButton.vue';
import ModalCancelButton from '@/Components/Buttons/ModalCancelButton.vue';

const props =  defineProps({
    actionDelete: {
        type: String,
        default: ''
    },
    actionEdit: {
        type: String,
        default: ''
    },
    editRoute: {
        type: String,
        default: ''
    },
    itemId: {
        type: String,
        default: ''
    }
});

const emit = defineEmits(['confirm-delete']);

const openModal = ref(false);

const confirmDelete = () => {
    emit('confirm-delete', props.itemId);
    openModal.value = false;
}
</script>

<template>
    <td class="w-24 px-4 py-2 text-sm">
        <div class="flex item-right justify-end">
            <div class="pr-2 hover:cursor-pointer w-6 mr-2 transform hover:text-dark hover:scale-110 text-dark">
                <a type="button" :href="route(editRoute, itemId)">
                    <svg class="w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </a>
            </div>
            <div @click="openModal = true; itemId = itemId" class="hover:cursor-pointer w-6 mr-2 transform hover:text-red-600 text-red-500  hover:scale-110">
                <svg class="w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
        </div>
    </td>

    <ModalConfirmDelete :show="openModal" @close="openModal = false">
        <template #title>
            Tem certeza?
        </template>

        <template #content>
            Tem certeza que deseja apagar este registro?
        </template>

        <template #footer>
            <ModalCancelButton @click="openModal = false">
                Cancelar
            </ModalCancelButton>
            <ModalConfirmButton @click.prevent="confirmDelete">
                Apagar
            </ModalConfirmButton>
        </template>
    </ModalConfirmDelete>
</template>