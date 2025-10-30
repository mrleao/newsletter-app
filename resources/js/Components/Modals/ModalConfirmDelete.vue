<script setup>
import Modal from './Modal.vue';

const emit = defineEmits(['close']);

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: 'md',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const close = () => {
    emit('close');
};
</script>

<template>
    <Modal
        :show="show"
        :max-width="maxWidth"
        :closeable="closeable"
        @close="close"
    >
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="text-center bg-white rounded-lg p-4 text-base text-red-500" role="alert">
                <span class="text-5xl"><font-awesome-icon icon="trash-can" /></span>            
            </div>
            <div class="mt-2 text-center text-xl font-bold">
                Tem certeza?
            </div>
            <div class="mt-2 text-center text-sm text-gray-600">
                Você realmente quer deletar esse registro? Essa ação não pode ser desfeita.
            </div>
        </div>
        <div class="p-3 mt-2 text-center space-x-4 md:block">
            <slot name="footer" />
        </div>
    </Modal>
</template>
