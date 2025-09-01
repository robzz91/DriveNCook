import { createApp, ref, onMounted } from 'vue/dist/vue.esm-bundler.js'
import { createPinia } from 'pinia'
import { useAuthStore } from '../shared/authStore.js'
import { useSuppliesStore } from './stores/suppliesStore.js'
import { api } from '../shared/api.js'
import http from '../shared/axios.js'

const App = {
  setup() {
    const pinia = createPinia()
    const auth = useAuthStore(pinia)
    const sp = useSuppliesStore(pinia)
    auth.load()

    const frMap = ref({})
    const whMap = ref({})

    async function ensureAuth(){ if(!api.getToken()){ location.href='./login.html'; return false } await auth.me(); if(auth.role!=='admin'){ location.href='./login.html'; return false } return true }
    async function loadFranchisees(){ try{ const { data } = await http.get('/franchisees'); const items = data?.data?.items || data?.data || []; const map = {}; for(const f of items){ map[f.id]=f.name } frMap.value = map } catch{} }
    async function loadWarehouses(){ try{ const { data } = await http.get('/warehouses'); const items = data?.data || []; const map = {}; for(const w of items){ map[w.id]=w.name } whMap.value = map } catch{} }

    onMounted( async ()=>{ if(!(await ensureAuth())) return; await Promise.all([loadFranchisees(), loadWarehouses()]); await sp.fetchList(); })

    function fname(id){ return frMap.value[id] || '' }
    function wname(id){ return whMap.value[id] || '' }
    async function remove(item){ if(!confirm(`Supprimer l'approvisionnement #${item.id} ?`)) return; await sp.remove(item.id) }

    return { sp, fname, wname, remove }
  },
  template:`
      <tr v-for="s in sp.items" :key="s.id" style="font-size:15px">
        <td>{{ fname(s.franchisee_id) }}</td>
        <td>{{ wname(s.warehouse_id) }}</td>
        <td>{{ Number(s.total_amount).toFixed(2) }}</td>
        <td>
          <button class="btn btn-danger" @click="remove(s)">Supprimer</button>
        </td>
      </tr>
  `
}

createApp(App).use(createPinia()).mount('#rows')


