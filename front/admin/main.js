import { createApp, ref, onMounted } from 'vue/dist/vue.esm-bundler.js'
import { createPinia } from 'pinia'
import { useAuthStore } from '../shared/authStore.js'
import http from '../shared/axios.js'

const App = {
  setup() {
    const pinia = createPinia()
    const auth = useAuthStore(pinia)
    auth.load()

    const status = ref('Non connecté')
    const error = ref('')
    const allowed = ref(false)
    const hasToken = ref(false)

    const clientsCount = ref(0)
    const trucksActiveCount = ref(0)
    const trucksMaintCount = ref(0)
    const salesTotal = ref(0)

    async function logout() {
      error.value = ''
      try {
        await auth.logout()
        status.value = 'Déconnecté'
        allowed.value = false
        location.href = './login.html'
      } catch (e) { error.value = e.message }
    }

    async function loadStats() {
      try {
        const [usersRes, trucksRes, salesRes] = await Promise.all([
          http.get('/users', { params: { role: 'client' } }),
          http.get('/trucks'),
          http.get('/sales')
        ])
        const users = usersRes?.data?.data || usersRes?.data || []
        const trucks = trucksRes?.data?.data || trucksRes?.data || []
        const sales = salesRes?.data?.data || salesRes?.data || []

        clientsCount.value = Array.isArray(users) ? users.length : 0
        trucksActiveCount.value = Array.isArray(trucks) ? trucks.filter(t => (t.status||'').toLowerCase()==='active').length : 0
        trucksMaintCount.value = Array.isArray(trucks) ? trucks.filter(t => (t.status||'').toLowerCase()==='maintenance').length : 0
        salesTotal.value = Array.isArray(sales) ? sales.reduce((sum, s) => sum + Number(s.amount||0), 0) : 0
      } catch (e) {
        error.value = e.message
      }
    }

    onMounted(async () => {
      hasToken.value = !!window.localStorage.getItem('token')
      if (!hasToken.value) { location.href = './login.html'; return }
      await auth.me()
      status.value = `Connecté (${auth.role})`
      allowed.value = auth.role === 'admin'
      if (!allowed.value) { error.value = 'Accès interdit (admin requis)'; setTimeout(()=>location.href='./login.html', 600); return }
      await loadStats()
    })

    function formatCurrency(n){ try{ return new Intl.NumberFormat('fr-FR', { style:'currency', currency:'EUR' }).format(n||0) } catch { return (n||0).toFixed(2)+' €' } }

    return { status, error, logout, allowed, hasToken, clientsCount, trucksActiveCount, trucksMaintCount, salesTotal, formatCurrency }
  },
  template: `
  <main style="font-family:sans-serif;padding:16px;background:#fff;min-height:100vh">
    <header style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;margin:-16px -16px 16px -16px;background:#111;color:#fff;border-bottom:3px solid #ffcc00">
      <h1 style="color:#ffcc00;margin:0">Drive'N'Cook — Administration</h1>
      <nav style="display:flex;gap:10px;align-items:center">
        <a href="./index.html" style="background:#222;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none;border:1px solid #333">Accueil</a>
        <a href="./users.html" style="background:#222;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none;border:1px solid #333">Utilisateurs</a>
        <a href="./franchisees.html" style="background:#222;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none;border:1px solid #333">Franchisés</a>
        <a href="./warehouses.html" style="background:#222;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none;border:1px solid #333">Entrepôts</a>
        <a href="./trucks.html" style="background:#222;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none;border:1px solid #333">Camions</a>
        <a href="./supplies.html" style="background:#222;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none;border:1px solid #333">Approvisionnements</a>
        <a href="./sales.html" style="background:#222;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none;border:1px solid #333">Ventes</a>
      </nav>
      <div style="display:flex;gap:8px;align-items:center">
        <span style="margin-right:8px">{{ status }}</span>
        <button @click="logout" style="background:#ff3b3b;color:#fff;border:none;padding:6px 10px;border-radius:6px">Déconnexion</button>
      </div>
    </header>

    <div v-if="allowed" style="display:grid;grid-template-columns:repeat(2, minmax(220px,1fr));gap:16px">
      <a href="./users.html?role=client" style="text-decoration:none">
        <div style="background:#fff3cd;border:2px solid #ffcc00;border-radius:12px;padding:16px;box-shadow:0 2px 6px rgba(0,0,0,0.08);color:#222;cursor:pointer;aspect-ratio:1/1;display:flex;flex-direction:column;justify-content:center;align-items:flex-start">
          <div style="font-size:14px;color:#a88400;margin-bottom:6px">Clients</div>
          <div style="font-size:36px;font-weight:800;color:#000">{{ clientsCount }}</div>
          <div style="font-size:12px;color:#a88400">Total des clients</div>
        </div>
      </a>

      <a href="./trucks.html" style="text-decoration:none">
        <div style="background:#ffe5e5;border:2px solid #ff3b3b;border-radius:12px;padding:16px;box-shadow:0 2px 6px rgba(0,0,0,0.08);color:#222;cursor:pointer;aspect-ratio:1/1;display:flex;flex-direction:column;justify-content:center;align-items:flex-start">
          <div style="font-size:14px;color:#b30000;margin-bottom:6px">Camions actifs</div>
          <div style="font-size:36px;font-weight:800;color:#000">{{ trucksActiveCount }}</div>
          <div style="font-size:12px;color:#b30000">Statut = actif</div>
        </div>
      </a>

      <a href="./trucks.html?status=maintenance" style="text-decoration:none">
        <div style="background:#e6f4ff;border:2px solid #1e88e5;border-radius:12px;padding:16px;box-shadow:0 2px 6px rgba(0,0,0,0.08);color:#222;cursor:pointer;aspect-ratio:1/1;display:flex;flex-direction:column;justify-content:center;align-items:flex-start">
          <div style="font-size:14px;color:#0b60b0;margin-bottom:6px">Camions en maintenance</div>
          <div style="font-size:36px;font-weight:800;color:#000">{{ trucksMaintCount }}</div>
          <div style="font-size:12px;color:#0b60b0">Statut = maintenance</div>
        </div>
      </a>

      <a href="./sales.html" style="text-decoration:none">
        <div style="background:#f1f1f1;border:2px solid #000;border-radius:12px;padding:16px;box-shadow:0 2px 6px rgba(0,0,0,0.08);color:#222;cursor:pointer;aspect-ratio:1/1;display:flex;flex-direction:column;justify-content:center;align-items:flex-start">
          <div style="font-size:14px;color:#333;margin-bottom:6px">Chiffre d'affaires (toutes franchises)</div>
          <div style="font-size:36px;font-weight:800;color:#000">{{ formatCurrency(salesTotal) }}</div>
          <div style="font-size:12px;color:#333">Somme des ventes</div>
        </div>
      </a>
    </div>

    <div v-else-if="hasToken" style="margin-top:8px;color:#c00">Accès interdit (admin requis).</div>
    <div v-if="error" style="margin-top:16px;color:#c00">{{ error }}</div>
  </main>`
}

const app = createApp(App)
app.use(createPinia())
app.mount('#app')


