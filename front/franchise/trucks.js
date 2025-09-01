import { createApp, ref, onMounted, computed } from 'vue/dist/vue.esm-bundler.js'
import { createPinia } from 'pinia'
import { useAuthStore } from '../shared/authStore.js'
import http from '../shared/axios.js'
import { api } from '../shared/api.js'

const App = {
  setup(){
    const pinia = createPinia()
    const auth = useAuthStore(pinia)
    auth.load()

    const err = ref('')
    const items = ref([])
    const q = ref('')

    const filtered = computed(()=> (items.value||[]).filter(t=> (t.plate||'').toLowerCase().includes(q.value.toLowerCase())))

    async function ensureAuth(){ if(!api.getToken()){ location.href='./login.html'; return false } await auth.me(); if(auth.role!=='franchisee'){ location.href='./login.html'; return false } return true }

    onMounted(async ()=>{
      if(!(await ensureAuth())) return
      try{ const { data } = await http.get('/trucks'); items.value = data?.data || [] } catch(e){ err.value=e.message }
      const s = document.getElementById('tSearch'); if (s) s.addEventListener('input',(e)=>{ q.value = e.target.value })
    })

    return { err, filtered }
  },
  template:`
    <tr v-for="t in filtered" :key="t.id">
      <td>{{ t.plate }}</td>
      <td>{{ t.status }}</td>
      <td>{{ t.last_service_at }}</td>
    </tr>
  `
}

createApp(App).use(createPinia()).mount('#rows')
