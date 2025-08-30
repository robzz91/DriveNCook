<template>
  <div>
    <h2>Liste des événements</h2>
    <button @click="fetchEvenements">Recharger</button>
    <ul>
      <li v-for="event in evenements" :key="event.id">
        {{ event.titre }} - {{ event.date_evenement }}
        <button @click="deleteEvenement(event.id)">❌</button>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import { useEvenementStore } from "../stores/evenementStore";

const store = useEvenementStore();
const { evenements, fetchEvenements, deleteEvenement } = store;

onMounted(() => {
  fetchEvenements();
});
</script>
