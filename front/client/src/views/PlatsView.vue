<template>
  <div class="container">
    <h1>{{ $t("plats.title") }}</h1>

    <h2>{{ $t("plats.list") }}</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>{{ $t("plats.name") }}</th>
          <th>{{ $t("plats.price") }}</th>
          <th>{{ $t("actions.title") }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="p in store.plats" :key="p.id">
          <td>{{ p.id }}</td>
          <td>{{ p.nom }}</td>
          <td>{{ p.prix }} €</td>
          <td>
            <button class="delete" @click="store.supprimerPlat(p.id)">
              {{ $t("actions.delete") }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <h2>{{ $t("plats.add") }}</h2>
    <form @submit.prevent="ajouter">
      <input v-model="nom" :placeholder="$t('plats.name')" required />
      <input v-model="prix" type="number" step="0.01" :placeholder="$t('plats.price')" required />
      <button type="submit" class="add">{{ $t("actions.save") }}</button>
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
