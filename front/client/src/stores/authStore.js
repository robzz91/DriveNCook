// client/src/store/authStore.js
import { defineStore } from "pinia";
import { ref, computed } from "vue";
import api, { setToken, clearToken, getUser } from "@/api";

export const useAuthStore = defineStore("auth", () => {
  const token = ref(localStorage.getItem("token") || null);
  const user = ref(getUser());

  const isAuthenticated = computed(() => !!token.value);

  async function login(email, password) {
    try {
      const { data } = await api.post("/login.php", { email, password });
      const t =
        data?.token || data?.access_token || data?.data?.token || null;
      const u = data?.user || data?.data?.user || null;

      if (!t) throw new Error("Token manquant dans la réponse API.");

      setToken(t, u);
      token.value = t;
      user.value = u;
      return true;
    } catch (err) {
      console.error("[AUTH] login failed", err);
      throw err;
    }
  }

  async function register(payload) {
    try {
      const { data } = await api.post("/register.php", payload);
      const t =
        data?.token || data?.access_token || data?.data?.token || null;
      const u = data?.user || data?.data?.user || null;

      if (t) {
        setToken(t, u);
        token.value = t;
        user.value = u;
      }
      return true;
    } catch (err) {
      console.error("[AUTH] register failed", err);
      throw err;
    }
  }

  function logout() {
    clearToken();
    token.value = null;
    user.value = null;
  }

  return {
    token,
    user,
    isAuthenticated,
    login,
    register,
    logout,
  };
});
