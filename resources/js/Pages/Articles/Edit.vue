<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import BackButton from '@/Components/Buttons/BackButton.vue';
import PrimaryButton from '@/Components/Buttons/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import 'vue-select/dist/vue-select.css'
import Quill from 'quill'
import 'quill/dist/quill.snow.css'

const props = defineProps({
    article: { 
        type: Object,
        required: true
    },
    articlesCategories:  { 
      type: Array, 
      default: () => [] 
    }
})

const element = ref(null)
let quill

const form = useForm({
  id: props.article?.id ?? '',
  category_id: props.article?.category_id ?? null,
  image: props.article?.image_path ?? null,
  slug: props.article?.slug ?? '',
  title: props.article?.title ?? '',
  body: props.article?.body ?? '',
  status: props.article?.status ?? 'draft',
})

const MAX_IMAGE_MB = 2
const MAX_IMAGE_BYTES = MAX_IMAGE_MB * 1024 * 1024
const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp']
const ACCEPT = ALLOWED_TYPES.join(',')

const imagePreview = ref(form.image);
const imageInput = ref(form.image);
const imageError = ref('')
const statusArticle = ref('draft');

const rawCategories = computed(() => Array.isArray(props.articlesCategories)
  ? props.articlesCategories
  : Object.values(props.articlesCategories ?? {}))

const treeRoots = computed(() => buildTree(rawCategories.value))
const flatTree  = computed(() => flattenTree(treeRoots.value))

function buildTree(list) {
  const byId = new Map()
  list.forEach(i => byId.set(i.id, { id: i.id, name: i.name, parent_id: i.parent_id ?? null, children: [] }))
  const roots = []
  byId.forEach(node => {
    if (node.parent_id == null) {
      roots.push(node)
    } else {
      const p = byId.get(node.parent_id)
      p ? p.children.push(node) : roots.push(node)
    }
  })
  const sortRec = (arr) => {
    arr.sort((a,b) => a.name.localeCompare(b.name))
    arr.forEach(n => sortRec(n.children))
  }
  sortRec(roots)
  return roots
}

function flattenTree(nodes, depth = 0, out = []) {
  nodes.forEach(n => {
    out.push({ id: n.id, name: n.name, depth, hasChildren: n.children.length > 0 })
    flattenTree(n.children, depth + 1, out)
  })
  return out
}

function toggleCategory(id) {
  form.category_id = (form.category_id === id) ? null : id
}

onMounted(() => {
  const SizeStyle = Quill.import('attributors/style/size')
  SizeStyle.whitelist = Array.from({ length: 23 }, (_, i) => `${i + 8}px`)
  Quill.register(SizeStyle, true)

  quill = new Quill(element.value, {
    theme: 'snow',
    placeholder: 'Quer escrever algo incrível? Digite aqui 🚀',
    modules: { toolbar: '#editor-toolbar' }
  })
  const container = element.value
  container.addEventListener('mousedown', (e) => {
    const editorEl = container.querySelector('.ql-editor')
    if (editorEl && !editorEl.contains(e.target)) {
      e.preventDefault()
      quill.focus()
      quill.setSelection(quill.getLength(), 0)
    }
  })
  quill.root.innerHTML = form.body
})

onBeforeUnmount(() => { quill = null })

function syncQuillToForm() {
  if (quill) form.body = quill.root.innerHTML
}

function submit() {
  form.status = statusArticle.value

  syncQuillToForm()
  if (imageInput.value) {
      form.image = imageInput.value.files[0];
  }
  
  if (form.category_id === null) {
      form.errors.category_id = 'A categoria é obrigatória.';
      return;
  }

  form.post(route('articles.update', form.id), {
      preserveScroll: true,
      onSuccess: () => form.reset(),
  })
}

const updateimagePreview = () => {
  const file = imageInput.value?.files?.[0]
  imageError.value = ''
  imagePreview.value = null
  form.image = null
  if (!file) return

  if (!ALLOWED_TYPES.includes(file.type)) {
    imageError.value = 'Formato inválido. Aceitos: JPG, PNG, WEBP.'
    imageInput.value.value = ''
    return
  }
  if (file.size > MAX_IMAGE_BYTES) {
    imageError.value = `Imagem muito grande. Máximo ${MAX_IMAGE_MB} MB.`
    imageInput.value.value = ''
    return
  }

  form.image = file
  const reader = new FileReader()
  reader.onload = e => { imagePreview.value = e.target.result }
  reader.readAsDataURL(file)
}

const selectNewimage = () => { imageInput.value?.click() }

</script>

