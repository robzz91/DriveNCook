<template>
  <div>
    <table>
      <thead>
        <tr>
          <th>{{ $t("plats.name") }}</th>
          <th>{{ $t("plats.price") }}</th>
          <th>{{ $t("actions.title") }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="plat in plats" :key="plat.id">
          <td>{{ plat.nom }}</td>
          <td>{{ plat.prix }}</td>
          <td>
            <button @click="deletePlat(plat.id)">
              {{ $t("actions.delete") }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { usePlatStore } from "@/stores/platStore";
import { onMounted } from "vue";

export default {
  setup() {
    const store = usePlatStore();
    onMounted(() => store.fetchPlats());
    return { plats: store.plats, deletePlat: store.deletePlat };
  }
};
</script>
