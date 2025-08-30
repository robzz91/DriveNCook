<template>
  <div class="container">
    <h1>{{ $t("clients.title") }}</h1>

    <h2>{{ $t("clients.list") }}</h2>
    <table>
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
            <button class="delete" @click="store.supprimerClient(c.id)">
              {{ $t("actions.delete") }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <h2>{{ $t("clients.add") }}</h2>
    <form @submit.prevent="ajouter">
      <input v-model="nom" :placeholder="$t('clients.name')" required />
      <input v-model="email" :placeholder="$t('clients.email')" required />
      <button type="submit" class="add">{{ $t("actions.save") }}</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useClientStore } from "@/stores/clientStore";

const store = useClientStore();
const nom = ref("");
const email = ref("");

function ajouter() {
  store.ajouterClient({ nom: nom.value, email: email.value });
  nom.value = "";
  email.value = "";
}
</script>
