<template>
  <div>
    <table>
      <thead>
        <tr>
          <th>{{ $t("commandes.id") }}</th>
          <th>{{ $t("commandes.client") }}</th>
          <th>{{ $t("commandes.status") }}</th>
          <th>{{ $t("actions.title") }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="commande in commandes" :key="commande.id">
          <td>{{ commande.id }}</td>
          <td>{{ commande.client_id }}</td>
          <td>{{ commande.status }}</td>
          <td>
            <button @click="deleteCommande(commande.id)">
              {{ $t("actions.delete") }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { useCommandeStore } from "@/stores/commandeStore";
import { onMounted } from "vue";

export default {
  setup() {
    const store = useCommandeStore();
    onMounted(() => store.fetchCommandes());
    return { commandes: store.commandes, deleteCommande: store.deleteCommande };
  }
};
</script>
