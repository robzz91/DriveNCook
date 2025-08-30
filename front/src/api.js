const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

async function request(method, path, body) {
  const res = await fetch(`${API_URL}${path}`, {
    method,
    headers: { 'Content-Type': 'application/json' },
    body: body ? JSON.stringify(body) : undefined,
  });
  const data = await res.json().catch(() => null);
  if (!res.ok) {
    const status = res.status;
    const message = data && data.message ? data.message : `HTTP ${status}`;
    throw new Error(message);
  }
  return { data };
}

const api = {
  get: (path) => request('GET', path),
  post: (path, body) => request('POST', path, body),
  delete: (path) => request('DELETE', path),
};

export default api;
