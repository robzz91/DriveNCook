<!-- client/src/views/CommandesView.vue -->
<script setup>
import { onMounted } from "vue";
import { useCommandeStore } from "@/stores/commandeStore";

const store = useCommandeStore();

onMounted(() => {
  store.fetchAll();
});
</script>

<template>
  <div class="page">
    <h1>Commandes</h1>

    <p v-if="store.loading">Chargement...</p>
    <p v-else-if="store.error" class="error">{{ store.error }}</p>

    <ul v-else>
      <li v-for="c in store.items" :key="c.id">
        🛒 Commande #{{ c.id }} — Client {{ c.client_id }} — {{ c.status }}
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
