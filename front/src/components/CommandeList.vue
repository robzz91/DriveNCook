<template>
  <div class="list-container">
    <h2>{{ $t("commandes.list") }}</h2>
    <table class="styled-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>{{ $t("commandes.clientId") }}</th>
          <th>{{ $t("commandes.total") }}</th>
          <th>{{ $t("actions.title") }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="cmd in store.commandes" :key="cmd.id">
          <td>{{ cmd.id }}</td>
          <td>{{ cmd.clientId }}</td>
          <td>{{ cmd.total }}</td>
          <td>
            <button class="btn-danger" @click="store.supprimerCommande(cmd.id)">
              {{ $t("actions.delete") }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { useCommandeStore } from "@/stores/commandeStore";
const store = useCommandeStore();
</script>
