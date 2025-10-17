<template>
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    {{ module.name }}
                </h3>
                <p class="text-sm text-gray-600 mb-4">
                    {{ module.description }}
                </p>
            </div>

            <!-- Toggle Switch -->
            <div class="ml-4">
                <button @click="toggleModule" :disabled="loading"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    :class="isActive ? 'bg-blue-600' : 'bg-gray-200'">
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                        :class="isActive ? 'translate-x-6' : 'translate-x-1'" />
                </button>
            </div>
        </div>

        <!-- Status badge -->
        <div class="mt-4 flex items-center justify-between">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                :class="isActive ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                {{ isActive ? 'Activé' : 'Désactivé' }}
            </span>

            <span v-if="loading" class="text-xs text-gray-500">
                Chargement...
            </span>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useModulesStore } from '@/stores/modules'

const props = defineProps({
    module: {
        type: Object,
        required: true
    }
})

const modulesStore = useModulesStore()

const isActive = computed(() => props.module.active)
const loading = computed(() => modulesStore.loading)

const toggleModule = async () => {
    if (isActive.value) {
        await modulesStore.deactivateModule(props.module.id)
    } else {
        await modulesStore.activateModule(props.module.id)
    }
}
</script>
