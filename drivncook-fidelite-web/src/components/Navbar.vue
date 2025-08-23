<template>
  <nav class="navbar">
    <router-link to="/clients" class="nav-link">Clients</router-link>
    <router-link to="/plats" class="nav-link">Plats</router-link>
    <router-link to="/commandes" class="nav-link">Commandes</router-link>
  </nav>
</template>

<script setup>
</script>

<style scoped>
.navbar {
  background-color: #f2f2f2;
  padding: 1rem;
  display: flex;
  gap: 1rem;
}
.nav-link {
  text-decoration: none;
  color: #333;
  font-weight: bold;
}
.nav-link.router-link-active {
  color: #007bff;
}
</style>