<template>
  <AppLayout title="Criar artigo">
    <template #header>
      <div class="flex items-center gap-3">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Criar artigos</h2>
      </div>
    </template>

    <form @submit.prevent="submit" enctype="multipart/form-data" class="p-6">
      <div class="sticky top-0 z-40 -mx-6 px-6 py-3 bg-white/80 backdrop-blur border-b flex items-center gap-3">
        <BackButton
          :href="route('articles.index')"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          Voltar
        </BackButton>

        <div class="ml-auto flex gap-3">
          <PrimaryButton
            type="submit"
            @click="statusArticle = 'draft'"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            Salvar rascunho
          </PrimaryButton>

          <PrimaryButton
            type="submit"
            @click="statusArticle = 'published'"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            Publicar
          </PrimaryButton>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mt-4">
        <div class="lg:col-span-8">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 ">
            <div class="md:col-span-2">
              <InputLabel :required="true" for="title" value="Título" />
              <TextInput
                :required="true"
                id="title"
                type="text"
                maxlength="255"
                v-model="form.title"
                class="w-full"
              />
              <InputError class="mt-2" :message="form.errors.title" />
            </div>
          </div>

          <div id="editor-toolbar" class="toolbar-lite border rounded-md px-2 py-1">
            <span class="ql-formats">
              <select class="ql-size">
                <option v-for="n in 23" :key="n" :value="(n + 7) + 'px'">{{ n + 7 }}</option>
              </select>
            </span>

            <span class="ql-formats">
              <button class="ql-bold"></button>
              <button class="ql-italic"></button>
              <button class="ql-underline"></button>
              <button class="ql-strike"></button>
            </span>

            <span class="ql-formats">
              <button class="ql-link"></button>
              <button class="ql-blockquote"></button>
              <button class="ql-code-block"></button>
            </span>

            <span class="ql-formats">
              <button class="ql-list" value="ordered"></button>
              <button class="ql-list" value="bullet"></button>
              <select class="ql-align"></select>
              <button class="ql-clean"></button>
            </span>
          </div>

          <div id="editor-text" ref="element" class="editor border rounded-md"></div>
        </div>

        <div class="lg:col-span-4 mt-6 grid justify-center">
          <div class="lg:sticky lg:top-20 space-y-4">
            <div>
              <InputLabel for="image" value="Capa / Foto" />
              <input
                id="image"
                ref="imageInput"
                type="file"
                :accept="ACCEPT"
                class="hidden"
                @change="updateimagePreview"
              />
              <div class="mt-1 text-sm text-red-600" v-if="imageError">{{ imageError }}</div>
              <div class="mt-2">
                <div
                  v-if="imagePreview"
                  class="w-72 aspect-square bg-cover bg-center rounded-lg border"
                  :style="{ backgroundImage: `url('${imagePreview}')` }"
                />
                <div
                  v-else
                  class="max-w-72 aspect-square rounded-lg border border-dashed grid place-items-center text-gray-500"
                >
                  <span class="text-sm">Selecione uma imagem…</span>
                </div>
              </div>
              <SecondaryButton class="w-72 mt-2 me-2" type="button" @click.prevent="selectNewimage">
                Selecionar foto
              </SecondaryButton>
            </div>
            <div v-if="articlesCategories.length > 0" class="w-full">
              <InputLabel :required="true" value="Categorias" />
              <ul class="p-4 category-tree mt-2 bg-white rounded-md border border-gray-200 divide-y divide-gray-100">
                <li
                  v-for="n in flatTree"
                  :key="n.id"
                  class="category-row py-2 px-3"
                  :style="{ paddingLeft: (n.depth * 16) + 'px' }"
                >
                  <label class="flex items-center gap-2">
                    <input
                      type="checkbox"
                      :checked="form.category_id === n.id"
                      @change="toggleCategory(n.id)"
                      class="cursor-pointer rounded border-gray-300 text-main focus:ring-secondy"
                    />
                    <p class="cursor-pointer text-gray-800 truncate">{{ n.name }}</p>
                  </label>
                </li>
              </ul>
              <InputError class="mt-2" :message="form.errors.category_id" />
            </div>
          </div>
        </div>

      </div>
    </form>
  </AppLayout>
</template>

<style scoped>
  #editor-text {
    height: 500px;
  }

  .toolbar-lite {
    display: flex;
    gap: 5px;
    align-items: center;
    margin: 0px 0 0px;
  }

  .toolbar-lite :deep(.ql-picker.ql-size) { width: 84px; border: solid 1px rgb(85, 85, 85); }
  .toolbar-lite :deep(.ql-picker.ql-size .ql-picker-label) { width: 100%; }

  .toolbar-lite :deep(.ql-picker.ql-size .ql-picker-options) {
    max-height: 160px;
    overflow-y: auto;
    width: 100%;
  }

  .toolbar-lite :deep(.ql-picker.ql-size .ql-picker-item) { white-space: nowrap; }

  .editor { min-height: 320px; }
  .editor :deep(.ql-container) { min-height: 320px; cursor: text; }
  .editor :deep(.ql-editor) { min-height: 320px; padding: 12px 14px; white-space: pre-wrap; }
  .editor :deep(.ql-blank::before) { left: 14px; right: 14px; }
</style>
