<template>
  <div class="form-container">
    <h2>{{ $t("commandes.add") }}</h2>
    <form @submit.prevent="ajouter">
      <div class="form-group">
        <label>{{ $t("commandes.clientId") }}</label>
        <input v-model="clientId" type="number" required />
      </div>
      <div class="form-group">
        <label>{{ $t("commandes.total") }}</label>
        <input v-model="total" type="number" required />
      </div>
      <button type="submit" class="btn-primary">{{ $t("actions.save") }}</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useCommandeStore } from "@/stores/commandeStore";

const store = useCommandeStore();
const clientId = ref("");
const total = ref("");

function ajouter() {
  store.ajouterCommande({ clientId: clientId.value, total: total.value });
  clientId.value = "";
  total.value = "";
}
</script>
