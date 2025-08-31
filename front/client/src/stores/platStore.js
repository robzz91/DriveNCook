import { defineStore } from "pinia";
import api from "../api";

export const usePlatStore = defineStore("plat", {
  state: () => ({
    plats: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchPlats() {
      this.loading = true;
      try {
        const response = await api.get("/plats");
        this.plats = response.data;
      } catch (err) {
        this.error = err.response?.data?.message || "Erreur lors du chargement";
      } finally {
        this.loading = false;
      }
    },

    async addPlat(plat) {
      try {
        const response = await api.post("/plats", plat);
        this.plats.push(response.data);
      } catch (err) {
        this.error = err.response?.data?.message || "Erreur ajout plat";
      }
    },

    async deletePlat(id) {
      try {
        await api.delete(`/plats/${id}`);
        this.plats = this.plats.filter(p => p.id !== id);
      } catch (err) {
        this.error = err.response?.data?.message || "Erreur suppression plat";
      }
    },
  },
});
