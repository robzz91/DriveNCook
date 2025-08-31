<!-- client/src/views/LoginView.vue -->
<template>
  <div class="page page-auth">
    <div class="auth-card">
      <h1>Connexion</h1>

      <form @submit.prevent="onSubmit">
        <label>
          Email
          <input v-model="email" type="email" required placeholder="email@example.com" />
        </label>

        <label>
          Mot de passe
          <input v-model="password" type="password" required placeholder="••••••••" />
        </label>

        <button :disabled="loading" class="btn primary">
          {{ loading ? "Connexion..." : "Se connecter" }}
        </button>

        <p v-if="error" class="error">{{ error }}</p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "@/stores/authStore";

const auth = useAuthStore();
const router = useRouter();
const route = useRoute();

const email = ref("");
const password = ref("");

const loading = computed(() => auth.loading);
const error = computed(() => auth.error);

const onSubmit = async () => {
  const ok = await auth.login(email.value, password.value);
  if (ok) {
    const redirect = route.query.redirect || "/clients";
    router.replace(redirect);
  }
};
</script>

<style scoped>
.page-auth { display:flex; justify-content:center; align-items:center; min-height:60vh; }
.auth-card { width: 100%; max-width: 380px; border:1px solid #eee; border-radius:12px; padding:24px; background:#fff; box-shadow:0 8px 24px rgba(0,0,0,.06); }
h1 { margin:0 0 16px; font-size:20px; }
form { display:flex; flex-direction:column; gap:12px; }
label { display:flex; flex-direction:column; gap:6px; font-size:14px; }
input { padding:10px 12px; border:1px solid #ddd; border-radius:8px; }
.btn.primary { background:#111827; color:#fff; border:none; padding:10px 14px; border-radius:8px; cursor:pointer; }
.btn.primary:disabled { opacity:.7; cursor:not-allowed; }
.error { color:#c62828; margin-top:8px; }
</style>
