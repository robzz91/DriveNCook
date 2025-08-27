import { defineStore } from "pinia";
import { api } from "@/api";

export const usePlatStore = defineStore("platStore", {
  state: () => ({
    items: [],
    loading: false,
    error: "",
  }),
  actions: {
    async fetchAll() {
      this.loading = true; this.error = "";
      try {
        const { data } = await api.get("/plats");
        this.items = data;
      } catch (e) {
        this.error = e?.message || "Erreur chargement plats";
      } finally {
        this.loading = false;
      }
    },
  },
});
