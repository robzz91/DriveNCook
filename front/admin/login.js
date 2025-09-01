import http from '../shared/axios.js'

const email = document.getElementById('email')
const password = document.getElementById('password')
const err = document.getElementById('err')
const btn = document.getElementById('loginBtn')

btn.addEventListener('click', async () => {
  err.textContent = ''
  try {
    const { data } = await http.post('/login.php', { email: email.value, password: password.value })
    const token = data?.data?.token
    if (!token) throw new Error('Token manquant')
    localStorage.setItem('token', token)
    // Vérifier role
    const me = await http.get('/me.php')
    if ((me?.data?.data?.role || '') !== 'admin') { throw new Error('Accès admin requis') }
    location.href = './index.html'
  } catch (e) {
    const status = e?.response?.status || 0
    err.textContent = status === 401 ? 'Identifiants invalides' : 'Erreur'
  }
})


