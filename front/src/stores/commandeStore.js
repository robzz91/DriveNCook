import { defineStore } from "pinia";
import api from "../api";

export const useCommandeStore = defineStore("commande", {
  state: () => ({
    commandes: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchCommandes() {
      this.loading = true;
      try {
        const response = await api.get("/commandes");
        this.commandes = response.data;
      } catch (err) {
        this.error = err.response?.data?.message || "Erreur chargement commandes";
      } finally {
        this.loading = false;
      }
    },

    async addCommande(commande) {
      try {
        const response = await api.post("/commandes", commande);
        this.commandes.push(response.data);
      } catch (err) {
        this.error = err.response?.data?.message || "Erreur ajout commande";
      }
    },

    async deleteCommande(id) {
      try {
        await api.delete(`/commandes/${id}`);
        this.commandes = this.commandes.filter(c => c.id !== id);
      } catch (err) {
        this.error = err.response?.data?.message || "Erreur suppression commande";
      }
    },
  },
});
