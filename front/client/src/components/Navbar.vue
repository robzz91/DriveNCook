<!-- client/src/components/Navbar.vue -->
<script setup>
import { useAuthStore } from "@/stores/authStore";
import { useRouter } from "vue-router";

const auth = useAuthStore();
const router = useRouter();

const logout = async () => {
  await auth.logout();
  router.replace({ name: "login" });
};
</script>

<template>
  <nav class="navbar">
    <div class="navbar-left">
      <RouterLink to="/" class="nav-logo">DriveNCook</RouterLink>
    </div>

    <div class="navbar-right">
      <RouterLink to="/" class="nav-link">🏠 Home</RouterLink>

      <template v-if="auth.isAuthenticated">
        <RouterLink to="/clients" class="nav-link">👤 Clients</RouterLink>
        <RouterLink to="/plats" class="nav-link">🍽️ Plats</RouterLink>
        <RouterLink to="/commandes" class="nav-link">🛒 Commandes</RouterLink>
        <RouterLink to="/evenements" class="nav-link">📅 Événements</RouterLink>
        <button @click="logout" class="btn-logout">🚪 Déconnexion</button>
      </template>

      <template v-else>
        <RouterLink to="/login" class="nav-link">🔑 Connexion</RouterLink>
      </template>
    </div>
  </nav>
</template>

<style scoped>
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #111827;
  color: #fff;
  padding: 12px 24px;
}
.nav-logo {
  font-weight: bold;
  font-size: 20px;
  color: #fff;
  text-decoration: none;
}
.navbar-right {
  display: flex;
  align-items: center;
  gap: 16px;
}
.nav-link {
  color: #fff;
  text-decoration: none;
  font-size: 14px;
}
.nav-link:hover {
  text-decoration: underline;
}
.btn-logout {
  background: #dc2626;
  border: none;
  color: #fff;
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
}
.btn-logout:hover {
  background: #b91c1c;
}
</style>
