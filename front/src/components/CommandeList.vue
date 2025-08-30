<template>
  <div>
    <h2>Liste des commandes</h2>
    <button @click="fetchCommandes">Recharger</button>
    <ul>
      <li v-for="commande in commandes" :key="commande.id">
        Commande #{{ commande.id }} - Client {{ commande.client_id }} - Total: {{ commande.total_ttc }} €
        <button @click="deleteCommande(commande.id)">❌</button>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import { useCommandeStore } from "../stores/commandeStore";

const store = useCommandeStore();
const { commandes, fetchCommandes, deleteCommande } = store;

onMounted(() => {
  fetchCommandes();
});
</script>
