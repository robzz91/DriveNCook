// src/stores/commandeStore.js
import { defineStore } from "pinia";
import api from "../api";

export const useCommandeStore = defineStore("commandeStore", {
  state: () => ({
    commandes: [],
  }),
  actions: {
    async fetchCommandes() {
      try {
        const response = await api.get("/commandes");
        this.commandes = response.data;
      } catch (error) {
        console.error("Erreur lors du chargement des commandes", error);
      }
    },
    async addCommande(commande) {
      try {
        const response = await api.post("/commandes", commande);
        this.commandes.push(response.data);
      } catch (error) {
        console.error("Erreur lors de l’ajout d’une commande", error);
      }
    },
    async deleteCommande(id) {
      try {
        await api.delete(`/commandes/${id}`);
        this.commandes = this.commandes.filter(c => c.id !== id);
      } catch (error) {
        console.error("Erreur lors de la suppression de la commande", error);
      }
    }
  }
});
