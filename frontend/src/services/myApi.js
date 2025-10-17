import { createApiClient } from "./api";

export const api = createApiClient({
    baseURL: "http://localhost:8000/api",
});
