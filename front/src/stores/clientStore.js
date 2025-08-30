import { defineStore } from "pinia";
import api from "@/api";

export const useClientStore = defineStore("clientStore", {
  state: () => ({
    items: [],
    loading: false,
    error: "",
  }),
  actions: {
    async fetchAll() {
      this.loading = true;
      this.error = "";
      try {
        const { data } = await api.get("/clients");
        this.items = data;
      } catch (e) {
        this.error = e?.message || "Erreur chargement clients";
      } finally {
        this.loading = false;
      }
    },

    async addClient(client) {
      try {
        const { data } = await api.post("/clients", client);
        this.items.push(data);
      } catch (e) {
        this.error = e?.message || "Erreur ajout client";
      }
    },

    async deleteClient(id) {
      try {
        await api.delete(`/clients/${id}`);
        this.items = this.items.filter(c => c.id !== id);
      } catch (e) {
        this.error = e?.message || "Erreur suppression client";
      }
    },
  },
});
