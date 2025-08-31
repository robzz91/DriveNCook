<template>
  <div class="form-container">
    <h2>{{ $t("evenements.add") }}</h2>
    <form @submit.prevent="ajouter">
      <div class="form-group">
        <label>{{ $t("evenements.title") }}</label>
        <input v-model="titre" required />
      </div>
      <div class="form-group">
        <label>{{ $t("evenements.date") }}</label>
        <input type="date" v-model="date" required />
      </div>
      <button type="submit" class="btn-primary">{{ $t("actions.save") }}</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useEvenementStore } from "@/stores/evenementStore";

const store = useEvenementStore();
const titre = ref("");
const date = ref("");

function ajouter() {
  store.ajouterEvenement({ titre: titre.value, date: date.value });
  titre.value = "";
  date.value = "";
}
</script>
