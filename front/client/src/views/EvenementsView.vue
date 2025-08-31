<!-- client/src/views/EvenementsView.vue -->
<script setup>
import { onMounted } from "vue";
import { useEvenementStore } from "@/stores/evenementStore";

const store = useEvenementStore();

onMounted(() => {
  store.fetchAll();
});
</script>

<template>
  <div class="page">
    <h1>Événements</h1>

    <p v-if="store.loading">Chargement...</p>
    <p v-else-if="store.error" class="error">{{ store.error }}</p>

    <ul v-else>
      <li v-for="e in store.items" :key="e.id">
        📅 {{ e.nom }} — {{ e.date }}
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
