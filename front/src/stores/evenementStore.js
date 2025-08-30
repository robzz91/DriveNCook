import { defineStore } from "pinia";
import api from "../api";

export const useEvenementStore = defineStore("evenement", {
  state: () => ({
    evenements: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchEvenements() {
      this.loading = true;
      try {
        const response = await api.get("/evenements");
        this.evenements = response.data;
      } catch (err) {
        this.error = err.response?.data?.message || "Erreur chargement événements";
      } finally {
        this.loading = false;
      }
    },

    async addEvenement(evenement) {
      try {
        const response = await api.post("/evenements", evenement);
        this.evenements.push(response.data);
      } catch (err) {
        this.error = err.response?.data?.message || "Erreur ajout événement";
      }
    },

    async deleteEvenement(id) {
      try {
        await api.delete(`/evenements/${id}`);
        this.evenements = this.evenements.filter(e => e.id !== id);
      } catch (err) {
        this.error = err.response?.data?.message || "Erreur suppression événement";
      }
    },
  },
});
