import { defineStore } from 'pinia'
import http from '../../shared/axios.js'

export const useWarehousesStore = defineStore('warehouses', {
  state: () => ({ items: [], loading: false, error: '' }),
  actions: {
    async fetchList() { this.loading=true; this.error=''; try { const { data } = await http.get('/warehouses'); this.items = data?.data || [] } catch(e) { this.error=e.message } finally { this.loading=false } },
    async create(payload) { this.error=''; await http.post('/warehouses', payload); await this.fetchList() },
    async update(id,payload){ this.error=''; await http.put(`/warehouses/${id}`, payload); await this.fetchList() },
    async remove(id){ this.error=''; await http.delete(`/warehouses/${id}`); this.items = this.items.filter(i=>i.id!==id) },
  }
})


