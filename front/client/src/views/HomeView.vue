<template>
  <div class="home-container">
    <div v-if="auth.isAuthenticated()" class="welcome-box">
      <h1>Bienvenue, {{ auth.user?.nom || "Cher client" }} 👋</h1>
      <p>Vous êtes bien connecté à votre espace client.</p>
      <button @click="logout">Se déconnecter</button>
    </div>

    <div v-else class="guest-box">
      <h1>Bienvenue sur DrivnCook 🚚🍔</h1>
      <p>Veuillez vous connecter ou créer un compte pour accéder à votre espace client.</p>
      <router-link class="btn" to="/login">Se connecter</router-link>
      <router-link class="btn secondary" to="/register">Créer un compte</router-link>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from "@/stores/authStore";
import { useRouter } from "vue-router";

const auth = useAuthStore();
const router = useRouter();

const logout = async () => {
  await auth.logout();
  router.push("/login");
};
</script>

<style scoped>
.home-container {
  max-width: 600px;
  margin: 50px auto;
  text-align: center;
  background: #fff;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
}

.welcome-box h1 {
  color: #2d89ef;
}

.guest-box h1 {
  color: #333;
}

p {
  margin: 15px 0;
  font-size: 16px;
}

.btn {
  display: inline-block;
  margin: 10px;
  padding: 10px 20px;
  background: #2d89ef;
  color: white;
  border-radius: 5px;
  text-decoration: none;
  transition: background 0.3s;
}

.btn:hover {
  background: #1c5db5;
}

.btn.secondary {
  background: #28a745;
}

.btn.secondary:hover {
  background: #1e7e34;
}

button {
  margin-top: 20px;
  padding: 10px 20px;
  background: #dc3545;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

button:hover {
  background: #a71d2a;
}
</style>
