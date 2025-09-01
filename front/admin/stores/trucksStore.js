import { defineStore } from 'pinia'
import http from '../../shared/axios.js'

export const useTrucksStore = defineStore('trucks', {
  state: () => ({ items: [], loading: false, error: '' }),
  actions: {
    async fetchList() { this.loading=true; this.error=''; try { const { data } = await http.get('/trucks'); this.items = data?.data || [] } catch(e){ this.error=e.message } finally { this.loading=false } },
    async create(payload) { this.error=''; await http.post('/trucks', payload); await this.fetchList() },
    async update(id,payload){ this.error=''; await http.put(`/trucks/${id}`, payload); await this.fetchList() },
    async remove(id){ this.error=''; await http.delete(`/trucks/${id}`); this.items = this.items.filter(i=>i.id!==id) },
    async listMaintenance(id){ const { data } = await http.get(`/trucks/${id}/maintenance`); return data?.data || [] },
    async addMaintenance(id,payload){ await http.post(`/trucks/${id}/maintenance`, payload) },
  }
})


