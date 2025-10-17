import { defineStore } from "pinia";
import { api } from "@/services/myApi";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        token: null,
        user: null,
        loading: false,
        error: null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        currentUser: (state) => state.user,
    },

    actions: {
        async login(credentials) {
            this.loading = true;
            this.error = null;

            const res = await api.post("/login", credentials);
            this.loading = false;

            if (res.success) {
                this.token = res.data.token;
                api.setToken(res.data.token);
                await this.fetchUser();
                return true;
            } else {
                this.error = res.message;
                return false;
            }
        },

        async register(payload) {
            this.loading = true;
            this.error = null;

            const res = await api.post("/register", payload);
            this.loading = false;

            if (res.success) {
                return await this.login({
                    email: payload.email,
                    password: payload.password,
                });
            } else {
                this.error = res.message;
                return false;
            }
        },

        async fetchUser() {
            const res = await api.get("/user");
            if (res.success) {
                this.user = res.data;
            }
        },

        logout() {
            this.token = null;
            this.user = null;
            this.error = null;
            api.setToken(null);
        },

        async initializeAuth() {
            if (this.token) {
                api.setToken(this.token);
                if (!this.user) {
                    await this.fetchUser();
                }
            }
        },
    },

    persist: {
        key: "auth",
        storage: localStorage,
        paths: ["token", "user"],
    },
});
