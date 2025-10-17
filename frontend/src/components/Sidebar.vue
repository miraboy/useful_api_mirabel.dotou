<template>
    <aside class="bg-gray-800 text-white w-64 min-h-screen p-4">

        <div class="mb-8">
            <h2 class="text-2xl font-bold">Dashboard</h2>
        </div>

        <!-- Navigation -->
        <nav class="space-y-2">

            <router-link to="/dashboard"
                class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors hover:bg-gray-700"
                :class="{ 'bg-gray-700': $route.path === '/dashboard' }">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="font-medium">Dashboard</span>
            </router-link>

            <!-- Active Modules -->
            <div v-if="activeModules.length > 0" class="pt-4">
                <p class="text-gray-400 text-xs uppercase tracking-wider mb-2 px-4">Modules</p>

                <router-link v-for="module in activeModules" :key="module.id"
                    :to="`/dashboard/${getModuleRoute(module.name)}`"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors hover:bg-gray-700"
                    :class="{ 'bg-gray-700': isModuleActive(module.name) }">
                    <component :is="getModuleIcon(module.name)" class="w-5 h-5" />
                    <span class="font-medium">{{ module.name }}</span>
                </router-link>
            </div>

            <!-- Message if no active module -->
            <div v-else class="pt-4 px-4">
                <p class="text-gray-400 text-sm">No active module</p>
            </div>
        </nav>
    </aside>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useModulesStore } from '@/stores/modules'

const route = useRoute()
const modulesStore = useModulesStore()

const activeModules = computed(() => modulesStore.activeModules)

const getModuleRoute = (moduleName) => {
    const routes = {
        'URL Shortener': 'url-shortener',
        'Wallet': 'wallet',
        'Marketplace': 'marketplace',
        'Time Tracker': 'time-tracker',
        'Investment Tracker': 'investment-tracker',
    }
    return routes[moduleName] || moduleName.toLowerCase().replace(/\s+/g, '-')
}

// Vérifier si une route de module est active
const isModuleActive = (moduleName) => {
    const moduleRoute = getModuleRoute(moduleName)
    return route.path.includes(moduleRoute)
}

// Icônes pour chaque module
const getModuleIcon = (moduleName) => {
    const icons = {
        'URL Shortener': 'IconLink',
        'Wallet': 'IconWallet',
        'Marketplace': 'IconShop',
        'Time Tracker': 'IconClock',
        'Investment Tracker': 'IconChart',
    }
    return icons[moduleName] || 'IconDefault'
}
</script>

<!-- Composants d'icônes inline -->
<script>
const IconLink = {
    template: `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
    </svg>
  `
}

const IconWallet = {
    template: `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
    </svg>
  `
}

const IconShop = {
    template: `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
    </svg>
  `
}

const IconClock = {
    template: `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
  `
}

const IconChart = {
    template: `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
    </svg>
  `
}

const IconDefault = {
    template: `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
  `
}
</script>
