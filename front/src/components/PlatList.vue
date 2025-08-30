<template>
  <div class="list-container">
    <h2>{{ $t("plats.list") }}</h2>
    <table class="styled-table">
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
          <td>{{ p.prix }}</td>
          <td>
            <button class="btn-danger" @click="store.supprimerPlat(p.id)">
              {{ $t("actions.delete") }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { usePlatStore } from "@/stores/platStore";
const store = usePlatStore();
</script>
