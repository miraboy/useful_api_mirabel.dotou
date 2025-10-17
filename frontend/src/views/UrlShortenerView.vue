<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Header avec animation -->
            <div class="text-center mb-6 animate-fade-in">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl mb-4 shadow-lg transform hover:scale-110 transition-transform">
                    <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                </div>
                <h1
                    class="text-4xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 mb-3">
                    URL Shortener
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Transform your long URLs into short, shareable links. Track clicks and manage everything in one
                    place.
                </p>
                <div
                    class="mt-4 inline-flex items-center text-xs text-gray-500 bg-white px-3 py-1 rounded-full shadow-sm">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                    Auto-refresh every 10s
                </div>
            </div>

            <!-- Messages d'alerte -->
            <div v-if="error"
                class="mb-4 bg-red-500 text-white rounded-xl p-4 shadow-lg animate-slide-in flex items-center">
                <svg class="h-6 w-6 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
                <p class="font-medium">{{ error }}</p>
            </div>

            <div v-if="showSuccess"
                class="mb-4 bg-green-500 text-white rounded-xl p-4 shadow-lg animate-slide-in flex items-center">
                <svg class="h-6 w-6 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <p class="font-medium">{{ successMessage }}</p>
            </div>

            <!-- Formulaire de création avec design moderne -->
            <div
                class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-gray-100 hover:shadow-2xl transition-shadow">
                <div class="flex items-center mb-6">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Create Short Link</h2>
                </div>

                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <div class="space-y-2">
                        <label for="original_url" class="block text-sm font-semibold text-gray-700">
                            Original URL <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                </svg>
                            </div>
                            <input id="original_url" v-model="form.originalUrl" type="url" required
                                placeholder="https://example.com/your-very-long-url-here"
                                class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                :disabled="isLoading" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="custom_code" class="block text-sm font-semibold text-gray-700">
                            Custom Code <span class="text-xs font-normal text-gray-500">(optional)</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                </svg>
                            </div>
                            <input id="custom_code" v-model="form.customCode" type="text" placeholder="my-custom-link"
                                maxlength="10"
                                class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all"
                                :disabled="isLoading" />
                        </div>
                        <p class="text-xs text-gray-500 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            Leave empty for a random 6-character code
                        </p>
                    </div>

                    <button type="submit" :disabled="isLoading"
                        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-4 px-6 rounded-xl transition-all transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none shadow-lg">
                        <span v-if="!isLoading" class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Shorten URL
                        </span>
                        <span v-else class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                                    fill="none"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Creating...
                        </span>
                    </button>
                </form>
            </div>

            <!-- Liste des liens avec cards modernes -->
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">Your Links</h2>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div v-if="links.length > 0"
                            class="text-sm font-semibold text-gray-500 bg-gray-100 px-4 py-2 rounded-full">
                            {{ links.length }} {{ links.length === 1 ? 'link' : 'links' }}
                        </div>
                        <button @click="refreshLinks()" :disabled="isRefreshing"
                            class="flex items-center px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 font-semibold rounded-lg transition-colors disabled:opacity-50"
                            title="Refresh links">
                            <svg :class="{ 'animate-spin': isRefreshing }" class="w-5 h-5 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            {{ isRefreshing ? 'Refreshing...' : 'Refresh' }}
                        </button>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="isLoading && links.length === 0" class="text-center py-16">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-500 rounded-2xl mb-4 animate-pulse">
                        <svg class="w-8 h-8 text-white animate-spin" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                                fill="none"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                    <p class="text-gray-600 font-medium">Loading your links...</p>
                </div>

                <!-- Empty State -->
                <div v-else-if="links.length === 0" class="text-center py-16">
                    <div
                        class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No links yet</h3>
                    <p class="text-gray-500 mb-6">Create your first short link to get started!</p>
                    <div class="inline-flex items-center text-sm text-blue-600 font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                        Use the form above
                    </div>
                </div>

                <!-- Links Grid -->
                <div v-else class="space-y-4">
                    <div v-for="link in links" :key="link.id"
                        class="group bg-gradient-to-br from-gray-50 to-white border-2 border-gray-100 rounded-xl p-6 hover:border-blue-300 hover:shadow-lg transition-all">
                        <!-- Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center mb-2">
                                    <div
                                        class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                    </div>
                                    <a :href="getShortUrl(link.code)" target="_blank"
                                        class="text-blue-600 hover:text-blue-800 font-mono font-bold text-lg flex items-center group-hover:underline">
                                        {{ link.code }}
                                        <svg class="ml-2 h-4 w-4 opacity-0 group-hover:opacity-100 transition-opacity"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                </div>
                                <p class="text-sm text-gray-600 truncate" :title="link.original_url">
                                    {{ link.original_url }}
                                </p>
                            </div>
                            <button @click="handleDelete(link.id)"
                                class="ml-4 p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                title="Delete link">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>

                        <!-- Stats & Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center text-sm">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-2">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </div>
                                    <span class="font-bold text-gray-900">{{ link.clicks }}</span>
                                    <span class="text-gray-500 ml-1">{{ link.clicks === 1 ? 'click' : 'clicks' }}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ formatDate(link.created_at) }}
                                </div>
                            </div>
                            <button @click="copyToClipboard(getShortUrl(link.code))"
                                class="flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-semibold rounded-lg transition-all transform hover:scale-105 shadow-md">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                Copy
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useUrlShortenerStore } from '../stores/urlShortener'
import { useAuthStore } from '../stores/auth'
import { api } from '../services/myApi'

