<!-- client/src/views/HomeView.vue -->
<template>
  <div class="page home">
    <h1>Bienvenue sur DriveNCook</h1>
    <p>Votre interface de gestion des clients, plats, commandes et événements.</p>

    <div class="cards">
      <RouterLink to="/clients" class="card">👤 Gérer les Clients</RouterLink>
      <RouterLink to="/plats" class="card">🍽️ Gérer les Plats</RouterLink>
      <RouterLink to="/commandes" class="card">🛒 Gérer les Commandes</RouterLink>
      <RouterLink to="/evenements" class="card">📅 Gérer les Événements</RouterLink>
    </div>
  </div>
</template>

<script setup>
// aucun script pour l'instant
</script>

<style scoped>
.page.home {
  text-align: center;
  padding: 40px;
}
.cards {
  margin-top: 40px;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}
.card {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 120px;
  background: #f9fafb;
  border-radius: 12px;
  border: 1px solid #ddd;
  text-decoration: none;
  color: #111827;
  font-size: 16px;
  font-weight: 500;
  transition: all 0.2s;
}
.card:hover {
  background: #111827;
  color: #fff;
  transform: scale(1.05);
}
</style>
