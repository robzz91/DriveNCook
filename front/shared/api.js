const API_URL = (import.meta?.env?.VITE_API_URL) || 'http://localhost:8000/api';

function getToken() {
  try { return localStorage.getItem('token') || ''; } catch { return ''; }
}

function setToken(token) {
  try { localStorage.setItem('token', token || ''); } catch {}
}

async function request(method, path, body, opts = {}) {
  const headers = { 'Content-Type': 'application/json', ...(opts.headers || {}) };
  const token = getToken();
  if (token) headers['Authorization'] = `Bearer ${token}`;
  const res = await fetch(`${API_URL}${path}`, {
    method,
    headers,
    body: body ? JSON.stringify(body) : undefined,
  });
  if (opts.raw) return res;
  const data = await res.json().catch(() => null);
  if (!res.ok) {
    const message = data && (data.message || data.error) ? (data.message || data.error) : `HTTP ${res.status}`;
    throw new Error(message);
  }
  return data;
}

function parseFilenameFromContentDisposition(disposition){
  if (!disposition) return ''
  const m = /filename\*=UTF-8''([^;]+)|filename="?([^";]+)"?/i.exec(disposition)
  return decodeURIComponent(m?.[1] || m?.[2] || '')
}

export const api = {
  login: async (email, password) => {
    const res = await request('POST', '/login.php', { email, password });
    const token = res?.data?.token;
    if (token) setToken(token);
    return res;
  },
  logout: async () => {
    const res = await request('POST', '/logout.php');
    setToken('');
    return res;
  },
  get: (path) => request('GET', path),
  post: (path, body) => request('POST', path, body),
  put: (path, body) => request('PUT', path, body),
  del: (path) => request('DELETE', path),
  fetchPdf: async (path) => {
    const token = getToken();
    const res = await fetch(`${API_URL}${path}`, { headers: { Authorization: `Bearer ${token}` } });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const blob = await res.blob();
    const disposition = res.headers.get('Content-Disposition') || ''
    const filename = parseFilenameFromContentDisposition(disposition)
    return { blob, filename };
  },
  getToken,
  setToken,
};


