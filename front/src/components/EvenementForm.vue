<template>
  <form class="space-y-3" @submit.prevent="submit">
    <div>
      <label class="block mb-1">{{ $t("evenements.name") }}</label>
      <input v-model="nom" type="text" required class="border px-3 py-2 rounded w-full" />
    </div>

    <div>
      <label class="block mb-1">{{ $t("evenements.date") }}</label>
      <input v-model="date" type="date" required class="border px-3 py-2 rounded w-full" />
    </div>

    <button type="submit" class="border px-4 py-2 rounded">
      {{ $t("actions.add") }}
    </button>
  </form>
</template>

<script setup>
import { ref } from "vue";
import { useEvenementStore } from "@/stores/evenementStore";

const nom = ref("");
const date = ref("");

const store = useEvenementStore();

const submit = async () => {
  await store.createEvenement({ nom: nom.value, date: date.value });
  nom.value = "";
  date.value = "";
  await store.fetchEvenements();
};
</script>
