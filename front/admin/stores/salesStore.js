import { defineStore } from 'pinia'
import http from '../../shared/axios.js'

export const useSalesStore = defineStore('sales', {
  state: () => ({ items: [], loading: false, error: '' }),
  actions: {
    async fetchList(params={}) { this.loading=true; this.error=''; try { const usp=new URLSearchParams(params); const { data } = await http.get('/sales'+(usp.toString()?('?'+usp.toString()):'')); this.items=data?.data||[] } catch(e){ this.error=e.message } finally { this.loading=false } },
  }
})


