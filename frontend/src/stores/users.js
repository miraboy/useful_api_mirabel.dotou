import { defineStore } from "pinia";
import { api } from "@/services/myApi";

export const useUsersStore = defineStore("users", {
    state: () => ({
        users: [],
        loading: false,
        error: null,
    }),

    getters: {
        usersForSelect: (state) =>
            state.users.map((user) => ({
                value: user.id,
                label: `${user.name} (${user.email})`,
                email: user.email,
            })),
    },

    actions: {
        // Récupérer la liste des utilisateurs
        async fetchUsers() {
            this.loading = true;
            this.error = null;

            const res = await api.get("/users");
            this.loading = false;

            if (res.success) {
                this.users = res.data;
            } else {
                this.error =
                    res.message ||
                    "Erreur lors de la récupération des utilisateurs";
            }
        },

        // Trouver un utilisateur par son ID
        getUserById(userId) {
            return this.users.find((user) => user.id === userId);
        },
    },
});
