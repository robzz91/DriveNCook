import { defineStore } from 'pinia'
import http from '../../shared/axios.js'

export const useUsersStore = defineStore('users', {
  state: () => ({ items: [], loading: false, error: '', filters: { role: '', q: '' } }),
  actions: {
    async fetchList(filters = {}) {
      this.loading = true; this.error=''
      try {
        const params = new URLSearchParams()
        if (filters.role) params.set('role', filters.role)
        if (filters.q) params.set('q', filters.q)
        const { data } = await http.get('/users' + (params.toString()? ('?'+params.toString()):''))
        this.items = data?.data || []
      } catch (e) { this.error = e.message }
      finally { this.loading = false }
    },
    async create(payload) {
      this.error=''
      const { data } = await http.post('/users', payload)
      await this.fetchList(this.filters)
      return data?.data
    },
    async update(id, payload) {
      this.error=''
      const { data } = await http.put(`/users/${id}`, payload)
      await this.fetchList(this.filters)
      return data?.data
    },
    async remove(id) {
      this.error=''
      await http.delete(`/users/${id}`)
      this.items = this.items.filter(i=>i.id!==id)
    }
  }
})


