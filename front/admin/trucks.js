import { createApp, ref, onMounted, computed } from 'vue/dist/vue.esm-bundler.js'
import { createPinia } from 'pinia'
import { useAuthStore } from '../shared/authStore.js'
import { useTrucksStore } from './stores/trucksStore.js'
import { api } from '../shared/api.js'
import http from '../shared/axios.js'

const App = {
  setup() {
    const pinia = createPinia()
    const auth = useAuthStore(pinia)
    const tr = useTrucksStore(pinia)
    auth.load()

    const err = ref('')
    const dlgNew = ref(null)
    const dlgEdit = ref(null)
    const dlgMaint = ref(null)
    const editId = ref(null)
    const maintId = ref(null)
    const q = ref('')
    const frMap = ref({})

    const filtered = computed(()=> (tr.items||[]).filter(t=> (t.plate||'').toLowerCase().includes(q.value.toLowerCase())))

    async function loadFranchisees(){
      try {
        const { data } = await http.get('/franchisees')
        const items = data?.data?.items || data?.data || []
        const map = {}
        for (const f of items) { if (f && f.id != null) map[f.id] = f.name }
        frMap.value = map
      } catch {}
    }
    function fname(id){ return frMap.value[id] || '' }

    async function ensureAuth() { if (!api.getToken()) { location.href = './login.html'; return false } await auth.me(); if (auth.role !== 'admin') { location.href = './login.html'; return false } return true }
    function openNew(){ document.getElementById('tPlate').value=''; document.getElementById('tStatus').value='active'; document.getElementById('tFrId').value=''; document.getElementById('tLast').value=''; dlgNew.value.showModal() }
    function openEdit(item){ editId.value=item.id; document.getElementById('tPlateE').value=item.plate||''; document.getElementById('tStatusE').value=item.status||''; document.getElementById('tFrIdE').value=item.franchisee_id||''; document.getElementById('tLastE').value=item.last_service_at||''; dlgEdit.value.showModal() }
    async function saveNew(){ try{ err.value=''; await tr.create({ plate:document.getElementById('tPlate').value.trim(), status:document.getElementById('tStatus').value.trim(), franchisee_id:Number(document.getElementById('tFrId').value)||null, last_service_at:document.getElementById('tLast').value||null }); dlgNew.value.close() } catch(e){ err.value=e.message } }
    async function saveEdit(){ try{ err.value=''; await tr.update(editId.value, { plate:document.getElementById('tPlateE').value.trim(), status:document.getElementById('tStatusE').value.trim(), franchisee_id:Number(document.getElementById('tFrIdE').value)||null, last_service_at:document.getElementById('tLastE').value||null }); dlgEdit.value.close() } catch(e){ err.value=e.message } }
    async function remove(item){ if(!confirm(`Supprimer #${item.id} ?`)) return; await tr.remove(item.id) }

    async function openMaint(item){ maintId.value=item.id; await loadMaint(); dlgMaint.value.showModal() }
    async function loadMaint(){ const list = await tr.listMaintenance(maintId.value); const el = document.getElementById('maintList'); el.innerHTML = list.map(m=>`<div>- ${m.serviced_at} | ${m.title} | ${m.cost}</div>`).join('') }
    async function addMaint(){ try{ await tr.addMaintenance(maintId.value, { title:document.getElementById('mTitle').value.trim(), description:document.getElementById('mDesc').value.trim(), cost:Number(document.getElementById('mCost').value)||0, serviced_at:document.getElementById('mDate').value }); await loadMaint(); } catch(e){ alert(e.message) } }

    onMounted( async () => {
      dlgNew.value = document.getElementById('dlgNew'); dlgEdit.value = document.getElementById('dlgEdit'); dlgMaint.value = document.getElementById('dlgMaint')
      if (!(await ensureAuth())) return
      await loadFranchisees()
      await tr.fetchList()
      document.getElementById('btnNew').onclick = openNew
      document.getElementById('dlgSaveNew').onclick = saveNew
      document.getElementById('dlgCloseNew').onclick = ()=>dlgNew.value.close()
      document.getElementById('dlgSaveEdit').onclick = saveEdit
      document.getElementById('dlgCloseEdit').onclick = ()=>dlgEdit.value.close()
      document.getElementById('dlgSaveMaint').onclick = addMaint
      document.getElementById('dlgCloseMaint').onclick = ()=>dlgMaint.value.close()
      const s = document.getElementById('tSearch'); if (s) s.addEventListener('input', (e)=>{ q.value = e.target.value })
    })

    return { tr, err, openEdit, remove, openMaint, filtered, fname }
  },
  template:`
      <tr v-for="t in filtered" :key="t.id">
        <td>{{ t.plate }}</td>
        <td>{{ t.status }}</td>
        <td>{{ fname(t.franchisee_id) }}</td>
        <td>{{ t.last_service_at }}</td>
        <td>
          <button class="btn" @click="openEdit(t)">Modifier</button>
          <button class="btn" @click="openMaint(t)">Maintenance</button>
          <button class="btn btn-danger" @click="remove(t)">Supprimer</button>
        </td>
      </tr>
  `
}

createApp(App).use(createPinia()).mount('#rows')


