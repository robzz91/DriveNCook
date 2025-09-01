// client/src/api.js
import axios from "axios";

/**
 * Base URL lue depuis .env (VITE_API_URL), avec fallback local.
 * Exemple de .env :
 * VITE_API_URL=http://localhost:8000/api
 */
const baseURL =
  (import.meta.env.VITE_API_URL && import.meta.env.VITE_API_URL.replace(/\/$/, "")) ||
  "http://localhost:8000/api";

// Clé unique pour le token dans le stockage local
const TOKEN_KEY = "token";

/**
 * Instance Axios centralisée
 */
const api = axios.create({
  baseURL,
  withCredentials: false, // API PHP simple -> pas besoin de cookies cross-domain
  timeout: 15000,
  headers: {
    "X-Requested-With": "XMLHttpRequest",
    Accept: "application/json",
    "Content-Type": "application/json",
  },
});

/**
 * Récupère le token depuis localStorage
 */
function getStoredToken() {
  // on garde la compat avec d’anciens noms éventuels
  return (
    localStorage.getItem(TOKEN_KEY) ||
    localStorage.getItem("auth_token") ||
    sessionStorage.getItem(TOKEN_KEY) ||
    ""
  );
}

/**
 * Intercepteur requêtes : ajoute Authorization: Bearer <token>
 */
api.interceptors.request.use(
  (config) => {
    const token = getStoredToken();
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

/**
 * Intercepteur réponses : journalise & gère les 401
 */
api.interceptors.response.use(
  (response) => response,
  (error) => {
    // Normalise un message lisible
    const status = error?.response?.status;
    const apiMessage =
      error?.response?.data?.message ||
      error?.response?.data?.error ||
      error.message ||
      "Erreur inconnue";

    if (status === 401) {
      console.warn("[API] 401 Non authentifié -> nettoyage du token et redirection /login");
      clearToken();
      // On stocke le chemin courant pour rediriger après login
      sessionStorage.setItem("redirect_after_login", window.location.pathname);
      // Redirection côté client (évite d'avoir besoin du router ici)
      if (window.location.pathname !== "/login") {
        window.location.href = "/login";
      }
    } else if (status >= 500) {
      console.error("[API ERROR] Erreur serveur", error.response?.data || error);
    } else {
      console.warn("[API ERROR]", apiMessage);
    }

    return Promise.reject(error);
  }
);

/* ---------- Helpers d’auth centralisés ---------- */

/** Sauvegarde un token (et optionnellement le user) */
export function setToken(token, user = null) {
  if (token) localStorage.setItem(TOKEN_KEY, token);
  if (user) localStorage.setItem("user", JSON.stringify(user));
}

/** Supprime tout ce qui est auth côté client */
export function clearToken() {
  localStorage.removeItem(TOKEN_KEY);
  localStorage.removeItem("user");
}

/** Récupère le user si présent */
export function getUser() {
  try {
    const raw = localStorage.getItem("user");
    return raw ? JSON.parse(raw) : null;
  } catch {
    return null;
  }
}

/** Expose l’instance axios */
export default api;
