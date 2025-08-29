<template>
  <div>
    <h2>{{ $t('commandes.title') }}</h2>
    <form @submit.prevent="passerCommande">
      <div>
        <label>{{ $t('commandes.client') }}</label>
        <select v-model="selectedClient">
          <option disabled value="">{{ $t('clients.selectClient') }}</option>
          <option v-for="client in clients" :key="client.email" :value="client.email">
            {{ client.nom }}
          </option>
        </select>
      </div>

      <div>
        <label>{{ $t('commandes.plat') }}</label>
        <select v-model="selectedPlat">
          <option disabled value="">-- Choisir un plat --</option>
          <option v-for="plat in plats" :key="plat.nom" :value="plat.nom">
            {{ plat.nom }}
          </option>
        </select>
      </div>

      <button type="submit">{{ $t('commandes.passerCommande') }}</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useClientStore } from '@/stores/clientStore'
import { usePlatStore } from '@/stores/platStore'
import { useCommandeStore } from '@/stores/commandeStore'

const clientStore = useClientStore()
const platStore = usePlatStore()
const commandeStore = useCommandeStore()

const selectedClient = ref('')
const selectedPlat = ref('')

const clients = clientStore.clients
const plats = platStore.plats

function passerCommande() {
  if (selectedClient.value && selectedPlat.value) {
    commandeStore.ajouterCommande(selectedClient.value, selectedPlat.value)
    selectedClient.value = ''
    selectedPlat.value = ''
  }
}
</script>
