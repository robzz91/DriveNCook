// client/src/api.js
import axios from "axios";

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || "http://localhost:8000/api",
  timeout: 15000,
});

// Injecte automatiquement le Bearer token
api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Logging + gestion de 401
api.interceptors.response.use(
  (res) => res,
  (err) => {
    const status = err.response?.status;
    const payload = err.response?.data;
    const msg = payload?.message || err.message || "Erreur";
    console.error("[API ERROR]", status === 401 ? "Non authentifié" : msg, payload || err);

    // Option (décommenter si tu veux auto-redirect vers login) :
    // if (status === 401) {
    //   localStorage.removeItem("token");
    //   window.location.href = "/login";
    // }
    return Promise.reject(err);
  }
);

export default api;
