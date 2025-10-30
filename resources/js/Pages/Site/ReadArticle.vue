<script setup>
import SiteLayout from '@/Layouts/SiteLayout.vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    article: { 
        type: Object,
        required: true
    }
})

const formatDate = (iso) => {
  if (!iso) return ''
  try {
    return new Intl.DateTimeFormat('pt-BR', { dateStyle: 'medium', timeZone: 'America/Fortaleza' }).format(new Date(iso))
  } catch {
    return iso
  }
}

const redirectToBack = () => {
  router.visit(route('site.articles.page'))
}
</script>

<template>
  <SiteLayout>
    <main class="mx-auto max-w-3xl p-6 bg-white">
      <header class="mb-6 flex gap-4 md:grid-cols-2 md:items-center">
        <div @click="redirectToBack" class="cursor-pointer w-12 hover:opacity-50">
            <span class="material-symbols-outlined">
                arrow_back
            </span>
        </div>
        <div>
            <h1 class="text-3xl font-bold leading-tight text-slate-900">{{ article.title }}</h1>
            <div class="mt-2 text-sm text-slate-500">
            <time :datetime="article.published_at">{{ formatDate(article.published_at) }}</time>
            <span v-if="article?.category?.name"> • {{ article.category.name }}</span>
            </div>
        </div>
      </header>

      <figure v-if="article.image_path" class="mb-6 overflow-hidden rounded-xl border border-slate-200">
        <img :src="article.image_path" :alt="article.title" class="w-full object-cover" loading="lazy" />
      </figure>

      <article class="prose prose-slate max-w-none" v-html="article.body"></article>
    </main>
  </SiteLayout>
</template>
