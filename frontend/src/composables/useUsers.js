import { ref } from "vue";
import { api } from "@/services/myApi";

export function useUsers() {
    const users = ref([]);
    const user = ref(null);
    const loading = ref(false);
    const error = ref(null);

    // Lire tous les utilisateurs
    async function fetchUsers() {
        loading.value = true;
        const res = await api.get("/users");
        loading.value = false;

        if (res.success) users.value = res.data;
        else error.value = res.message;
    }

    // Lire un utilisateur spécifique
    async function fetchUser(id) {
        loading.value = true;
        const res = await api.get(`/users/${id}`);
        loading.value = false;

        if (res.success) user.value = res.data;
        else error.value = res.message;
    }

    // Créer un nouvel utilisateur
    async function createUser(payload) {
        loading.value = true;
        const res = await api.post("/users", { data: payload });
        loading.value = false;

        if (res.success) {
            users.value.push(res.data);
            return res.data;
        } else {
            error.value = res.message;
            return null;
        }
    }

    // Mettre à jour un utilisateur
    async function updateUser(id, payload) {
        loading.value = true;
        const res = await api.put(`/users/${id}`, { data: payload });
        loading.value = false;

        if (res.success) {
            const index = users.value.findIndex((u) => u.id === id);
            if (index !== -1) users.value[index] = res.data;
            return res.data;
        } else {
            error.value = res.message;
            return null;
        }
    }

    // Supprimer un utilisateur
    async function deleteUser(id) {
        loading.value = true;
        const res = await api.delete(`/users/${id}`);
        loading.value = false;

        if (res.success) {
            users.value = users.value.filter((u) => u.id !== id);
            return true;
        } else {
            error.value = res.message;
            return false;
        }
    }

    return {
        users,
        user,
        loading,
        error,
        fetchUsers,
        fetchUser,
        createUser,
        updateUser,
        deleteUser,
    };
}
