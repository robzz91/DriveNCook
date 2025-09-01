import { createApp, ref, onMounted } from 'vue/dist/vue.esm-bundler.js'
import { api } from '../shared/api.js'
import { createPinia } from 'pinia'
import { useAuthStore } from '../shared/authStore.js'

const App = {
  setup() {
    const pinia = createPinia()
    const auth = useAuthStore(pinia)
    auth.load()

    const status = ref('Non connecté')
    const error = ref('')
    const trucks = ref([])
    const allowed = ref(false)

    async function logout() {
      error.value = ''
      try { await auth.logout(); status.value = 'Déconnecté'; trucks.value = []; allowed.value = false; location.href = './login.html' } catch (e) { error.value = e.message }
    }

    async function load() {
      try { trucks.value = (await api.get('/trucks')).data || [] } catch (e) { error.value = e.message }
    }

    onMounted(async () => {
      if (!api.getToken()) {
        location.href = './login.html'
        return
      }
      if (api.getToken()) {
        await auth.me();
        status.value = `Connecté (${auth.role})`;
        allowed.value = auth.role === 'admin';
        if (allowed.value) { await load() } else { error.value = 'Accès interdit (admin requis)'; setTimeout(()=>location.href='./login.html', 600) }
      }
    })

    return { status, error, trucks, logout, allowed }
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
    <div v-if="allowed">
      <h2 style="color:#222;border-left:4px solid #ffcc00;padding-left:8px">Camions</h2>
      <ul style="list-style:none;padding:0">
        <li v-for="t in trucks" :key="t.id" style="padding:8px;border-bottom:1px solid #eee;display:flex;justify-content:space-between">
          <span><b style="color:#d32">{{ t.plate }}</b> — statut: {{ t.status }} — franchisé: {{ t.franchisee_id }}</span>
        </li>
      </ul>
    </div>
    <div v-else-if="api && api.getToken()" style="margin-top:8px;color:#c00">Accès interdit (admin requis).</div>
  </main>`
}

const app = createApp(App)
app.use(createPinia())
app.mount('#app')


