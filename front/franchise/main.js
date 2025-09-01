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

    const sales = ref([])
    const trucks = ref([])
    const supplies = ref([])

    const totalSales = ref(0)

    async function logout(){ try{ await auth.logout(); location.href='./login.html' } catch(e){ error.value = e.message } }

    async function loadAll(){
      try{
        const [trRes, spRes, slRes] = await Promise.all([
          http.get('/trucks'),
          http.get('/supplies'),
          http.get('/sales')
        ])
        trucks.value = trRes?.data?.data || []
        supplies.value = spRes?.data?.data || []
        sales.value = slRes?.data?.data || []
        totalSales.value = sales.value.reduce((sum,s)=>sum+Number(s.amount||0),0)
      }catch(e){ error.value = e.message }
    }

    onMounted(async ()=>{
      hasToken.value = !!(window && window.localStorage && window.localStorage.getItem('token'))
      if (!hasToken.value) { location.href='./login.html'; return }
      await auth.me();
      status.value = `Connecté (${auth.role})`
      allowed.value = auth.role === 'franchisee'
      if (!allowed.value){ error.value='Accès interdit (franchisé requis)'; setTimeout(()=>location.href='./login.html', 600); return }
      await loadAll()
    })

    return { status, error, allowed, hasToken, logout, sales, trucks, supplies, totalSales }
  },
  template:`
  <main style="font-family:sans-serif;padding:16px;background:#fff;min-height:100vh">
    <header style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;margin:-16px -16px 16px -16px;background:#111;color:#fff;border-bottom:3px solid #ffcc00">
      <h1 style="color:#ffcc00;margin:0">Drive'N'Cook — Espace Franchisé</h1>
      <nav style="display:flex;gap:10px;align-items:center">
        <a href="./index.html" style="background:#222;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none;border:1px solid #333">Accueil</a>
        <a href="./trucks.html" style="background:#222;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none;border:1px solid #333">Camions</a>
        <a href="./supplies.html" style="background:#222;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none;border:1px solid #333">Approvisionnements</a>
        <a href="./sales.html" style="background:#222;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none;border:1px solid #333">Ventes</a>
      </nav>
      <div style="display:flex;gap:8px;align-items:center">
        <span style="margin-right:8px">{{ status }}</span>
        <button @click="logout" style="background:#ff3b3b;color:#fff;border:none;padding:6px 10px;border-radius:6px">Déconnexion</button>
      </div>
    </header>

    <div v-if="allowed" style="display:grid;grid-template-columns:repeat(2, minmax(220px,1fr));gap:16px;max-width:920px;margin:0 auto">
      <div style="background:#f1f1f1;border:2px solid #000;border-radius:12px;padding:16px;aspect-ratio:1/1;display:flex;flex-direction:column;justify-content:center;align-items:flex-start">
        <div style="font-size:14px;color:#333;margin-bottom:6px">Mon chiffre d'affaires</div>
        <div style="font-size:36px;font-weight:800;color:#000">{{ new Intl.NumberFormat('fr-FR', { style:'currency', currency:'EUR' }).format(totalSales) }}</div>
        <div style="font-size:12px;color:#333">Somme des ventes</div>
      </div>
      <div style="background:#ffe5e5;border:2px solid #ff3b3b;border-radius:12px;padding:16px;aspect-ratio:1/1;display:flex;flex-direction:column;justify-content:center;align-items:flex-start">
        <div style="font-size:14px;color:#b30000;margin-bottom:6px">Mes camions</div>
        <div style="font-size:36px;font-weight:800;color:#000">{{ trucks.length }}</div>
        <div style="font-size:12px;color:#b30000">Total camions</div>
      </div>
      <div style="background:#fff3cd;border:2px solid #ffcc00;border-radius:12px;padding:16px;aspect-ratio:1/1;display:flex;flex-direction:column;justify-content:center;align-items:flex-start">
        <div style="font-size:14px;color:#a88400;margin-bottom:6px">Mes approvisionnements</div>
        <div style="font-size:36px;font-weight:800;color:#000">{{ supplies.length }}</div>
        <div style="font-size:12px;color:#a88400">Total approvisionnements</div>
      </div>
      <div style="background:#e6f4ff;border:2px solid #1e88e5;border-radius:12px;padding:16px;aspect-ratio:1/1;display:flex;flex-direction:column;justify-content:center;align-items:flex-start">
        <div style="font-size:14px;color:#0b60b0;margin-bottom:6px">Mes ventes</div>
        <div style="font-size:36px;font-weight:800;color:#000">{{ sales.length }}</div>
        <div style="font-size:12px;color:#0b60b0">Total ventes</div>
      </div>
    </div>

    <div v-else-if="hasToken" style="margin-top:8px;color:#c00">Accès interdit (franchisé requis).</div>
  </main>`
}

const app = createApp(App)
app.use(createPinia())
app.mount('#app')


