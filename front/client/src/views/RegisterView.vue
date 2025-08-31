<template>
  <div class="auth-container">
    <h1>Inscription</h1>
    <form @submit.prevent="handleRegister">
      <input v-model="nom" type="text" placeholder="Nom complet" required />
      <input v-model="email" type="email" placeholder="Email" required />
      <input v-model="password" type="password" placeholder="Mot de passe" required />
      <button type="submit">S'inscrire</button>
    </form>
    <p>Déjà inscrit ? <router-link to="/login">Connexion</router-link></p>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useAuthStore } from "@/stores/authStore";
import { useRouter } from "vue-router";

const nom = ref("");
const email = ref("");
const password = ref("");
const authStore = useAuthStore();
const router = useRouter();

const handleRegister = async () => {
  try {
    await authStore.register(nom.value, email.value, password.value);
    router.push("/");
  } catch (e) {
    alert("Erreur inscription");
  }
};
</script>

<style scoped>
.auth-container {
  max-width: 400px;
  margin: 50px auto;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 8px;
  background: #fff;
}
input {
  display: block;
  width: 100%;
  margin-bottom: 15px;
  padding: 10px;
}
button {
  width: 100%;
  padding: 10px;
  background: #2d89ef;
  color: white;
  border: none;
  border-radius: 5px;
}
</style>
