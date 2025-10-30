<script setup>
import { computed, useSlots } from 'vue';

defineEmits(['submitted']);

const hasActions = computed(() => !! useSlots().actions);
</script>

<template>
        <form @submit.prevent="$emit('submitted')">
            <slot name="form" />
            <div v-if="hasActions" class="py-5px-2 mt-10">
                <div class="py-5px-2 mt-10">
                    <div class="text-right">
                        <slot name="actions" />
                    </div>
                </div>
            </div>
        </form>
</template>
