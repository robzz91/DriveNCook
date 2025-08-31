// client/src/stores/authStore.js
import { defineStore } from "pinia";
import api from "../api";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    token: localStorage.getItem("token") || null,
    loading: false,
    error: null,
  }),

  getters: {
    isAuthenticated: (s) => !!s.token,
  },

  actions: {
    async login(email, password) {
      this.loading = true;
      this.error = null;
      try {
        const { data } = await api.post("/auth/login", { email, password });
        // Attendu: { token: "...", user: {...} }
        this.token = data.token;
        localStorage.setItem("token", data.token);
        this.user = data.user || null;
        return true;
      } catch (e) {
        this.error = e.response?.data?.message || "Échec de connexion";
        return false;
      } finally {
        this.loading = false;
      }
    },

    async fetchMe() {
      if (!this.token) return null;
      try {
        const { data } = await api.get("/auth/me");
        this.user = data || null;
        return this.user;
      } catch {
        return null;
      }
    },

    async logout() {
      try {
        await api.post("/auth/logout");
      } catch {
        // pas grave si ça échoue
      } finally {
        localStorage.removeItem("token");
        this.token = null;
        this.user = null;
      }
    },
  },
});
