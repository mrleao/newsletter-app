<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'
import FormSection from '@/Components/Forms/FormSection.vue'
import BackButton from '@/Components/Buttons/BackButton.vue'
import PrimaryButton from '@/Components/Buttons/PrimaryButton.vue'

const props = defineProps({
  category: { type: Object, required: true },
  parentOptions: { type: [Array, Object], default: () => [] }
})

const normalizeId = (v) => {
  if (v === null || v === undefined || v === '') return null
  const n = Number(v)
  return Number.isFinite(n) ? n : String(v)
}

const toSlug = (v) => {
  const s = String(v ?? '')
  const base = typeof s.normalize === 'function' ? s.normalize('NFD').replace(/[\u0300-\u036f]/g, '') : s
  return base.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '').slice(0, 120)
}

const form = useForm({
  id: props.category?.category?.id ?? '',
  name: props.category?.category?.name ?? '',
  slug: props.category?.category?.slug ?? '',
  parent_id: normalizeId(props.category?.category?.parent_id ?? null)
})

const slugTouched = ref(!!form.slug)
watch(() => form.name, (val) => { if (!slugTouched.value) form.slug = toSlug(val) })
const onSlugInput = () => { slugTouched.value = true }

const normalized = computed(() => {
  const list = []
  const raw = props.parentOptions
  if (Array.isArray(raw)) {
    for (const item of raw) {
      if (item && typeof item === 'object') {
        list.push({
          id: normalizeId(item.id ?? item.value ?? null),
          name: String(item.name ?? item.label ?? ''),
          parent_id: normalizeId(item.parent_id ?? null)
        })
      } else {
        list.push({ id: normalizeId(item), name: String(item), parent_id: null })
      }
    }
  } else if (raw && typeof raw === 'object') {
    for (const [idKey, nameVal] of Object.entries(raw)) {
      list.push({ id: normalizeId(idKey), name: String(nameVal ?? ''), parent_id: null })
    }
  }
  return [{ id: null, name: 'Nenhuma (raiz)', parent_id: null }, ...list].filter(o => o.id !== normalizeId(props.category?.id))
})

const optionById = computed(() => {
  const m = new Map()
  for (const o of normalized.value) m.set(o.id, o)
  return m
})

const childrenMap = computed(() => {
  const m = new Map()
  for (const o of normalized.value) {
    const pid = o.parent_id ?? null
    if (!m.has(pid)) m.set(pid, [])
    m.get(pid).push(o)
  }
  for (const arr of m.values()) arr.sort((a, b) => a.name.localeCompare(b.name))
  return m
})

const pathToSelected = computed(() => {
  const id = form.parent_id
  if (id === null || id === undefined) return []
  const path = []
  let cur = optionById.value.get(id) || null
  let guard = 0
  while (cur && guard++ < 100) {
    if (cur.id !== null) path.push(cur)
    cur = cur.parent_id != null ? optionById.value.get(cur.parent_id) : null
  }
  return path.reverse()
})

function collectDescendants(startId, startDepth, acc) {
  const children = childrenMap.value.get(startId) || []
  for (const c of children) {
    if (c.id === startId) continue
    acc.push({ item: c, depth: startDepth + 1 })
    collectDescendants(c.id, startDepth + 1, acc)
  }
}

const cascade = computed(() => {
  const id = form.parent_id
  if (id === null || id === undefined) return []
  const out = []
  const path = pathToSelected.value
  for (let i = 0; i < path.length; i++) out.push({ item: path[i], depth: i })
  const baseDepth = path.length - 1
  collectDescendants(id, Math.max(baseDepth, 0), out)
  return out
})

const reduceToId = (opt) => {
  const v = opt ? (opt.id ?? opt.value ?? null) : null
  return normalizeId(v)
}

