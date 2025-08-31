import { createApp, ref, onMounted } from 'vue/dist/vue.esm-bundler.js'
import { api } from '../shared/api.js'

const App = {
  setup() {
    const email = ref('admin@example.com')
    const password = ref('password')
    const status = ref('Non connecté')
    const error = ref('')
    const trucks = ref([])

    async function login() {
      error.value = ''
      try {
        await api.login(email.value, password.value)
        status.value = 'Connecté'
        await load()
      } catch (e) { error.value = e.message }
    }

    async function logout() {
      error.value = ''
      try { await api.logout(); status.value = 'Déconnecté'; trucks.value = [] } catch (e) { error.value = e.message }
    }

    async function load() {
      try { trucks.value = (await api.get('/trucks')).data || [] } catch (e) { error.value = e.message }
    }

    onMounted(() => { if (api.getToken()) { status.value = 'Connecté'; load() } })

    return { email, password, status, error, trucks, login, logout }
  },
  template: `
  <main style="font-family:sans-serif;padding:16px;background:#fff">
    <header style="display:flex;justify-content:space-between;align-items:center;border-bottom:2px solid #000;padding-bottom:8px;margin-bottom:12px">
      <h1 style="color:#d32;">Drive'N'Cook Admin</h1>
      <div>
        <span style="margin-right:8px">{{ status }}</span>
        <button @click="logout" style="background:#000;color:#fff;border:none;padding:6px 10px;border-radius:4px">Logout</button>
      </div>
    </header>
    <section style="display:flex;gap:12px;align-items:center;margin-bottom:12px">
      <input v-model="email" placeholder="email" style="padding:6px;border:1px solid #ccc;border-radius:4px"/>
      <input v-model="password" type="password" placeholder="password" style="padding:6px;border:1px solid #ccc;border-radius:4px"/>
      <button @click="login" style="background:#ffcc00;border:none;padding:6px 10px;border-radius:4px">Se connecter</button>
      <span style="color:#c00">{{ error }}</span>
    </section>
    <h2 style="color:#222;border-left:4px solid #ffcc00;padding-left:8px">Camions</h2>
    <ul style="list-style:none;padding:0">
      <li v-for="t in trucks" :key="t.id" style="padding:8px;border-bottom:1px solid #eee;display:flex;justify-content:space-between">
        <span><b style="color:#d32">{{ t.plate }}</b> — statut: {{ t.status }} — franchisé: {{ t.franchisee_id }}</span>
      </li>
    </ul>
  </main>`
}

createApp(App).mount('#app')


