import { defineStore } from "pinia";
import api from "@/api";

export const useClientStore = defineStore("client", {
  state: () => ({
    clients: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchClients() {
      this.loading = true;
      this.error = null;
      try {
        const res = await api.get("/clients");
        this.clients = res.data;
      } catch (err) {
        this.error = err.response?.data?.message || err.message;
      } finally {
        this.loading = false;
      }
    },

    async addClient(client) {
      this.error = null;
      try {
        const res = await api.post("/clients", client);
        this.clients.push(res.data);
        return res.data;
      } catch (err) {
        this.error = err.response?.data?.message || err.message;
        throw err;
      }
    },

    async updateClient(id, client) {
      this.error = null;
      try {
        const res = await api.put(`/clients/${id}`, client);
        const index = this.clients.findIndex(c => c.id === id);
        if (index !== -1) this.clients[index] = res.data;
        return res.data;
      } catch (err) {
        this.error = err.response?.data?.message || err.message;
        throw err;
      }
    },

    async deleteClient(id) {
      this.error = null;
      try {
        await api.delete(`/clients/${id}`);
        this.clients = this.clients.filter(c => c.id !== id);
      } catch (err) {
        this.error = err.response?.data?.message || err.message;
        throw err;
      }
    },
  },
});
