<script setup>
import { onMounted } from "vue";
import { useCommandeStore } from "@/stores/commandeStore";
const store = useCommandeStore();
onMounted(() => store.fetchAll());
</script>

<template>
  <section>
    <h1>Commandes</h1>
    <p v-if="store.loading">Chargement…</p>
    <p v-else-if="store.error" style="color:red">{{ store.error }}</p>
    <ul v-else>
      <li v-for="o in store.items" :key="o.id">
        #{{ o.id }} — client: {{ o.client_nom }} — total TTC: {{ o.total_ttc }}
      </li>
    </ul>
  </section>
</template>
