<template>
  <nav>
    <ul>
      <li><router-link to="/clients">Clients</router-link></li>
      <li><router-link to="/plats">Plats</router-link></li>
      <li><router-link to="/commandes">Commandes</router-link></li>
      <li><router-link to="/evenements">Événements</router-link></li>
    </ul>
  </nav>
</template>

<script setup>
</script>

<style scoped>
nav {
  background: #333;
  padding: 10px;
}
ul {
  display: flex;
  list-style: none;
  gap: 15px;
}
a {
  color: white;
  text-decoration: none;
}
a.router-link-active {
  font-weight: bold;
  text-decoration: underline;
}
</style>
