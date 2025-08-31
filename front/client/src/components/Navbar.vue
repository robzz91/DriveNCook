<template>
  <nav class="navbar">
    <router-link to="/">Accueil</router-link>
    <router-link v-if="!auth.isAuthenticated()" to="/login">Connexion</router-link>
    <router-link v-if="!auth.isAuthenticated()" to="/register">Inscription</router-link>
    <button v-if="auth.isAuthenticated()" @click="logout">Déconnexion</button>
  </nav>
</template>

<script setup>
import { useAuthStore } from "@/stores/authStore";

const auth = useAuthStore();

const logout = async () => {
  await auth.logout();
};
</script>

<style scoped>
.navbar {
  display: flex;
  gap: 20px;
  padding: 15px;
  background: #222;
  color: white;
}
a {
  color: white;
  text-decoration: none;
}
button {
  background: transparent;
  border: none;
  color: white;
  cursor: pointer;
}
</style>
