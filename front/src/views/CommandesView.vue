<template>
  <div class="container">
    <h1>{{ $t("commandes.title") }}</h1>

    <h2>{{ $t("commandes.list") }}</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>{{ $t("commandes.client") }}</th>
          <th>{{ $t("commandes.status") }}</th>
          <th>{{ $t("actions.title") }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="cmd in store.commandes" :key="cmd.id">
          <td>{{ cmd.id }}</td>
          <td>{{ cmd.client_id }}</td>
          <td>{{ cmd.status }}</td>
          <td>
            <button class="delete" @click="store.supprimerCommande(cmd.id)">
              {{ $t("actions.delete") }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <h2>{{ $t("commandes.add") }}</h2>
    <form @submit.prevent="ajouter">
      <select v-model="clientId" required>
        <option disabled value="">{{ $t("clients.selectClient") }}</option>
        <option v-for="c in clientStore.clients" :key="c.id" :value="c.id">
          {{ c.nom }}
        </option>
      </select>

      <select v-model="platId" required>
        <option disabled value="">{{ $t("commandes.selectPlat") }}</option>
        <option v-for="p in platStore.plats" :key="p.id" :value="p.id">
          {{ p.nom }}
        </option>
      </select>

      <button type="submit" class="add">{{ $t("commandes.passerCommande") }}</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useCommandeStore } from "@/stores/commandeStore";
import { useClientStore } from "@/stores/clientStore";
import { usePlatStore } from "@/stores/platStore";

const store = useCommandeStore();
const clientStore = useClientStore();
const platStore = usePlatStore();

const clientId = ref("");
const platId = ref("");

function ajouter() {
  store.ajouterCommande({ client_id: clientId.value, plat_id: platId.value });
  clientId.value = "";
  platId.value = "";
}
</script>
