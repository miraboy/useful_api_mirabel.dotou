<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-800 mb-2 flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                        class="w-10 h-10 text-indigo-600">
                        <path fill-rule="evenodd"
                            d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z"
                            clip-rule="evenodd" />
                    </svg>
                    Time Tracker
                </h1>
                <p class="text-gray-600">Track your time and boost productivity</p>
            </div>

            <!-- Active Session Card -->
            <div v-if="hasActiveSession"
                class="bg-white rounded-2xl shadow-xl p-8 mb-8 border-l-4 border-green-500 animate-fade-in">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-4 h-4 bg-green-500 rounded-full animate-pulse"></div>
                        <h2 class="text-2xl font-bold text-gray-800">Active Session</h2>
                    </div>
                    <button @click="stopActiveSession" :disabled="loading"
                        class="px-6 py-3 bg-red-500 text-white rounded-xl hover:bg-red-600 transition-all duration-300 transform hover:scale-105 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed font-semibold flex items-center gap-2">
                        <span v-if="!loading" class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-5 h-5">
                                <path fill-rule="evenodd"
                                    d="M4.5 7.5a3 3 0 0 1 3-3h9a3 3 0 0 1 3 3v9a3 3 0 0 1-3 3h-9a3 3 0 0 1-3-3v-9Z"
                                    clip-rule="evenodd" />
                            </svg>
                            Stop Session</span>
                        <span v-else>Stopping...</span>
                    </button>
                </div>

                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6">
                    <p class="text-lg font-semibold text-gray-700 mb-2">{{ activeSession.task_name }}</p>
                    <p class="text-sm text-gray-600">Started: {{ formatDateTime(activeSession.start_time) }}</p>
                    <p class="text-2xl font-bold text-green-600 mt-4">{{ currentDuration }}</p>
                </div>
            </div>

            <!-- Start Session Card -->
            <div v-else class="bg-white rounded-2xl shadow-xl p-8 mb-8 animate-fade-in">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                        class="w-7 h-7 text-green-600">
                        <path fill-rule="evenodd"
                            d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z"
                            clip-rule="evenodd" />
                    </svg>
                    Start New Session
                </h2>

                <form @submit.prevent="startNewSession" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Task Name</label>
                        <input v-model="newTaskName" type="text" placeholder="What are you working on?"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-300"
                            required />
                    </div>

                    <button type="submit" :disabled="loading || !newTaskName.trim()"
                        class="w-full px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed font-bold text-lg flex items-center justify-center gap-2">
                        <span v-if="!loading" class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-6 h-6">
                                <path fill-rule="evenodd"
                                    d="M9.315 7.584C12.195 3.883 16.695 1.5 21.75 1.5a.75.75 0 0 1 .75.75c0 5.056-2.383 9.555-6.084 12.436A6.75 6.75 0 0 1 9.75 22.5a.75.75 0 0 1-.75-.75v-4.131A15.838 15.838 0 0 1 6.382 15H2.25a.75.75 0 0 1-.75-.75 6.75 6.75 0 0 1 7.815-6.666ZM15 6.75a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5Z"
                                    clip-rule="evenodd" />
                                <path
                                    d="M5.26 17.242a.75.75 0 1 0-.897-1.203 5.243 5.243 0 0 0-2.05 5.022.75.75 0 0 0 .625.627 5.243 5.243 0 0 0 5.022-2.051.75.75 0 1 0-1.202-.897 3.744 3.744 0 0 1-3.008 1.51c0-1.23.592-2.323 1.51-3.008Z" />
                            </svg>
                            Start Session</span>
                        <span v-else>Starting...</span>
                    </button>
                </form>

                <div v-if="error" class="mt-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg">
                    {{ error }}
                </div>
            </div>

            <!-- Analytics Section -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 animate-fade-in">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="w-7 h-7 text-blue-600">
                            <path fill-rule="evenodd"
                                d="M15.22 6.268a.75.75 0 0 1 .968-.431l5.942 2.28a.75.75 0 0 1 .431.97l-2.28 5.94a.75.75 0 1 1-1.4-.537l1.63-4.251-1.086.484a11.2 11.2 0 0 0-5.45 5.173.75.75 0 0 1-1.199.19L9 12.312l-6.22 6.22a.75.75 0 0 1-1.06-1.061l6.75-6.75a.75.75 0 0 1 1.06 0l3.606 3.606a12.695 12.695 0 0 1 5.68-4.974l1.086-.483-4.251-1.632a.75.75 0 0 1-.432-.97Z"
                                clip-rule="evenodd" />
                        </svg>
                        Analytics
                    </h2>
                    <select v-model="analyticsPeriod" @change="loadAnalytics"
                        class="px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 font-semibold">
                        <option value="day">Per Day</option>
                        <option value="month">Per Month</option>
                        <option value="year">Per Year</option>
                    </select>
                </div>

                <div v-if="analytics" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6">
                        <p class="text-sm font-semibold text-blue-700 mb-2">Total Sessions</p>
                        <p class="text-4xl font-bold text-blue-600">{{ analytics.total_sessions }}</p>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6">
                        <p class="text-sm font-semibold text-purple-700 mb-2">Total Duration</p>
                        <p class="text-4xl font-bold text-purple-600">{{ analytics.converted_total_duration.toFixed(2)
                        }}</p>
                        <p class="text-xs text-purple-600 mt-1">{{ analyticsPeriod }}s</p>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6">
                        <p class="text-sm font-semibold text-green-700 mb-2">Average Duration</p>
                        <p class="text-4xl font-bold text-green-600">{{ analytics.converted_average_duration.toFixed(2)
                        }}</p>
                        <p class="text-xs text-green-600 mt-1">{{ analyticsPeriod }}s</p>
                    </div>
                </div>

                <!-- Task Breakdown -->
                <div v-if="analytics && analytics.task_breakdown.length > 0" class="mt-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Task Breakdown</h3>
                    <div class="space-y-3">
                        <div v-for="task in analytics.task_breakdown" :key="task.task_name"
                            class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4 hover:shadow-md transition-shadow duration-300">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ task.task_name }}</p>
                                    <p class="text-sm text-gray-600">{{ task.total_sessions }} sessions</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-indigo-600">{{ task.converted_duration.toFixed(2) }} {{
                                        analyticsPeriod }}s</p>
                                    <p class="text-sm text-gray-500">{{ formatDuration(task.total_duration_seconds) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sessions List -->
            <div class="bg-white rounded-2xl shadow-xl p-8 animate-fade-in">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-7 h-7 text-purple-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                        </svg>
                        Session History
                    </h2>
                    <button @click="refreshSessions" :disabled="loading"
                        class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-xl hover:bg-indigo-200 transition-colors duration-300 font-semibold disabled:opacity-50 flex items-center gap-2">
                        <span v-if="!loading" class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-5 h-5">
                                <path fill-rule="evenodd"
                                    d="M4.755 10.059a7.5 7.5 0 0 1 12.548-3.364l1.903 1.903h-3.183a.75.75 0 1 0 0 1.5h4.992a.75.75 0 0 0 .75-.75V4.356a.75.75 0 0 0-1.5 0v3.18l-1.9-1.9A9 9 0 0 0 3.306 9.67a.75.75 0 1 0 1.45.388Zm15.408 3.352a.75.75 0 0 0-.919.53 7.5 7.5 0 0 1-12.548 3.364l-1.902-1.903h3.183a.75.75 0 0 0 0-1.5H2.984a.75.75 0 0 0-.75.75v4.992a.75.75 0 0 0 1.5 0v-3.18l1.9 1.9a9 9 0 0 0 15.059-4.035.75.75 0 0 0-.53-.918Z"
                                    clip-rule="evenodd" />
                            </svg>
                            Refresh</span>
                        <span v-else>Loading...</span>
                    </button>
                </div>

                <div v-if="sessions.length === 0" class="text-center py-12">
                    <div class="flex justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="w-16 h-16 text-gray-400">
                            <path fill-rule="evenodd"
                                d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <p class="text-gray-500 text-lg">No sessions yet. Start tracking your time!</p>
                </div>

                <div v-else class="space-y-4">
                    <div v-for="session in sessions" :key="session.id"
                        class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 hover:shadow-lg transition-all duration-300 border-l-4"
                        :class="session.end_time ? 'border-gray-300' : 'border-green-500'">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h3 class="text-xl font-bold text-gray-800">{{ session.task_name }}</h3>
                                    <span v-if="!session.end_time"
                                        class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                        ACTIVE
                                    </span>
                                </div>

                                <div class="space-y-1 text-sm text-gray-600">
                                    <p class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="w-4 h-4 text-green-600">
                                            <path fill-rule="evenodd"
                                                d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Started: {{ formatDateTime(session.start_time) }}
                                    </p>
                                    <p v-if="session.end_time" class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="w-4 h-4 text-red-600">
                                            <path fill-rule="evenodd"
                                                d="M4.5 7.5a3 3 0 0 1 3-3h9a3 3 0 0 1 3 3v9a3 3 0 0 1-3 3h-9a3 3 0 0 1-3-3v-9Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Ended: {{ formatDateTime(session.end_time) }}
                                    </p>
                                    <p v-if="session.duration"
                                        class="font-semibold text-indigo-600 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="w-4 h-4">
                                            <path fill-rule="evenodd"
                                                d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Duration: {{ formatDuration(session.duration) }}
                                    </p>
                                </div>
                            </div>

                            <button v-if="session.end_time" @click="deleteSessionById(session.id)" :disabled="loading"
                                class="ml-4 px-4 py-2 bg-red-100 text-red-700 rounded-xl hover:bg-red-200 transition-colors duration-300 font-semibold disabled:opacity-50 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="w-5 h-5">
                                    <path fill-rule="evenodd"
                                        d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z"
                                        clip-rule="evenodd" />
                                </svg>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useTimeTrackerStore } from '../stores/timeTracker'
import { useAuthStore } from '../stores/auth'
import { api } from '@/services/myApi'

const timeTrackerStore = useTimeTrackerStore()
const authStore = useAuthStore()

const newTaskName = ref('')
const analyticsPeriod = ref('day')
const currentDuration = ref('0s')
let durationInterval = null

const sessions = computed(() => timeTrackerStore.sessions)
const activeSession = computed(() => timeTrackerStore.activeSession)
const hasActiveSession = computed(() => timeTrackerStore.hasActiveSession)
const analytics = computed(() => timeTrackerStore.analytics)
const loading = computed(() => timeTrackerStore.loading)
const error = computed(() => timeTrackerStore.error)
const formatDuration = (seconds) => timeTrackerStore.formatDuration(seconds)

const formatDateTime = (dateStr) => {
    const date = new Date(dateStr)
    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const updateCurrentDuration = () => {
    if (activeSession.value) {
        const start = new Date(activeSession.value.start_time)
        const now = new Date()
        const seconds = Math.floor((now - start) / 1000)
        currentDuration.value = formatDuration(seconds)
    }
}

const startNewSession = async () => {
    try {
        await timeTrackerStore.startSession(newTaskName.value)
        newTaskName.value = ''
        await loadAnalytics()

        // Start duration counter
        durationInterval = setInterval(updateCurrentDuration, 1000)
    } catch (error) {
        console.error('Error starting session:', error)
    }
}

const stopActiveSession = async () => {
    if (!activeSession.value) return

    try {
        await timeTrackerStore.stopSession(activeSession.value.id)
        await loadAnalytics()

        // Clear duration counter
        if (durationInterval) {
            clearInterval(durationInterval)
            durationInterval = null
        }
    } catch (error) {
        console.error('Error stopping session:', error)
    }
}

const deleteSessionById = async (id) => {
    if (!confirm('Are you sure you want to delete this session?')) return

    try {
        await timeTrackerStore.deleteSession(id)
        await loadAnalytics()
    } catch (error) {
        console.error('Error deleting session:', error)
    }
}

const refreshSessions = async () => {
    try {
        await timeTrackerStore.fetchSessions()
    } catch (error) {
        console.error('Error refreshing sessions:', error)
    }
}

const loadAnalytics = async () => {
    try {
        await timeTrackerStore.fetchAnalytics(analyticsPeriod.value)
    } catch (error) {
        console.error('Error loading analytics:', error)
    }
}

onMounted(async () => {
    // Ensure token is set
    if (authStore.token) {
        api.setToken(authStore.token)
    }

    // Load data
    await refreshSessions()
    await loadAnalytics()

    // Start duration counter if there's an active session
    if (hasActiveSession.value) {
        updateCurrentDuration()
        durationInterval = setInterval(updateCurrentDuration, 1000)
    }
})

onUnmounted(() => {
    if (durationInterval) {
        clearInterval(durationInterval)
    }
})
</script>
