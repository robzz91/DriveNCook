import { defineStore } from 'pinia'
import http from './axios.js'

export const useAuthStore = defineStore('auth', {
  state: () => ({ token: '', role: null, user: null }),
  actions: {
    load() { try { this.token = localStorage.getItem('token') || '' } catch { this.token='' } },
    save(token) { this.token = token || ''; try { localStorage.setItem('token', this.token) } catch {} },
    async login(email, password) {
      const { data } = await http.post('/login.php', { email, password })
      const token = data?.data?.token
      if (!token) throw new Error('Token manquant')
      this.save(token)
      await this.me()
    },
    async me() {
      const { data } = await http.get('/me.php')
      this.user = data?.data || null
      this.role = this.user?.role || null
      return this.user
    },
    async logout() {
      try { await http.post('/logout.php') } catch {}
      this.save(''); this.role=null; this.user=null
    },
  }
})


