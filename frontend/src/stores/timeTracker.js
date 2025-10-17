import { defineStore } from "pinia";
import { api } from "@/services/myApi";

export const useTimeTrackerStore = defineStore("timeTracker", {
    state: () => ({
        sessions: [],
        activeSession: null,
        analytics: null,
        loading: false,
        error: null,
    }),

    getters: {
        hasActiveSession: (state) => state.activeSession !== null,

        totalSessions: (state) => state.sessions.length,

        completedSessions: (state) =>
            state.sessions.filter((s) => s.end_time !== null),
    },

    actions: {
        async startSession(taskName, startTime = null) {
            this.loading = true;
            this.error = null;

            try {
                const response = await api.post("/sessions/start", {
                    task_name: taskName,
                    start_time: startTime || new Date().toISOString(),
                });

                this.activeSession = response.data.session;
                await this.fetchSessions();

                return response.data;
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Error starting session";
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async stopSession(sessionId, endTime = null) {
            this.loading = true;
            this.error = null;

            try {
                const response = await api.post("/sessions/stop", {
                    session_id: sessionId,
                    end_time: endTime || new Date().toISOString(),
                });

                this.activeSession = null;
                await this.fetchSessions();

                return response.data;
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Error stopping session";
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchSessions() {
            this.loading = true;
            this.error = null;

            try {
                const response = await api.get("/sessions");
                this.sessions = response.data.sessions;

                // Find active session
                this.activeSession =
                    this.sessions.find((s) => s.end_time === null) || null;
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Error fetching sessions";
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async deleteSession(id) {
            this.loading = true;
            this.error = null;

            try {
                await api.delete(`/sessions/${id}`);
                await this.fetchSessions();
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Error deleting session";
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchAnalytics(period = "day") {
            this.loading = true;
            this.error = null;

            try {
                const response = await api.get(
                    `/sessions/analytics?period=${period}`
                );
                this.analytics = response.data;
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Error fetching analytics";
                throw error;
            } finally {
                this.loading = false;
            }
        },

        formatDuration(seconds) {
            if (!seconds) return "0s";

            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;

            const parts = [];
            if (hours > 0) parts.push(`${hours}h`);
            if (minutes > 0) parts.push(`${minutes}m`);
            if (secs > 0 || parts.length === 0) parts.push(`${secs}s`);

            return parts.join(" ");
        },
    },
});
