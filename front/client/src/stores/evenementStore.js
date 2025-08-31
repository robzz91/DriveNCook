// client/src/stores/evenementStore.js
import { defineStore } from "pinia";
import api from "../api";

export const useEvenementStore = defineStore("evenement", {
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
        const { data } = await api.get("/evenements");
        this.items = Array.isArray(data) ? data : (data?.data || []);
      } catch (e) {
        this.error = e.response?.data?.message || e.message;
        throw e;
      } finally {
        this.loading = false;
      }
    },

    async getOne(id) {
      const { data } = await api.get(`/evenements/${id}`);
      return data;
    },

    async create(payload) {
      const { data } = await api.post("/evenements", payload);
      this.items.push(data);
      return data;
    },

    async update(id, payload) {
      const { data } = await api.put(`/evenements/${id}`, payload);
      const idx = this.items.findIndex((x) => x.id === data.id);
      if (idx > -1) this.items[idx] = data;
      return data;
    },

    async remove(id) {
      await api.delete(`/evenements/${id}`);
      this.items = this.items.filter((x) => x.id !== id);
    },
  },
});
