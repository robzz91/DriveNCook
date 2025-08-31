import { createApp, ref, onMounted } from 'vue/dist/vue.esm-bundler.js'
import { api } from '../shared/api.js'

const App = {
  setup() {
    const email = ref('franchisee@example.com')
    const password = ref('password')
    const status = ref('Non connecté')
    const error = ref('')
    const mySales = ref([])

    async function login() {
      error.value = ''
      try { await api.login(email.value, password.value); status.value='Connecté'; await load() } catch (e) { error.value = e.message }
    }
    async function logout() { try { await api.logout(); status.value='Déconnecté'; mySales.value=[] } catch (e) { error.value = e.message } }
    async function load() {
      try { mySales.value = (await api.get('/sales')).data || [] } catch (e) { error.value = e.message }
    }
    onMounted(()=>{ if (api.getToken()) { status.value='Connecté'; load() }})
    return { email, password, status, error, mySales, login, logout }
  },
  template:`
  <main style="font-family:sans-serif;padding:16px;background:#fff">
    <header style="display:flex;justify-content:space-between;align-items:center;border-bottom:2px solid #000;padding-bottom:8px;margin-bottom:12px">
      <h1 style="color:#d32;">Drive'N'Cook Franchisé</h1>
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
    <h2 style="color:#222;border-left:4px solid #ffcc00;padding-left:8px">Mes ventes</h2>
    <ul style="list-style:none;padding:0">
      <li v-for="s in mySales" :key="s.id" style="padding:8px;border-bottom:1px solid #eee;display:flex;justify-content:space-between">
        <span><b style="color:#d32">{{ new Date(s.sold_at).toLocaleString() }}</b> — {{ Number(s.amount).toFixed(2) }} €</span>
      </li>
    </ul>
  </main>`
}

createApp(App).mount('#app')


