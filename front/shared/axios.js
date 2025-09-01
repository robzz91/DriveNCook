import axios from 'axios'

const API_URL = (import.meta?.env?.VITE_API_URL) || 'http://localhost:8000/api'

const http = axios.create({ baseURL: API_URL })

http.interceptors.request.use((config) => {
  try {
    const token = localStorage.getItem('token')
    const path = (config.url || '')
    // Ne pas injecter le token pour login/register publics
    const isPublicAuth = path.endsWith('/login.php') || path.endsWith('/register.php')
    if (token && !isPublicAuth) config.headers.Authorization = `Bearer ${token}`
  } catch {}
  return config
})

export default http


