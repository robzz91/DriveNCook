import { createApp, ref, onMounted } from 'vue/dist/vue.esm-bundler.js'
import { createPinia } from 'pinia'
import { useAuthStore } from '../shared/authStore.js'
import { useSalesStore } from './stores/salesStore.js'
import { api } from '../shared/api.js'
import http from '../shared/axios.js'

const App = {
  setup() {
    const pinia = createPinia()
    const auth = useAuthStore(pinia)
    const sl = useSalesStore(pinia)
    auth.load()

    const frName = ref('')
    const frMap = ref({})
    const frList = ref([])

    const dlgExport = ref(null)

    async function ensureAuth(){ if(!api.getToken()){ location.href='./login.html'; return false } await auth.me(); if(auth.role!=='admin'){ location.href='./login.html'; return false } return true }

    async function loadFranchisees(){
      try{
        const { data } = await http.get('/franchisees')
        const items = data?.data?.items || data?.data || []
        frList.value = items
        const map = {}; for(const f of items){ map[f.id]=f.name }
        frMap.value = map
      }catch{}
    }

    function fname(id){ return frMap.value[id] || '' }

    function findFranchiseeIdByNamePart(namePart){
      const n = (namePart||'').trim().toLowerCase()
      if(!n) return ''
      const found = frList.value.find(f => (f.name||'').toLowerCase().includes(n))
      return found ? found.id : ''
    }

    async function filter(){
      const p = {}
      const fid = findFranchiseeIdByNamePart(frName.value)
      if(fid) p.franchisee_id = fid
      await sl.fetchList(p)
    }

    async function exportPdfForParams(params){
      const qs = new URLSearchParams(params)
      const { blob, filename } = await api.fetchPdf(`/sales/report.php${qs.toString()?('?'+qs.toString()):''}`)
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = filename || 'rapport.pdf'
      document.body.appendChild(a)
      a.click()
      a.remove()
      setTimeout(()=>URL.revokeObjectURL(url), 3000)
    }

    function exportRow(sale){ exportPdfForParams({ franchisee_id: sale.franchisee_id }) }

    function openExportDialog(){ dlgExport.value.showModal(); renderExportList('') }
    function closeExportDialog(){ dlgExport.value.close() }

    function renderExportList(q){
      const el = document.getElementById('exList')
      const list = frList.value.filter(f => (f.name||'').toLowerCase().includes((q||'').toLowerCase()))
      el.innerHTML = list.map(f => `<div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid #eee"><span>${f.name}</span><button data-id="${f.id}" class="btn">Exporter</button></div>`).join('')
      ;[...el.querySelectorAll('button[data-id]')].forEach(btn => {
        btn.addEventListener('click', async () => {
          const id = btn.getAttribute('data-id')
          await exportPdfForParams({ franchisee_id: id })
          closeExportDialog()
        })
      })
    }

    onMounted( async ()=>{
      dlgExport.value = document.getElementById('dlgExport')
      if(!(await ensureAuth())) return
      await loadFranchisees()
      const nameInput = document.getElementById('fFrName')
      if(nameInput) nameInput.addEventListener('input', e => { frName.value = e.target.value })
      document.getElementById('btnFilter').onclick = filter
      const exBtn = document.getElementById('btnExportFr')
      if (exBtn) exBtn.onclick = openExportDialog
      const exSearch = document.getElementById('exSearch')
      if (exSearch) exSearch.addEventListener('input', e => renderExportList(e.target.value))
      await filter()
    })

    return { sl, fname, exportRow, dlgExport, closeExportDialog }
  },
  template:`
      <tr v-for="s in sl.items" :key="s.id">
        <td>{{ fname(s.franchisee_id) }}</td>
        <td>{{ Number(s.amount).toFixed(2) }}</td>
        <td>{{ s.sold_at }}</td>
        <td><button class="btn" @click="exportRow(s)">Exporter PDF</button></td>
      </tr>
  `
}

createApp(App).use(createPinia()).mount('#rows')


