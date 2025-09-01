import { defineStore } from 'pinia'
import http from '../../shared/axios.js'

export const useSuppliesStore = defineStore('supplies', {
  state: () => ({ items: [], loading: false, error: '' }),
  actions: {
    async fetchList() { this.loading=true; this.error=''; try { const { data } = await http.get('/supplies'); this.items = data?.data || [] } catch(e){ this.error=e.message } finally { this.loading=false } },
    async fetchItems(supplyId){ const { data } = await http.get(`/supplies/${supplyId}/items`); return data?.data || [] },
    async remove(id){ this.error=''; await http.delete(`/supplies/${id}`); this.items = this.items.filter(i=>i.id!==id) }
  }
})


