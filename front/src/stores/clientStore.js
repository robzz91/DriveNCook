// src/stores/clientStore.js
import { defineStore } from "pinia";
import api from "../api";

export const useClientStore = defineStore("clientStore", {
  state: () => ({
    clients: [],
  }),
  actions: {
    async fetchClients() {
      try {
        const response = await api.get("/clients");
        this.clients = response.data;
      } catch (error) {
        console.error("Erreur lors du chargement des clients", error);
      }
    },
    async addClient(client) {
      try {
        const response = await api.post("/clients", client);
        this.clients.push(response.data);
      } catch (error) {
        console.error("Erreur lors de l’ajout d’un client", error);
      }
    },
    async deleteClient(id) {
      try {
        await api.delete(`/clients/${id}`);
        this.clients = this.clients.filter(c => c.id !== id);
      } catch (error) {
        console.error("Erreur lors de la suppression du client", error);
      }
    }
  }
});
