<template>
  <div>
    <table>
      <thead>
        <tr>
          <th>{{ $t("evenements.name") }}</th>
          <th>{{ $t("evenements.date") }}</th>
          <th>{{ $t("actions.title") }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="evt in evenements" :key="evt.id">
          <td>{{ evt.nom }}</td>
          <td>{{ evt.date }}</td>
          <td>
            <button @click="deleteEvenement(evt.id)">
              {{ $t("actions.delete") }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { useEvenementStore } from "@/stores/evenementStore";
import { onMounted } from "vue";

export default {
  setup() {
    const store = useEvenementStore();
    onMounted(() => store.fetchEvenements());
    return { evenements: store.evenements, deleteEvenement: store.deleteEvenement };
  }
};
</script>
