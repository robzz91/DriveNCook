import { createApp, ref, onMounted, computed } from 'vue/dist/vue.esm-bundler.js'
import { createPinia } from 'pinia'
import { useAuthStore } from '../shared/authStore.js'
import { useFranchiseesStore } from './stores/franchiseesStore.js'
import { api } from '../shared/api.js'

const App = {
  setup() {
    const pinia = createPinia()
    const auth = useAuthStore(pinia)
    const fr = useFranchiseesStore(pinia)
    auth.load()

    const err = ref('')
    const dlgNew = ref(null)
    const dlgEdit = ref(null)
    const editId = ref(null)
    const q = ref('')

    const filtered = computed(()=> (fr.items||[]).filter(f=> (f.name||'').toLowerCase().includes(q.value.toLowerCase())))

    async function ensureAuth() {
      if (!api.getToken()) { location.href = './login.html'; return false }
      await auth.me();
      if (auth.role !== 'admin') { location.href = './login.html'; return false }
      return true
    }

    function openNew() {
      document.getElementById('frName').value = ''
      document.getElementById('frEmail').value = ''
      dlgNew.value.showModal()
    }
    function openEdit(item) {
      editId.value = item.id
      document.getElementById('frNameE').value = item.name || ''
      document.getElementById('frEmailE').value = item.email || ''
      dlgEdit.value.showModal()
    }
    async function saveNew() {
      try {
        err.value=''
        await fr.create({ name: document.getElementById('frName').value.trim(), email: document.getElementById('frEmail').value.trim() })
        dlgNew.value.close()
      } catch (e) { err.value = e.message }
    }
    async function saveEdit() {
      try {
        err.value=''
        await fr.update(editId.value, { name: document.getElementById('frNameE').value.trim(), email: document.getElementById('frEmailE').value.trim() })
        dlgEdit.value.close()
      } catch (e) { err.value = e.message }
    }
    async function remove(item) { if (!confirm(`Supprimer #${item.id} ?`)) return; await fr.remove(item.id) }

    onMounted( async () => {
      dlgNew.value = document.getElementById('dlgNew')
      dlgEdit.value = document.getElementById('dlgEdit')
      if (!(await ensureAuth())) return
      await fr.fetchList()
      document.getElementById('btnNew').onclick = openNew
      document.getElementById('dlgSaveNew').onclick = saveNew
      document.getElementById('dlgCloseNew').onclick = ()=>dlgNew.value.close()
      document.getElementById('dlgSaveEdit').onclick = saveEdit
      document.getElementById('dlgCloseEdit').onclick = ()=>dlgEdit.value.close()
      const s = document.getElementById('frSearch'); if (s) s.addEventListener('input', (e)=>{ q.value = e.target.value })
    })

    return { fr, err, openEdit, remove, filtered }
  },
  template:`
      <tr v-for="f in filtered" :key="f.id">
        <td>{{ f.name }}</td>
        <td>{{ f.email }}</td>
        <td>
          <button class="btn" @click="openEdit(f)">Modifier</button>
          <button class="btn btn-danger" @click="remove(f)">Supprimer</button>
        </td>
      </tr>
  `
}

createApp(App).use(createPinia()).mount('#rows')


