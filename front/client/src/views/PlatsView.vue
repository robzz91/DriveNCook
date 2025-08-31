<!-- client/src/views/PlatsView.vue -->
<script setup>
import { onMounted } from "vue";
import { usePlatStore } from "@/stores/platStore";

const store = usePlatStore();

onMounted(() => {
  store.fetchAll();
});
</script>

<template>
  <div class="page">
    <h1>Plats</h1>

    <p v-if="store.loading">Chargement...</p>
    <p v-else-if="store.error" class="error">{{ store.error }}</p>

    <ul v-else>
      <li v-for="p in store.items" :key="p.id">
        🍽️ {{ p.nom }} — {{ p.prix }} €
      </li>
    </ul>
  </div>
</template>

<style scoped>
.page {
  padding: 24px;
}
.error {
  color: red;
}
</style>
