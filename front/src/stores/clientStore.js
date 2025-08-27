import { defineStore } from "pinia";
import { api } from "@/api";

export const useClientStore = defineStore("clientStore", {
  state: () => ({
    items: [],
    loading: false,
    error: "",
  }),
  actions: {
    async fetchAll() {
      this.loading = true; this.error = "";
      try {
        const { data } = await api.get("/clients");
        this.items = data;
      } catch (e) {
        this.error = e?.message || "Erreur chargement clients";
      } finally {
        this.loading = false;
      }
    },
  },
});
