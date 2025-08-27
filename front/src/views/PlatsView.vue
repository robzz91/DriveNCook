<script setup>
import { onMounted } from "vue";
import { usePlatStore } from "@/stores/platStore";
const store = usePlatStore();
onMounted(() => store.fetchAll());
</script>

<template>
  <section>
    <h1>Plats</h1>
    <p v-if="store.loading">Chargement…</p>
    <p v-else-if="store.error" style="color:red">{{ store.error }}</p>
    <ul v-else>
      <li v-for="p in store.items" :key="p.id">{{ p.id }} — {{ p.nom }} — {{ p.prix }} €</li>
    </ul>
  </section>
</template>
