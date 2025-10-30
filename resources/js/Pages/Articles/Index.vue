<script setup>
import { Head, router, useForm } from '@inertiajs/vue3'
import { computed, watch } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import NewRegisterButton from '@/Components/Buttons/NewRegisterButton.vue'
import ActionTd from '@/Components/Tables/DefaultTable/ActionTd.vue'
import DefaultTable from '@/Components/Tables/DefaultTable.vue'
import Td from '@/Components/Tables/DefaultTable/Td.vue'

const props = defineProps({
    articles: { 
      type: Object,
      required: true
    },
    filters: {
      type: Object,
      default: () => ({ 
        search: '' 
      })
    },
    categories: {
      type: Array,
      default: () => []
    },
})

const tableTitles = ['Imagem', 'Título', 'Publicado em', 'Ações']

const form = useForm({
  q: props.filters?.q ?? '',
  category_id: props.filters?.category_id ?? '',
})

const onDelete = (id) => {
  form.delete(route('articles.delete', id), {
    preserveScroll: true
  })
}

const redirectToEdit = (id) => {
  router.visit(route('articles.updateView', id))
}

const rows = computed(() =>
  Array.isArray(props.articles?.data) ? props.articles.data : []
)

const pickBy = (obj) =>
  Object.fromEntries(
    Object.entries(obj).filter(([, v]) => v !== '' && v !== null && v !== undefined)
  )

const applyFilters = () => {
  router.get(route('articles.index'), pickBy(form), {
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
  <Head title="Artigos" />
  <AppLayout title="Artigos">

    <div class="flex w-full items-center">
        <h1 class="text-2xl font-bold text-gray-900">Artigos</h1>
        <div class="ml-auto">
          <NewRegisterButton :href="route('articles.storeView')" />
        </div>
    </div>
    <div class="mt-6" >
        <div class="mt-8 mb-12">
            <form
              class="mb-6 grid grid-cols-1 items-end gap-3 sm:grid-cols-3"
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

              <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Categoria</label>
                <select
                  v-model="form.category_id"
                  class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                >
                  <option value="">Todas</option>
                  <option v-for="c in categories" :key="c.id" :value="c.id">
                    {{ c.name }}
                  </option>
                </select>
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

      <DefaultTable :titles="tableTitles" :data="articles">
        <template #td>
          <template v-if="rows.length">
            <tr
              v-for="(item, index) in rows"
              :key="item.id ?? index"
              class="cursor-pointer hover:bg-indigo-50/40"
              :class="index % 2 ? 'bg-gray-50' : 'bg-white'"
              @dblclick="redirectToEdit(item.id)"
            >
              <Td class="w-12 whitespace-nowrap">
                <div
                  class="group h-10 w-10 rounded-md bg-gray-50 border border-gray-200
                        overflow-hidden grid place-items-center shadow-sm"
                >
                  <img
                    :src="item.image_path"
                    :alt="item.title"
                    class="h-8 w-8 object-cover object-center rounded
                          transition-transform duration-200 group-hover:scale-105"
                    loading="lazy"
                    decoding="async"
                  />
                </div>
              </Td>
              <Td class="whitespace-nowrap">
                <p class="truncate font-medium text-gray-900">
                  {{ item.title }}  
                </p>
                <p class="truncate text-gray-400">
                  {{ item.slug }}
                </p>
              </Td>
              <Td class="w-32 whitespace-nowrap">
                {{ item.published_at }}
              </Td>

              <ActionTd
                @confirm-delete="onDelete"
                :editRoute="'articles.updateView'"
                :itemId="String(item.id)"
              />
            </tr>
          </template>

        </template>
      </DefaultTable>
    </div>
  </AppLayout>
</template>
