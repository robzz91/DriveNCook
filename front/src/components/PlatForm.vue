<template>
  <form @submit.prevent="addPlat">
    <input v-model="plat.nom" placeholder="Nom du plat" required />
    <input v-model.number="plat.prix" placeholder="Prix (€)" required type="number" min="0" step="0.01" />
    <button type="submit">Ajouter le plat</button>
  </form>
</template>

<script setup>
import { reactive } from 'vue'
import { usePlatStore } from '@/stores/platStore'

const store = usePlatStore()

const plat = reactive({
  nom: '',
  prix: ''
})

function addPlat() {
  store.ajouterPlat(plat.nom, plat.prix)
  plat.nom = ''
  plat.prix = ''
}
</script>