const onSubmit = () => {
    form.patch(route('categories.update', form.id), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
  <Head title="Editar Categoria" />
  <AppLayout title="Editar Categoria">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Categoria</h2>
    </template>
    <FormSection @submit.prevent="onSubmit" class="p-6 space-y-6">
      <template #form>
        <div class="w-full sm:grid lg:grid-cols-2 sm:grid-cols-1 gap-2">
          <div class="lg:col-span-2 col-span-4">
            <div class="sm:grid lg:grid-cols-4 sm:grid-cols-2 sm:gap-4 gap-2">
              <div class="col-span-4">
                <InputLabel :required="true" for="name" value="Nome da categoria" />
                <TextInput id="name" type="text" maxlength="50" v-model="form.name" placeholder="Ex.: Esportes" required />
                <InputError class="mt-2" :message="form.errors.name" />
              </div>
              <div class="col-span-4">
                <InputLabel for="slug" value="Slug (automático a partir do nome, edite se quiser)" />
                <TextInput id="slug" type="text" v-model="form.slug" autocomplete="off" placeholder="auto-gerado se vazio" @input="onSlugInput" required />
                <InputError class="mt-2" :message="form.errors.slug" />
              </div>
              <div class="col-span-4">
                <InputLabel for="parent-select" value="Categoria pai (opcional)" />
                <v-select
                  id="parent-select"
                  class="mt-1"
                  :options="normalized"
                  :reduce="reduceToId"
                  v-model="form.parent_id"
                  label="name"
                  :clearable="true"
                  :searchable="true"
                  placeholder="Selecione ou digite para pesquisar…"
                >
                  <template #option="{ name }">
                    <span class="truncate">{{ name }}</span>
                  </template>
                  <template #selected-option="{ name }">
                    <span class="truncate">{{ name }}</span>
                  </template>
                  <template #no-options>
                    <div class="px-3 py-2 text-sm text-gray-500">Nenhum resultado</div>
                  </template>
                </v-select>
                <InputError class="mt-2" :message="form.errors.parent_id" />
              </div>
            </div>
          </div>

          <div v-if="cascade.length" class="col-span-2 space-y-2">
            <div class="text-sm text-gray-500">Árvore de categorias</div>
            <ul class="relative">
              <li
                v-for="row in cascade"
                :key="row.item.id"
                class="group flex items-center gap-3"
                :style="{ paddingLeft: `${row.depth * 20}px` }"
              >
                <div class="relative">
                  <div class="h-5 w-5 rounded-full bg-indigo-100 border border-indigo-300 flex items-center justify-center text-[10px] font-semibold text-indigo-700">
                    {{ row.depth }}
                  </div>
                  <div v-if="row.depth > 0" class="absolute -left-5 top-2 h-px w-5 bg-indigo-200"></div>
                </div>
                <div
                  class="flex-1 rounded-xl px-3 py-2 text-sm"
                  :class="row.item.id === form.parent_id
                    ? 'bg-gradient-to-r from-indigo-50 to-indigo-100 text-indigo-900 ring-1 ring-indigo-200'
                    : 'bg-gray-50 text-gray-800 ring-1 ring-gray-200'"
                >
                  <div class="flex items-center justify-between">
                    <span class="font-medium">{{ row.item.name }}</span>
                    <span
                      class="text-[10px] px-2 py-0.5 rounded-full"
                      :class="row.item.id === form.parent_id ? 'bg-indigo-200 text-indigo-900' : 'bg-gray-200 text-gray-700'"
                    >
                      Nv {{ row.depth }}
                    </span>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </template>

      <template #actions>
        <BackButton :href="route('categories.index')" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
          Voltar
        </BackButton>
        <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
          Salvar
        </PrimaryButton>
      </template>
    </FormSection>
  </AppLayout>
</template>

<style>
.vs__dropdown-toggle {
  border-radius: 0.375rem;
  border: 1px solid rgb(209 213 219);
  box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
}
.vs__search::placeholder { color: rgb(156 163 175); }
.vs__dropdown-menu {
  z-index: 20;
  max-height: 15rem;
  overflow: auto;
  border: 1px solid rgb(229 231 235);
  box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
}
</style>
