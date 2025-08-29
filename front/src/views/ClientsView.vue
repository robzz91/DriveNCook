<script setup>
import { onMounted } from "vue";
import { useClientStore } from "@/stores/clientStore";
const store = useClientStore();
onMounted(() => store.fetchAll());
</script>

<template>
  <section>
    <h1>Clients</h1>
    <p v-if="store.loading">Chargement…</p>
    <p v-else-if="store.error" style="color:red">{{ store.error }}</p>
    <ul v-else>
      <li v-for="c in store.items" :key="c.id">{{ c.id }} — {{ c.nom }} ({{ c.email }})</li>
    </ul>
  </section>
</template>
