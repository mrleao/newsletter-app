<script setup>
import SiteLayout from '@/Layouts/SiteLayout.vue'
import Pagination from '@/Components/Tables/DefaultTable/Pagination.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  articles: { type: Object, required: true },
  filters: { type: Object, default: () => ({ q: '', category_id: '' }) },
  categories: { type: Array, default: () => [] },
})

const form = useForm({
  q: props.filters?.q ?? '',
  category_id: props.filters?.category_id ?? '',
})

const toPlainText = (html) => {
  const doc = new DOMParser().parseFromString(String(html ?? ''), 'text/html')
  return (doc.body.textContent || '').trim()
}
const pickBy = (obj) =>
  Object.fromEntries(Object.entries(obj).filter(([, v]) => v !== '' && v !== null && v !== undefined))

const applyFilters = () => {
  router.get(route('site.articles.page'), pickBy(form), {
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
const redirectToEdit = (id) => {
  router.visit(route('site.read.article.page', id))
}

const showFilters = ref(false)
</script>

<template>
  <SiteLayout>
    <Head title="Notícias" />

    <main class="mx-auto max-w-7xl p-6">

      <div class="mb-10 flex w-full items-center justify-between gap-3">
        <h2 class="text-2xl font-bold text-slate-900">Notícias</h2>

        <button
          type="button"
          class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
          @click="showFilters = !showFilters"
        >
          <span class="material-symbols-outlined mr-1 text-[18px] leading-none">
            {{ showFilters ? 'filter_alt_off' : 'tune' }}
          </span>
          {{ showFilters ? 'Ocultar filtros' : 'Filtros' }}
        </button>
      </div>

      <div v-show="showFilters" class="mb-6 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <form class="grid grid-cols-1 items-end gap-3 sm:grid-cols-3" @submit.prevent="applyFilters">
          <div class="sm:col-span-2">
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

          <div class="sm:col-span-3 flex flex-col-reverse items-stretch gap-2 sm:flex-row sm:items-center sm:justify-end">
            <button
              type="button"
              class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
              @click="clearFilters"
            >
              Limpar
            </button>
            <button
              type="submit"
              class="inline-flex h-10 items-center justify-center rounded-lg bg-slate-900 px-4 text-sm font-semibold text-white transition hover:bg-slate-800"
            >
              Aplicar
            </button>
          </div>
        </form>
      </div>

      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <article
          v-for="item in articles?.data"
          :key="item.id"
          class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
        >
          <div class="relative overflow-hidden rounded-t-2xl">
            <img
              @click="redirectToEdit(item.id)"
              :src="item.image_path"
              :alt="item.title"
              class="h-48 w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105"
              style="will-change: transform; backface-visibility: hidden;"
              loading="lazy"
            />
            <div class="pointer-events-none absolute inset-0 ring-1 ring-inset ring-black/5"></div>
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-black/60 to-transparent"></div>
            <div v-if="item.category && item.category.name" class="absolute left-3 top-3">
              <span class="rounded-full bg-black/60 px-2.5 py-1 text-xs font-medium text-white backdrop-blur">
                {{ item.category.name }}
              </span>
            </div>
          </div>

          <div class="p-4">
            <h3
              class="text-base font-semibold text-slate-900 transition-colors group-hover:text-slate-950 line-clamp-2"
              :title="item.title"
            >
              {{ item.title }}
            </h3>

            <p class="mt-2 text-sm text-slate-600 line-clamp-2">
              {{ toPlainText(item.body) }}
            </p>

            <div class="mt-4 flex items-center justify-between">
              <div class="flex items-center gap-2 text-xs text-slate-500">
                <span v-if="item.price" class="hidden sm:inline">{{ item.price }}</span>
              </div>

              <button
                @click="redirectToEdit(item.id)"
                type="button"
                class="inline-flex items-center gap-1 rounded-md bg-slate-900 px-3 py-1.5 text-sm font-medium text-white transition hover:bg-slate-800"
              >
                <span>Ler mais</span>
                <span class="material-symbols-outlined text-[18px] leading-none">arrow_right_alt</span>
              </button>
            </div>
          </div>
        </article>
      </div>
    </main>

    <Pagination :data="articles" />
  </SiteLayout>
</template>

