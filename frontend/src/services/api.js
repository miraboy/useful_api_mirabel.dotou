import axios from "axios";

export function createApiClient({
    baseURL = "",
    token = null,
    headers = {},
} = {}) {
    const apiClient = axios.create({
        baseURL,
        timeout: 10000, // 10 secondes
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            ...headers,
        },
    });

    // Stocker l'ID de l'intercepteur
    let requestInterceptor = null;

    // Fonction pour définir le token
    const setAuthToken = (newToken) => {
        // Supprimer l'ancien intercepteur s'il existe
        if (requestInterceptor !== null) {
            apiClient.interceptors.request.eject(requestInterceptor);
        }

        // Ajouter un nouvel intercepteur si le token existe
        if (newToken) {
            requestInterceptor = apiClient.interceptors.request.use(
                (config) => {
                    config.headers.Authorization = `Bearer ${newToken}`;
                    return config;
                }
            );
        }
    };

    // Initialiser avec le token si fourni
    if (token) {
        setAuthToken(token);
    }

    const handleError = (error) => {
        const status = error.response?.status || 500;

        // Extract error message
        let message = error.message || "Unknown error occurred";

        // For Laravel validation errors (422)
        if (status === 422 && error.response?.data?.errors) {
            const errors = error.response.data.errors;
            const firstError = Object.values(errors)[0];
            message = Array.isArray(firstError) ? firstError[0] : firstError;
        } else if (error.response?.data?.message) {
            message = error.response.data.message;
        } else if (error.response?.data?.error) {
            message = error.response.data.error;
        }

        if (error.code === "ECONNABORTED") {
            return {
                success: false,
                status: 408,
                message: "Request timeout",
            };
        }

        return {
            success: false,
            status,
            message,
            data: error.response?.data || null,
        };
    };

    async function request(method, url, options = {}) {
        try {
            const config = {
                method,
                url,
                headers: {
                    "Content-Type":
                        options.data instanceof FormData
                            ? "multipart/form-data"
                            : "application/json",
                    ...(options.headers || {}),
                },
            };

            // Ajouter les données selon la méthode
            if (method === "get" || method === "delete") {
                if (options.params) config.params = options.params;
            } else {
                if (options.data) config.data = options.data;
            }

            const response = await apiClient(config);
            return {
                success: true,
                status: response.status,
                data: response.data,
            };
        } catch (error) {
            return handleError(error);
        }
    }

    return {
        get: (url, options = {}) => request("get", url, options),
        post: (url, data = null, options = {}) =>
            request("post", url, { ...options, data }),
        put: (url, data = null, options = {}) =>
            request("put", url, { ...options, data }),
        patch: (url, data = null, options = {}) =>
            request("patch", url, { ...options, data }),
        delete: (url, options = {}) => request("delete", url, options),
        setToken: setAuthToken,
        setBaseURL: (newBaseURL) => {
            apiClient.defaults.baseURL = newBaseURL;
        },
    };
}
