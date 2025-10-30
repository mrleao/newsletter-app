<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import ApplicationMark from '@/Components/ApplicationMark.vue'
import Banner from '@/Components/Banner.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NavLink from '@/Components/NavLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'

defineProps({ title: String })

const showingNavigationDropdown = ref(false)

const page = usePage()
const isLoggedIn = computed(() => !!page.props.auth?.user)
const hasTeamFeatures = computed(() => !!page.props.jetstream?.hasTeamFeatures && isLoggedIn.value)
const managesProfilePhotos = computed(() => !!page.props.jetstream?.managesProfilePhotos && isLoggedIn.value)

const switchToTeam = (team) => {
  router.put(route('current-team.update'), { team_id: team.id }, { preserveState: false })
}

const logout = () => {
  router.post(route('logout'))
}
</script>

<template>
  <div>
    <Head :title="title" />

    <Banner />

    <div class="min-h-screen bg-gray-100">
      <nav class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <div class="flex">
              <div class="shrink-0 flex items-center">
                <Link :href="route('site.articles.page')">
                  <p class="text-main font-medium hover:font-bold">NovaNews</p>
                </Link>
              </div>

              <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                <NavLink :href="route('site.articles.page')" :active="route().current('site.articles.page')">
                  Noticias
                </NavLink>
              </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
              <div class="ms-3 relative" v-if="hasTeamFeatures">
                <Dropdown align="right" width="60">
                  <template #trigger>
                    <span class="inline-flex rounded-md">
                      <button
                        type="button"
                        class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition ease-in-out duration-150 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50"
                      >
                        {{ $page.props.auth.user.current_team.name }}
                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                        </svg>
                      </button>
                    </span>
                  </template>
                </Dropdown>
              </div>

              <div class="ms-3 relative" v-if="isLoggedIn">
                <Dropdown align="right" width="48">
                  <template #trigger>
                    <button
                      v-if="managesProfilePhotos"
                      class="flex rounded-full border-2 border-transparent focus:outline-none focus:border-gray-300 transition"
                    >
                      <img class="size-8 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                    </button>

                    <span v-else class="inline-flex rounded-md">
                      <button
                        type="button"
                        class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition ease-in-out duration-150 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50"
                      >
                        {{ $page.props.auth.user.name }}
                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                      </button>
                    </span>
                  </template>

                  <template #content>
                    <div class="block px-4 py-2 text-xs text-gray-400">
                      Manage Account
                    </div>
                    <DropdownLink :href="route('articles.index')">Gerenciar Noticias</DropdownLink>
                    <DropdownLink :href="route('categories.index')">Gerenciar Categorias</DropdownLink>

                    <DropdownLink :href="route('profile.show')">Profile</DropdownLink>
                    <DropdownLink v-if="$page.props.jetstream?.hasApiFeatures" :href="route('api-tokens.index')">API Tokens</DropdownLink>

                    <div class="border-t border-gray-200" />

                    <form @submit.prevent="logout">
                      <DropdownLink as="button">Log Out</DropdownLink>
                    </form>
                  </template>
                </Dropdown>
              </div>

              <div class="ms-3 flex items-center gap-3" v-else>
                <Link :href="route('login')" class="rounded-md border border-gray-200 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Entrar</Link>
                <Link :href="route('register')" class="rounded-md bg-main px-3 py-1.5 text-sm font-semibold text-white hover:opacity-90">Criar conta</Link>
              </div>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
              <button
                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                @click="showingNavigationDropdown = !showingNavigationDropdown"
              >
                <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                  <path
                    :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                  />
                  <path
                    :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
          <div class="space-y-1 pt-2 pb-3">
            <ResponsiveNavLink v-if="isLoggedIn" :href="route('dashboard')" :active="route().current('dashboard')">
              Dashboard
            </ResponsiveNavLink>
            <ResponsiveNavLink href="#contact" :active="false">
              Contato
            </ResponsiveNavLink>
          </div>

          <div class="border-t border-gray-200 pb-1 pt-4">
            <div v-if="isLoggedIn" class="flex items-center px-4">
              <div v-if="managesProfilePhotos" class="me-3 shrink-0">
                <img class="size-10 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
              </div>

              <div>
                <div class="text-base font-medium text-gray-800">
                  {{ $page.props.auth.user.name }}
                </div>
                <div class="text-sm font-medium text-gray-500">
                  {{ $page.props.auth.user.email }}
                </div>
              </div>
            </div>

            <div v-if="isLoggedIn" class="mt-3 space-y-1">
              <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">Profile</ResponsiveNavLink>
              <ResponsiveNavLink v-if="$page.props.jetstream?.hasApiFeatures" :href="route('api-tokens.index')" :active="route().current('api-tokens.index')">API Tokens</ResponsiveNavLink>
              <form method="POST" @submit.prevent="logout">
                <ResponsiveNavLink as="button">Log Out</ResponsiveNavLink>
              </form>
            </div>

            <div v-else class="mt-3 space-y-1 px-4">
              <ResponsiveNavLink :href="route('login')">Entrar</ResponsiveNavLink>
              <ResponsiveNavLink :href="route('register')">Criar conta</ResponsiveNavLink>
            </div>
          </div>
        </div>
      </nav>

      <header v-if="$slots.header" class="bg-white shadow">
        <div class="mx-auto mb-12 max-w-7xl py-6 px-4 sm:px-6 lg:px-8">
          <slot name="header" />
        </div>
      </header>

      <main class="flex-1 overflow-x-hidden overflow-y-auto">
        <div class="mx-auto">
          <div class="py-2">
            <div :class="width" class="mx-auto mt-10 max-w-7xl p-4 sm:px-6 lg:px-8">
              <div :class="classes" class="p-5 sm:rounded-xl">
                <slot />
              </div>
            </div>
          </div>

          <footer id="contact" class="relative isolate mt-16 text-white">
            <div class="absolute inset-0 -z-10 bg-[#0b0b10]"></div>
            <div class="absolute inset-0 -z-10 opacity-90 bg-[radial-gradient(120%_100%_at_100%_0%,#183b75_0%,transparent_55%),radial-gradient(120%_100%_at_0%_0%,#2a1d57_0%,transparent_55%)]"></div>

            <div class="mx-auto max-w-6xl px-6 py-16">
              <div class="rounded-3xl border border-white/10 bg-gradient-to-br from-slate-800/50 to-slate-900/30 p-8 backdrop-blur-sm shadow-[0_10px_40px_rgba(0,0,0,0.45)] md:p-12">
                <h2 class="text-3xl font-extrabold md:text-4xl">Contato</h2>
                <p class="mt-3 text-slate-300">Entre em contato com a gente 🥰</p>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                  <a href="mailto:marcosleaosistemas@gmail.com" class="inline-flex items-center rounded-full bg-indigo-400 px-5 py-3 font-semibold text-slate-900 ring-1 ring-indigo-300/40 transition hover:bg-indigo-300 shadow-[0_8px_30px_rgba(99,102,241,0.6)]">
                    marcosleaosistemas@gmail.com
                  </a>

                  <a href="https://github.com/mrleao" target="_blank" class="inline-flex items-center rounded-full border border-white/15 px-5 py-3 font-medium text-slate-200 transition hover:border-white/35 hover:bg-white/5">
                    GitHub
                  </a>

                  <a href="https://www.linkedin.com/in/marcosleaosistemas" target="_blank" class="inline-flex items-center rounded-full border border-white/15 px-5 py-3 font-medium text-slate-200 transition hover:border-white/35 hover:bg-white/5">
                    LinkedIn
                  </a>
                </div>
              </div>

              <div class="mt-10 flex flex-col items-center gap-3">
                <div class="p-5 text-center">
                  <p>
                    Desenvolvido por
                    <a class="text-main mx-1 font-medium hover:font-bold" target="_blank" href="https://mrleao.github.io/">Marcos Leão</a>
                  </p>
                  <p class="bg-main mx-auto mt-2 w-40 p-1 text-white">
                    O café nos move ☕
                  </p>
                </div>
              </div>
            </div>
          </footer>
        </div>
      </main>
    </div>
  </div>
</template>
