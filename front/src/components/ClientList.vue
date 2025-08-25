<template>
  <div class="client-list">
    <h2>Liste des clients</h2>
    <ul>
      <li v-for="client in clients" :key="client.email">
        {{ client.nom }} - {{ client.email }}
      </li>
    </ul>
  </div>
</template>

<script setup>
import { useClientStore } from '@/stores/clientStore'

const store = useClientStore()
const clients = store.clients
</script>

<style scoped>
.client-list {
  margin: 2rem auto;
  max-width: 400px;
  padding: 1rem;
  border: 1px solid #ddd;
  border-radius: 8px;
}
h2 {
  text-align: center;
  margin-bottom: 1rem;
}
ul {
  list-style: none;
  padding: 0;
}
li {
  padding: 0.5rem;
  border-bottom: 1px solid #eee;
}
</style>
