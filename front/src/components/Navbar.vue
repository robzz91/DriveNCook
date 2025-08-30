<template>
  <nav>
    <ul>
      <li><router-link to="/clients">{{ $t("menu.clients") }}</router-link></li>
      <li><router-link to="/commandes">{{ $t("menu.commandes") }}</router-link></li>
      <li><router-link to="/evenements">{{ $t("menu.evenements") }}</router-link></li>
      <li><router-link to="/plats">{{ $t("menu.plats") }}</router-link></li>
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
