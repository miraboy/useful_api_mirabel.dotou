import { defineStore } from "pinia";
import { ref } from "vue";
import { api } from "../services/myApi";

export const useUrlShortenerStore = defineStore("urlShortener", () => {
    const links = ref([]);
    const isLoading = ref(false);
    const error = ref(null);

    // Create a short link
    async function createShortLink(originalUrl, customCode = null) {
        isLoading.value = true;
        error.value = null;

        try {
            const payload = { original_url: originalUrl };
            if (customCode) {
                payload.custom_code = customCode;
            }

            const result = await api.post("/shorten", payload);

            if (!result.success) {
                throw new Error(result.message || "Error creating short link");
            }

            // The response is an array with the new link
            if (Array.isArray(result.data) && result.data.length > 0) {
                links.value.unshift(result.data[0]);
                return result.data[0];
            }

            return null;
        } catch (err) {
            error.value = err.message || "Error creating short link";
            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    // Get all user's links
    async function fetchLinks() {
        isLoading.value = true;
        error.value = null;

        try {
            const result = await api.get("/links");

            if (!result.success) {
                throw new Error(result.message || "Error fetching links");
            }

            links.value = result.data;
            return result.data;
        } catch (err) {
            error.value = err.message || "Error fetching links";
            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    // Delete a link
    async function deleteLink(linkId) {
        isLoading.value = true;
        error.value = null;

        try {
            const result = await api.delete(`/links/${linkId}`);

            if (!result.success) {
                throw new Error(result.message || "Error deleting link");
            }

            // Remove from local state
            links.value = links.value.filter((link) => link.id !== linkId);
            return true;
        } catch (err) {
            error.value = err.message || "Error deleting link";
            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    // Get short URL
    function getShortUrl(code) {
        const baseUrl = import.meta.env.VITE_API_URL || "http://localhost:8000";
        return `${baseUrl}/api/s/${code}`;
    }

    // Reset store
    function $reset() {
        links.value = [];
        isLoading.value = false;
        error.value = null;
    }

    return {
        links,
        isLoading,
        error,
        createShortLink,
        fetchLinks,
        deleteLink,
        getShortUrl,
        $reset,
    };
});
