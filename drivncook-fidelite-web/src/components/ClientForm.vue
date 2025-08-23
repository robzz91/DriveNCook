<template>
  <form @submit.prevent="addClient" class="form">
    <input v-model="client.nom" placeholder="Nom" required />
    <input v-model="client.email" placeholder="Email" required />
    <button type="submit">Ajouter client</button>
  </form>
</template>

<script setup>
import { reactive } from 'vue'
import { useClientStore } from '@/stores/clientStore'

const store = useClientStore()

const client = reactive({
  nom: '',
  email: ''
})

function addClient() {
  store.ajouterClient(client.nom, client.email)
  client.nom = ''
  client.email = ''
}
</script>

<style scoped>
.form {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  max-width: 300px;
  margin: 1rem auto;
}
input, button {
  padding: 0.5rem;
  font-size: 1rem;
}
button {
  background-color: #42b983;
  color: white;
  border: none;
  cursor: pointer;
}
</style>
