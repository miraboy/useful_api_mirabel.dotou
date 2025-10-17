import { defineStore } from "pinia";
import { api } from "@/services/myApi";

export const useModulesStore = defineStore("modules", {
    state: () => ({
        modules: [],
        loading: false,
        error: null,
    }),

    getters: {
        // Récupérer les modules actifs
        activeModules: (state) => state.modules.filter((m) => m.active),

        // Vérifier si un module est actif
        isModuleActive: (state) => (moduleId) => {
            const module = state.modules.find((m) => m.id === moduleId);
            return module ? module.active : false;
        },

        // Récupérer un module par son nom
        getModuleByName: (state) => (name) => {
            return state.modules.find((m) => m.name === name);
        },
    },

    actions: {
        // Récupérer tous les modules de l'utilisateur
        async fetchModules() {
            this.loading = true;
            this.error = null;

            const res = await api.get("/modules");
            this.loading = false;

            if (res.success) {
                this.modules = res.data;
            } else {
                this.error = res.message;
            }
        },

        // Activer un module
        async activateModule(moduleId) {
            this.error = null;

            const res = await api.post(`/modules/${moduleId}/activate`);

            if (res.success) {
                
                const module = this.modules.find((m) => m.id === moduleId);
                if (module) {
                    module.active = true;
                }
                return true;
            } else {
                this.error = res.message;
                return false;
            }
        },

        // Désactiver un module
        async deactivateModule(moduleId) {
            
            this.error = null;

            const res = await api.post(`/modules/${moduleId}/deactivate`);
            
            if (res.success) {
                const module = this.modules.find((m) => m.id === moduleId);
                if (module) {
                    module.active = false;
                }
                return true;
            } else {
                this.error = res.message;
                return false;
            }
        },
    },
});
