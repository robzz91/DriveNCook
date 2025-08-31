import { defineStore } from "pinia";
import api from "@/api";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    token: localStorage.getItem("token") || null,
  }),

  actions: {
    async register(nom, email, password) {
      try {
        const res = await api.post("/register", {
          nom,
          email,
          password,
        });

        this.user = res.data.user;
        this.token = res.data.token;
        localStorage.setItem("token", this.token);
      } catch (err) {
        console.error("Erreur register:", err.response?.data || err);
        throw err;
      }
    },

    async login(email, password) {
      try {
        const res = await api.post("/login", { email, password });

        this.user = res.data.user;
        this.token = res.data.token;
        localStorage.setItem("token", this.token);
      } catch (err) {
        console.error("Erreur login:", err.response?.data || err);
        throw err;
      }
    },

    async logout() {
      try {
        await api.post("/logout");
      } catch (err) {
        console.error("Erreur logout:", err.response?.data || err);
      } finally {
        this.user = null;
        this.token = null;
        localStorage.removeItem("token");
      }
    },

    isAuthenticated() {
      return !!this.token;
    },
  },
});
