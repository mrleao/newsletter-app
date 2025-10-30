<script setup>
import { Head, router, useForm } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import NewRegisterButton from '@/Components/Buttons/NewRegisterButton.vue'
import ActionTd from '@/Components/Tables/DefaultTable/ActionTd.vue'
import DefaultTable from '@/Components/Tables/DefaultTable.vue'
import Td from '@/Components/Tables/DefaultTable/Td.vue'

const props = defineProps({
  categories: { 
    type: Object,
    required: true
  },
  filters: {
    type: Object, default: () => ({
        search: '' 
    })
  }
})

const tableTitles = ['Nome', '']

const form = useForm({
  q: props.filters?.q ?? '',
  category_id: props.filters?.category_id ?? '',
})

const onDelete = (id) => {
  form.delete(route('categories.delete', id), {
    preserveScroll: true
  })
}

const redirectToEdit = (id) => {
  router.visit(route('categories.updateView', id))
}

const rows = computed(() =>
  Array.isArray(props.categories?.data) ? props.categories.data : []
)


const pickBy = (obj) =>
  Object.fromEntries(
    Object.entries(obj).filter(([, v]) => v !== '' && v !== null && v !== undefined)
  )

const applyFilters = () => {
  router.get(route('categories.index'), pickBy(form), {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  })
}

const clearFilters = () => {
  form.q = ''
  form.category_id = ''
  applyFilters()
}
</script>

<template>
  <Head title="Categorias" />
  <AppLayout title="Categorias">

    <div class="flex w-full items-center">
        <h1 class="text-2xl font-bold text-gray-900">Categorias</h1>
        <div class="ml-auto">
          <NewRegisterButton :href="route('categories.storeView')" />
        </div>
    </div>
    <div class="mt-6" >
        <div class="mt-8 mb-12">
            <form
              class="mb-6 grid grid-cols-1 items-end gap-3 sm:grid-cols-2"
              @submit.prevent="applyFilters"
            >
              <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Título</label>
                <input
                  v-model="form.q"
                  type="text"
                  placeholder="Buscar por título…"
                  class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                  @keyup.enter="applyFilters"
                />
              </div>

              <div class="flex gap-2">
                <button
                  type="submit"
                  class="inline-flex h-10 w-full items-center justify-center rounded-lg bg-slate-900 px-4 text-sm font-semibold text-white transition hover:bg-slate-800"
                >
                  Aplicar
                </button>
                <button
                  type="button"
                  class="inline-flex h-10 w-full items-center justify-center rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                  @click="clearFilters"
                >
                  Limpar
                </button>
              </div>
            </form>
        </div>

      <DefaultTable :titles="tableTitles" :data="categories">
        <template #td>
          <template v-if="rows.length">
            <tr
              v-for="(item, index) in rows"
              :key="item.id ?? index"
              class="cursor-pointer hover:bg-indigo-50/40"
              :class="index % 2 ? 'bg-gray-50' : 'bg-white'"
              @dblclick="redirectToEdit(item.id)"
            >
              <Td class="whitespace-nowrap">
                {{ item.name }}
              </Td>

              <ActionTd
                @confirm-delete="onDelete"
                :editRoute="'categories.update'"
                :itemId="String(item.id)"
              />
            </tr>
          </template>

        </template>
      </DefaultTable>
    </div>
  </AppLayout>
</template>
