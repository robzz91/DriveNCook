<template>
  <div class="home">
    <h1 class="page-title">{{ $t("home.title") }}</h1>
    <p class="page-subtitle">
      {{ $t("home.subtitle") }}
    </p>

    <div class="cards-container">
      <router-link to="/clients" class="card">👤 {{ $t("menu.clients") }}</router-link>
      <router-link to="/commandes" class="card">🛒 {{ $t("menu.commandes") }}</router-link>
      <router-link to="/evenements" class="card">🎉 {{ $t("menu.evenements") }}</router-link>
      <router-link to="/plats" class="card">🍽 {{ $t("menu.plats") }}</router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: "HomeView"
};
</script>

<style scoped>
.home {
  text-align: center;
  padding: 2rem;
  color: white;
}
.page-title {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}
.page-subtitle {
  font-size: 1.2rem;
  margin-bottom: 2rem;
}
.cards-container {
  display: flex;
  justify-content: center;
  gap: 1.5rem;
  flex-wrap: wrap;
}
.card {
  background: #222;
  color: #ffcc00;
  padding: 1.5rem;
  border-radius: 10px;
  text-decoration: none;
  font-size: 1.2rem;
  transition: transform 0.2s ease, background 0.2s ease;
}
.card:hover {
  background: #333;
  transform: translateY(-5px);
}
</style>
