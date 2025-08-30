<template>
  <div>
    <h2>Liste des plats</h2>
    <button @click="fetchPlats">Recharger</button>
    <ul>
      <li v-for="plat in plats" :key="plat.id">
        {{ plat.nom }} - {{ plat.prix }} €
        <button @click="deletePlat(plat.id)">❌</button>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import { usePlatStore } from "../stores/platStore";

const store = usePlatStore();
const { plats, fetchPlats, deletePlat } = store;

onMounted(() => {
  fetchPlats();
});
</script>
