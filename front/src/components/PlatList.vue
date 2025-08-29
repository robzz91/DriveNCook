<template>
  <div>
    <h2>Liste des plats</h2>
    <ul v-if="plats.length > 0">
      <li v-for="(plat, index) in plats" :key="index">
        {{ plat.nom }} - {{ plat.prix }} €
      </li>
    </ul>
    <p v-else>Aucun plat enregistré.</p>
  </div>
</template>

<script setup>
import { usePlatStore } from '@/stores/platStore'

const store = usePlatStore()
const plats = store.plats
</script>
