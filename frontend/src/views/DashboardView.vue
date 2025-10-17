<template>
    <div class="min-h-screen bg-gray-100 flex">
        <!-- Sidebar -->
        <Sidebar />

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Header -->
            <nav class="bg-white shadow-sm">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <h1 class="text-xl font-bold text-gray-800">{{ pageTitle }}</h1>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600">
                                {{ authStore.user?.name || 'Utilisateur' }}
                            </span>
                            <button @click="handleLogout"
                                class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                Déconnexion
                            </button>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content Area - Router View for nested routes -->
            <main>
                <router-view />
            </main>
        </div>
    </div>
</template>

<script setup>
import { onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useModulesStore } from '@/stores/modules'
import Sidebar from '@/components/Sidebar.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const modulesStore = useModulesStore()

// Titre de page dynamique basé sur la route
const pageTitle = computed(() => {
    const routeName = route.name
    if (routeName === 'DashboardHome') return 'Dashboard'
    return routeName || 'Dashboard'
})

// Initialiser l'auth et charger les modules au montage
onMounted(async () => {
    await authStore.initializeAuth().catch((error) => {
        console.error("Erreur lors de l'initialisation de l'auth:", error);
    });

    // Charger les modules
    await modulesStore.fetchModules()
})

const handleLogout = () => {
    authStore.logout()
    router.push('/login')
}
</script>
