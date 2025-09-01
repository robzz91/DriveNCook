import { defineStore } from 'pinia'
import http from '../../shared/axios.js'

export const useFranchiseesStore = defineStore('franchisees', {
  state: () => ({ items: [], loading: false, error: '' }),
  actions: {
    async fetchList() {
      this.loading = true; this.error=''
      try { const { data } = await http.get('/franchisees'); this.items = data?.data?.items || data?.data || [] } catch (e) { this.error = e.message }
      finally { this.loading = false }
    },
    async create(payload) { this.error=''; await http.post('/franchisees', payload); await this.fetchList() },
    async update(id, payload) { this.error=''; await http.put(`/franchisees/${id}`, payload); await this.fetchList() },
    async remove(id) { this.error=''; await http.delete(`/franchisees/${id}`); this.items = this.items.filter(i=>i.id!==id) },
  }
})


