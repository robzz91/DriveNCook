<template>
  <div>
    <h2>{{ $t("clients.list") }}</h2>
    <table>
      <thead>
        <tr>
          <th>{{ $t("clients.name") }}</th>
          <th>{{ $t("clients.email") }}</th>
          <th>{{ $t("actions.title") }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="client in store.clients" :key="client.id">
          <td>{{ client.nom }}</td>
          <td>{{ client.email }}</td>
          <td>
            <button @click="store.deleteClient(client.id)">
              {{ $t("actions.delete") }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import { useClientStore } from "../stores/clientStore";

const store = useClientStore();

onMounted(() => {
  store.fetchClients();
});
</script>
