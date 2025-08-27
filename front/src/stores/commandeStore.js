import { defineStore } from "pinia";
import { api } from "@/api";

export const useCommandeStore = defineStore("commandeStore", {
  state: () => ({
    items: [],
    loading: false,
    error: "",
  }),
  actions: {
    async fetchAll() {
      this.loading = true; this.error = "";
      try {
        const { data } = await api.get("/commandes");
        this.items = data;
      } catch (e) {
        this.error = e?.message || "Erreur chargement commandes";
      } finally {
        this.loading = false;
      }
    },
  },
});
