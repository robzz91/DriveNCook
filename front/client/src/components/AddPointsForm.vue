<template>
  <form @submit.prevent="addPoints" class="form">
    <select v-model.number="clientId" required>
      <option disabled value="">-- Sélectionner un client --</option>
      <option v-for="client in clients" :key="client.id" :value="client.id">
        {{ client.nom }} ({{ client.email }})
      </option>
    </select>

    <input type="number" v-model.number="points" placeholder="Points à ajouter" required />
    <button type="submit">Ajouter les points</button>
  </form>
</template>

<script setup>
import { ref } from 'vue'
import { useClientStore } from '@/stores/clientStore'

const store = useClientStore()
const clients = store.clients

const clientId = ref('')
const points = ref(0)

function addPoints() {
  store.ajouterPoints(clientId.value, points.value)
  points.value = 0
  clientId.value = ''
}
</script>

<style scoped>
.form {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  max-width: 400px;
  margin: 2rem auto;
}
select, input, button {
  padding: 0.5rem;
  font-size: 1rem;
}
button {
  background-color: #007bff;
  color: white;
  border: none;
  cursor: pointer;
}
</style>
