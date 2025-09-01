<template>
  <nav class="navbar">
    <div class="navbar-left">
      <router-link to="/" class="logo">DriveNCook</router-link>

      <router-link v-if="isAuthenticated" to="/clients">Clients</router-link>
      <router-link v-if="isAuthenticated" to="/plats">Plats</router-link>
      <router-link v-if="isAuthenticated" to="/commandes">Commandes</router-link>
      <router-link v-if="isAuthenticated" to="/evenements">Événements</router-link>
    </div>

    <div class="navbar-right">
      <template v-if="!isAuthenticated">
        <router-link to="/login" class="btn">Se connecter</router-link>
        <router-link to="/register" class="btn btn-secondary">S'inscrire</router-link>
      </template>

      <template v-else>
        <span class="user-info">👤 {{ user?.nom || user?.name || "Utilisateur" }}</span>
        <button class="btn btn-logout" @click="logout">Se déconnecter</button>
      </template>
    </div>
  </nav>
</template>

<script setup>
import { useAuthStore } from "@/stores/authStore";
import { storeToRefs } from "pinia";

const auth = useAuthStore();
const { isAuthenticated, user } = storeToRefs(auth);

function logout() {
  auth.logout();
}
</script>

<style scoped>
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 24px;
  background: #222;
  color: white;
}

.navbar a {
  color: white;
  margin-right: 16px;
  text-decoration: none;
  font-weight: 500;
}

.navbar a:hover {
  text-decoration: underline;
}

.logo {
  font-size: 20px;
  font-weight: bold;
  color: #f39c12;
}

.navbar-right {
  display: flex;
  align-items: center;
}

.user-info {
  margin-right: 12px;
  font-size: 14px;
  color: #f0f0f0;
}

.btn {
  background: #f39c12;
  border: none;
  padding: 6px 14px;
  border-radius: 4px;
  margin-left: 6px;
  cursor: pointer;
  color: white;
  font-weight: 500;
}

.btn:hover {
  background: #e67e22;
}

.btn-secondary {
  background: #3498db;
}

.btn-secondary:hover {
  background: #2980b9;
}

.btn-logout {
  background: #e74c3c;
}

.btn-logout:hover {
  background: #c0392b;
}
</style>
