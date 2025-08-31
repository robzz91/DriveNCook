<!-- client/src/views/ClientsView.vue -->
<script setup>
import { onMounted } from "vue";
import { useClientStore } from "@/stores/clientStore";

const store = useClientStore();

onMounted(() => {
  store.fetchAll();
});
</script>

<template>
  <div class="page">
    <h1>Clients</h1>

    <p v-if="store.loading">Chargement...</p>
    <p v-else-if="store.error" class="error">{{ store.error }}</p>

    <ul v-else>
      <li v-for="c in store.items" :key="c.id">
        {{ c.nom }} — {{ c.email }}
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
