import { createApp, ref, onMounted } from 'vue/dist/vue.esm-bundler.js'
import { createPinia } from 'pinia'
import { useAuthStore } from '../shared/authStore.js'
import http from '../shared/axios.js'
import { api } from '../shared/api.js'

const App = {
  setup(){
    const pinia = createPinia()
    const auth = useAuthStore(pinia)
    auth.load()

    const err = ref('')
    const items = ref([])
    const dlgNew = ref(null)
    const whs = ref([])

    async function ensureAuth(){ if(!api.getToken()){ location.href='./login.html'; return false } await auth.me(); if(auth.role!=='franchisee'){ location.href='./login.html'; return false } return true }

    async function load(){ try{ const { data } = await http.get('/supplies'); items.value = data?.data || [] } catch(e){ err.value=e.message } }
    async function loadWarehouses(){ try{ const { data } = await http.get('/warehouses'); whs.value = data?.data || [] } catch{} }

    function openNew(){ const s=document.getElementById('wSelect'); s.innerHTML = whs.value.map(w=>`<option value="${w.id}">${w.name}</option>`).join(''); dlgNew.value.showModal() }
    async function saveNew(){ try{ err.value=''; const wid = Number(document.getElementById('wSelect').value)||null; await http.post('/supplies', { warehouse_id: wid }); dlgNew.value.close(); await load() } catch(e){ err.value = e.message } }
    async function remove(item){ if(!confirm(`Supprimer l'approvisionnement #${item.id} ?`)) return; await http.delete(`/supplies/${item.id}`); await load() }

    onMounted(async ()=>{
      dlgNew.value = document.getElementById('dlgNew')
      if(!(await ensureAuth())) return
      await Promise.all([load(), loadWarehouses()])
      document.getElementById('btnNew').onclick = openNew
      document.getElementById('dlgSaveNew').onclick = saveNew
      document.getElementById('dlgCloseNew').onclick = ()=>dlgNew.value.close()
    })

    return { err, items, openNew, saveNew, remove }
  },
  template:`
    <tr v-for="s in items" :key="s.id">
      <td>{{ s.warehouse_id }}</td>
      <td>{{ Number(s.total_amount).toFixed(2) }}</td>
      <td><button class="btn btn-danger" @click="remove(s)">Supprimer</button></td>
    </tr>
  `
}

createApp(App).use(createPinia()).mount('#rows')
