import { createApp, ref, onMounted, computed } from 'vue/dist/vue.esm-bundler.js'
import { createPinia } from 'pinia'
import { useAuthStore } from '../shared/authStore.js'
import { useWarehousesStore } from './stores/warehousesStore.js'
import { api } from '../shared/api.js'

const App = {
  setup() {
    const pinia = createPinia()
    const auth = useAuthStore(pinia)
    const wh = useWarehousesStore(pinia)
    auth.load()

    const err = ref('')
    const dlgNew = ref(null)
    const dlgEdit = ref(null)
    const editId = ref(null)
    const q = ref('')

    const filtered = computed(()=> (wh.items||[]).filter(w=> (w.name||'').toLowerCase().includes(q.value.toLowerCase())))

    async function ensureAuth() {
      if (!api.getToken()) { location.href = './login.html'; return false }
      await auth.me();
      if (auth.role !== 'admin') { location.href = './login.html'; return false }
      return true
    }

    function openNew() { document.getElementById('wName').value=''; document.getElementById('wAddress').value=''; document.getElementById('wCapacity').value=''; dlgNew.value.showModal() }
    function openEdit(item) { editId.value=item.id; document.getElementById('wNameE').value=item.name||''; document.getElementById('wAddressE').value=item.address||''; document.getElementById('wCapacityE').value=item.capacity||''; dlgEdit.value.showModal() }
    async function saveNew(){ try{ err.value=''; await wh.create({ name: document.getElementById('wName').value.trim(), address: document.getElementById('wAddress').value.trim(), capacity: Number(document.getElementById('wCapacity').value)||null }); dlgNew.value.close() } catch(e){ err.value=e.message } }
    async function saveEdit(){ try{ err.value=''; await wh.update(editId.value, { name: document.getElementById('wNameE').value.trim(), address: document.getElementById('wAddressE').value.trim(), capacity: Number(document.getElementById('wCapacityE').value)||null }); dlgEdit.value.close() } catch(e){ err.value=e.message } }
    async function remove(item){ if(!confirm(`Supprimer #${item.id} ?`)) return; await wh.remove(item.id) }

    onMounted( async () => {
      dlgNew.value = document.getElementById('dlgNew')
      dlgEdit.value = document.getElementById('dlgEdit')
      if (!(await ensureAuth())) return
      await wh.fetchList()
      document.getElementById('btnNew').onclick = openNew
      document.getElementById('dlgSaveNew').onclick = saveNew
      document.getElementById('dlgCloseNew').onclick = ()=>dlgNew.value.close()
      document.getElementById('dlgSaveEdit').onclick = saveEdit
      document.getElementById('dlgCloseEdit').onclick = ()=>dlgEdit.value.close()
      const s = document.getElementById('wSearch'); if (s) s.addEventListener('input', (e)=>{ q.value=e.target.value })
    })

    return { wh, err, openEdit, remove, filtered }
  },
  template:`
      <tr v-for="w in filtered" :key="w.id">
        <td>{{ w.name }}</td>
        <td>{{ w.address }}</td>
        <td>{{ w.capacity }}</td>
        <td>
          <button class="btn" @click="openEdit(w)">Modifier</button>
          <button class="btn btn-danger" @click="remove(w)">Supprimer</button>
        </td>
      </tr>
  `
}

createApp(App).use(createPinia()).mount('#rows')


