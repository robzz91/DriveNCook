<script setup>
import { onMounted } from "vue";
import { useEvenementStore } from "@/stores/evenementStore";
const store = useEvenementStore();
onMounted(() => store.fetchAll());
</script>

<template>
  <section>
    <h1>Événements</h1>
    <p v-if="store.loading">Chargement…</p>
    <p v-else-if="store.error" style="color:red">{{ store.error }}</p>
    <ul v-else>
      <li v-for="e in store.items" :key="e.id">
        {{ e.titre }} — {{ e.date_evenement }}
      </li>
    </ul>
  </section>
</template>
