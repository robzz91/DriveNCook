<!-- client/src/views/LoginView.vue -->
<template>
  <div class="auth-wrap">
    <div class="card">
      <h1>Connexion</h1>

      <form @submit.prevent="onSubmit" class="form">
        <label>
          Email
          <input
            v-model.trim="email"
            type="email"
            placeholder="ex: john@doe.com"
            autocomplete="email"
            required
          />
        </label>

        <label>
          Mot de passe
          <input
            v-model="password"
            type="password"
            placeholder="Votre mot de passe"
            autocomplete="current-password"
            required
          />
        </label>

        <button class="btn" :disabled="loading">
          <span v-if="!loading">Se connecter</span>
          <span v-else>Connexion…</span>
        </button>

        <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
      </form>

      <p class="hint">
        Pas encore de compte ?
        <RouterLink to="/register">Créer un compte</RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import api, { setToken } from "@/api";

const router = useRouter();

const email = ref("");
const password = ref("");
const loading = ref(false);
const errorMessage = ref("");

async function onSubmit() {
  errorMessage.value = "";
  loading.value = true;

  try {
    // ⚠️ adapte l'URL si ton endpoint est différent
    const payload = { email: email.value, password: password.value };
    const { data } = await api.post("/login.php", payload);

    // Formats possibles selon ton API:
    // { token, user }  ou  { data: { token, user } }  ou  { access_token, user }
    const token =
      data?.token || data?.data?.token || data?.access_token || null;
    const user = data?.user || data?.data?.user || null;

    if (!token) {
      throw new Error("Réponse API inattendue : token manquant.");
    }

    // Sauvegarde côté client (localStorage)
    setToken(token, user);

    // Redirige vers la page demandée avant 401 ou vers un défaut
    const redirect = sessionStorage.getItem("redirect_after_login") || "/clients";
    sessionStorage.removeItem("redirect_after_login");
    router.replace(redirect);
  } catch (err) {
    const apiMessage =
      err?.response?.data?.message ||
      err?.response?.data?.error ||
      err.message ||
      "Erreur inconnue";
    errorMessage.value = apiMessage;
    console.error("[LOGIN] Error:", err);
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.auth-wrap {
  min-height: calc(100vh - 64px);
  display: grid;
  place-items: center;
  padding: 2rem;
  background: #0f1217;
}

.card {
  width: 100%;
  max-width: 420px;
  background: #141a22;
  border: 1px solid #232b36;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 10px 30px rgba(0,0,0,.35);
  color: #e7edf6;
}

h1 {
  margin: 0 0 1rem;
  font-size: 1.6rem;
  font-weight: 700;
}

.form {
  display: grid;
  gap: 1rem;
  margin-top: .5rem;
}

label {
  display: grid;
  gap: .5rem;
  font-size: .95rem;
}

input {
  height: 44px;
  border-radius: 10px;
  border: 1px solid #2a3442;
  background: #0c1117;
  color: #e7edf6;
  padding: 0 .9rem;
  outline: none;
}

input::placeholder {
  color: #687386;
}

.btn {
  height: 44px;
  border: none;
  border-radius: 10px;
  background: #ffc107;
  color: #111;
  font-weight: 700;
  cursor: pointer;
}

.btn[disabled] {
  opacity: .65;
  cursor: not-allowed;
}

.error {
  color: #ff7777;
  margin: .25rem 0 0;
  font-size: .9rem;
}

.hint {
  margin-top: 1rem;
  font-size: .95rem;
  color: #9fb1c7;
}
</style>
