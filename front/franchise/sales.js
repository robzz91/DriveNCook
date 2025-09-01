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

    async function ensureAuth(){ if(!api.getToken()){ location.href='./login.html'; return false } await auth.me(); if(auth.role!=='franchisee'){ location.href='./login.html'; return false } return true }

    async function load(){ try{ const { data } = await http.get('/sales'); items.value = data?.data || [] } catch(e){ err.value=e.message } }

    function openNew(){ document.getElementById('vAmount').value=''; document.getElementById('vDate').value=''; dlgNew.value.showModal() }
    async function saveNew(){ try{ err.value=''; const amount = Number(document.getElementById('vAmount').value)||0; const sold_at = document.getElementById('vDate').value ? new Date(document.getElementById('vDate').value).toISOString().slice(0,19).replace('T',' ') : undefined; await http.post('/sales', { amount, sold_at }); dlgNew.value.close(); await load() } catch(e){ err.value = e.message } }
    async function remove(item){ if(!confirm(`Supprimer la vente #${item.id} ?`)) return; await http.delete(`/sales/${item.id}`); await load() }

    async function exportMine(){ const { blob } = await api.fetchPdf('/sales/report.php'); const url = URL.createObjectURL(blob); const a = document.createElement('a'); a.href=url; a.download='mes_ventes.pdf'; document.body.appendChild(a); a.click(); a.remove(); setTimeout(()=>URL.revokeObjectURL(url),3000) }

    onMounted(async ()=>{
      dlgNew.value = document.getElementById('dlgNew')
      if(!(await ensureAuth())) return
      await load()
      document.getElementById('btnNew').onclick = openNew
      document.getElementById('dlgSaveNew').onclick = saveNew
      document.getElementById('dlgCloseNew').onclick = ()=>dlgNew.value.close()
      document.getElementById('btnExport').onclick = exportMine
    })

    function formatAmount(n){ try{ return new Intl.NumberFormat('fr-FR',{style:'currency',currency:'EUR'}).format(Number(n)||0) }catch{ return (Number(n)||0).toFixed(2)+' €' } }

    return { err, items, openNew, saveNew, remove, formatAmount }
  },
  template:`
    <tr v-for="s in items" :key="s.id">
      <td>{{ formatAmount(s.amount) }}</td>
      <td>{{ s.sold_at }}</td>
      <td><button class="btn btn-danger" @click="remove(s)">Supprimer</button></td>
    </tr>
  `
}

createApp(App).use(createPinia()).mount('#rows')
