// client/src/stores/commandeStore.js
import { defineStore } from "pinia";
import api from "../api";

export const useCommandeStore = defineStore("commande", {
  state: () => ({
    items: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchAll() {
      this.loading = true;
      this.error = null;
      try {
        const { data } = await api.get("/commandes");
        this.items = Array.isArray(data) ? data : (data?.data || []);
      } catch (e) {
        this.error = e.response?.data?.message || e.message;
        throw e;
      } finally {
        this.loading = false;
      }
    },

    async getOne(id) {
      const { data } = await api.get(`/commandes/${id}`);
      return data;
    },

    async create(payload) {
      const { data } = await api.post("/commandes", payload);
      this.items.push(data);
      return data;
    },

    async update(id, payload) {
      const { data } = await api.put(`/commandes/${id}`, payload);
      const idx = this.items.findIndex((x) => x.id === data.id);
      if (idx > -1) this.items[idx] = data;
      return data;
    },

    async remove(id) {
      await api.delete(`/commandes/${id}`);
      this.items = this.items.filter((x) => x.id !== id);
    },
  },
});
