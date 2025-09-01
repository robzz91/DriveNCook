import { createApp, ref, onMounted } from 'vue/dist/vue.esm-bundler.js'
import { createPinia } from 'pinia'
import { useAuthStore } from '../shared/authStore.js'
import { useUsersStore } from './stores/usersStore.js'
import { api } from '../shared/api.js'

const App = {
  setup() {
    const pinia = createPinia()
    const auth = useAuthStore(pinia)
    const users = useUsersStore(pinia)
    auth.load()

    const role = ref('')
    const q = ref('')
    const err = ref('')

    const dlgNew = ref(null)
    const dlgEdit = ref(null)
    const dlgTitle = ref('Modifier')
    const editId = ref(null)

    function updateRoleFieldsNew() { /* plus d'IDs à saisir, rien à afficher */ }
    function updateRoleFieldsEdit() { /* plus d'IDs à saisir, rien à afficher */ }

    async function ensureAuth() {
      if (!api.getToken()) { location.href = './login.html'; return false }
      await auth.me();
      if (auth.role !== 'admin') { location.href = './login.html'; return false }
      return true
    }

    async function search() {
      users.filters = { role: role.value, q: q.value }
      await users.fetchList(users.filters)
      const errSpan = document.getElementById('err'); if (errSpan) errSpan.textContent = users.error || ''
    }

    function openNew() {
      editId.value = null
      document.getElementById('fName').value = ''
      document.getElementById('fEmail').value = ''
      document.getElementById('fPassword').value = ''
      document.getElementById('fRole').value = 'client'
      dlgNew.value.showModal()
    }

    function openEdit(u) {
      editId.value = u.id
      dlgTitle.value = `Modifier #${u.id}`
      document.getElementById('fNameE').value = u.name || ''
      document.getElementById('fEmailE').value = u.email || ''
      document.getElementById('fPasswordE').value = ''
      document.getElementById('fRoleE').value = u.role || 'client'
      dlgEdit.value.showModal()
    }

    async function saveNew() {
      try {
        err.value=''
        const nameVal = document.getElementById('fName').value.trim()
        const emailVal = document.getElementById('fEmail').value.trim()
        const passVal = document.getElementById('fPassword').value
        const roleVal = document.getElementById('fRole').value
        const payload = { name: nameVal, email: emailVal, role: roleVal }
        if (passVal) payload.password = passVal
        await users.create(payload)
        dlgNew.value.close()
      } catch (e) {
        const apiMsg = e?.response?.data?.error || e?.message || 'Erreur'
        err.value = apiMsg; const errSpan = document.getElementById('err'); if (errSpan) errSpan.textContent = apiMsg
      }
    }

    async function saveEdit() {
      try {
        err.value=''
        const nameVal = document.getElementById('fNameE').value.trim()
        const emailVal = document.getElementById('fEmailE').value.trim()
        const passVal = document.getElementById('fPasswordE').value
        const roleVal = document.getElementById('fRoleE').value
        const payload = { name: nameVal, email: emailVal, role: roleVal }
        if (passVal) payload.password = passVal
        await users.update(editId.value, payload)
        dlgEdit.value.close()
      } catch (e) {
        const apiMsg = e?.response?.data?.error || e?.message || 'Erreur'
        err.value = apiMsg; const errSpan = document.getElementById('err'); if (errSpan) errSpan.textContent = apiMsg
      }
    }

    async function remove(u) { if (!confirm(`Supprimer l'utilisateur #${u.id} ?`)) return; await users.remove(u.id) }

    onMounted(async () => {
      dlgNew.value = document.getElementById('dlgNew')
      dlgEdit.value = document.getElementById('dlgEdit')
      if (!(await ensureAuth())) return
      await search()
      document.getElementById('btnSearch').onclick = search
      document.getElementById('btnNew').onclick = openNew
      document.getElementById('dlgSaveNew').onclick = saveNew
      document.getElementById('dlgCloseNew').onclick = ()=>dlgNew.value.close()
      document.getElementById('dlgSaveEdit').onclick = saveEdit
      document.getElementById('dlgCloseEdit').onclick = ()=>dlgEdit.value.close()
      document.getElementById('filterRole').addEventListener('change', (e)=>{ role.value=e.target.value })
      document.getElementById('filterQ').addEventListener('input', (e)=>{ q.value=e.target.value; search() })
    })

    return { users, role, q, err, openEdit, remove, dlgTitle }
  },
  template: `
      <tr v-for="u in users.items" :key="u.id">
        <td>{{ u.name }}</td>
        <td>{{ u.email }}</td>
        <td>{{ u.role }}</td>
        <td>
          <span v-if="u.role==='franchisee'">franchisee_id={{ u.franchisee_id }}</span>
          <span v-else-if="u.role==='client'">client_id={{ u.client_id }}</span>
        </td>
        <td>
          <button class="btn" @click="openEdit(u)">Modifier</button>
          <button class="btn btn-danger" @click="remove(u)">Supprimer</button>
        </td>
      </tr>
  `
}

createApp(App).use(createPinia()).mount('#rows')


