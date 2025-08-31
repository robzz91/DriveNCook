<template>
  <div class="container">
    <h1>{{ $t("evenements.title") }}</h1>

    <h2>{{ $t("evenements.list") }}</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>{{ $t("evenements.name") }}</th>
          <th>{{ $t("evenements.date") }}</th>
          <th>{{ $t("actions.title") }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="e in store.evenements" :key="e.id">
          <td>{{ e.id }}</td>
          <td>{{ e.nom }}</td>
          <td>{{ e.date }}</td>
          <td>
            <button class="delete" @click="store.supprimerEvenement(e.id)">
              {{ $t("actions.delete") }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <h2>{{ $t("evenements.add") }}</h2>
    <form @submit.prevent="ajouter">
      <input v-model="nom" :placeholder="$t('evenements.name')" required />
      <input type="date" v-model="date" required />
      <button type="submit" class="add">{{ $t("actions.add") }}</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useEvenementStore } from "@/stores/evenementStore";

const store = useEvenementStore();
const nom = ref("");
const date = ref("");

function ajouter() {
  store.ajouterEvenement({ nom: nom.value, date: date.value });
  nom.value = "";
  date.value = "";
}
</script>