const urlShortenerStore = useUrlShortenerStore()
const authStore = useAuthStore()

const form = ref({
    originalUrl: '',
    customCode: ''
})

const showSuccess = ref(false)
const successMessage = ref('')
const isRefreshing = ref(false)

const links = computed(() => urlShortenerStore.links)
const isLoading = computed(() => urlShortenerStore.isLoading)
const error = computed(() => urlShortenerStore.error)

let refreshInterval = null

onMounted(async () => {
    try {
        // Ensure token is set before making API calls
        if (authStore.token) {
            console.log('Setting token for URL Shortener:', authStore.token?.substring(0, 20) + '...')
            api.setToken(authStore.token)
        } else {
            console.error('No token found in authStore!')
        }

        // Small delay to ensure auth is fully initialized
        await new Promise(resolve => setTimeout(resolve, 100))

        await urlShortenerStore.fetchLinks()

        // Setup auto-refresh every 10 seconds
        refreshInterval = setInterval(async () => {
            await refreshLinks(true)
        }, 10000)
    } catch (err) {
        console.error('Error loading links:', err)
    }
})

onUnmounted(() => {

    if (refreshInterval) {
        clearInterval(refreshInterval)
    }
})

async function refreshLinks(silent = false) {
    if (!silent) {
        isRefreshing.value = true
    }

    try {
        await urlShortenerStore.fetchLinks()
        if (!silent) {
            successMessage.value = 'Links refreshed!'
            showSuccess.value = true
            setTimeout(() => {
                showSuccess.value = false
            }, 1)
        }
    } catch (err) {
        console.error('Error refreshing links:', err)
    } finally {
        if (!silent) {
            isRefreshing.value = false
        }
    }
}

async function handleSubmit() {
    try {
        const customCode = form.value.customCode.trim() || null
        await urlShortenerStore.createShortLink(form.value.originalUrl, customCode)

        // Show success message
        successMessage.value = 'Short link created successfully!'
        showSuccess.value = true
        setTimeout(() => {
            showSuccess.value = false
        }, 3000)

        // Reset form
        form.value.originalUrl = ''
        form.value.customCode = ''
    } catch (err) {
        console.error('Error creating link:', err)
    }
}

async function handleDelete(linkId) {
    if (!confirm('Are you sure you want to delete this link?')) {
        return
    }

    try {
        await urlShortenerStore.deleteLink(linkId)

        successMessage.value = 'Link deleted successfully!'
        showSuccess.value = true
        setTimeout(() => {
            showSuccess.value = false
        }, 3000)
    } catch (err) {
        console.error('Error deleting link:', err)
    }
}

function getShortUrl(code) {
    return urlShortenerStore.getShortUrl(code)
}

function formatDate(dateString) {
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text)
        successMessage.value = 'Link copied to clipboard!'
        showSuccess.value = true
        setTimeout(() => {
            showSuccess.value = false
        }, 2000)
    } catch (err) {
        console.error('Error copying to clipboard:', err)
    }
}
</script>
