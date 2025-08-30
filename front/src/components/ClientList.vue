<template>
  <div class="list-container">
    <h2>{{ $t("clients.list") }}</h2>
    <table class="styled-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>{{ $t("clients.name") }}</th>
          <th>{{ $t("clients.email") }}</th>
          <th>{{ $t("clients.points") }}</th>
          <th>{{ $t("actions.title") }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="c in store.clients" :key="c.id">
          <td>{{ c.id }}</td>
          <td>{{ c.nom }}</td>
          <td>{{ c.email }}</td>
          <td>{{ c.points }}</td>
          <td>
            <button class="btn-danger" @click="store.supprimerClient(c.id)">
              {{ $t("actions.delete") }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { useClientStore } from "@/stores/clientStore";
const store = useClientStore();
</script>
