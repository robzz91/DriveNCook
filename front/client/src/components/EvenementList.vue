<template>
  <div class="list-container">
    <h2>{{ $t("evenements.list") }}</h2>
    <table class="styled-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>{{ $t("evenements.title") }}</th>
          <th>{{ $t("evenements.date") }}</th>
          <th>{{ $t("actions.title") }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="e in store.evenements" :key="e.id">
          <td>{{ e.id }}</td>
          <td>{{ e.titre }}</td>
          <td>{{ e.date }}</td>
          <td>
            <button class="btn-danger" @click="store.supprimerEvenement(e.id)">
              {{ $t("actions.delete") }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { useEvenementStore } from "@/stores/evenementStore";
const store = useEvenementStore();
</script>
