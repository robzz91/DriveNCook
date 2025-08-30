<template>
  <div class="form-container">
    <h2>{{ $t("plats.add") }}</h2>
    <form @submit.prevent="ajouter">
      <div class="form-group">
        <label>{{ $t("plats.name") }}</label>
        <input v-model="nom" required />
      </div>
      <div class="form-group">
        <label>{{ $t("plats.price") }}</label>
        <input type="number" v-model="prix" required />
      </div>
      <button type="submit" class="btn-primary">{{ $t("actions.save") }}</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { usePlatStore } from "@/stores/platStore";

const store = usePlatStore();
const nom = ref("");
const prix = ref("");

function ajouter() {
  store.ajouterPlat({ nom: nom.value, prix: prix.value });
  nom.value = "";
  prix.value = "";
}
</script>
