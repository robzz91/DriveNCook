<template>
  <div>
    <h1>{{ t('app.title') }}</h1>

    <input type="text" v-model="nom" :placeholder="t('form.name')" />
    <input type="email" v-model="email" :placeholder="t('form.email')" />
    <button @click="ajouterClient">{{ t('form.addClient') }}</button>

    <select v-model="selectedClientId">
      <option disabled value="">{{ t('form.selectClient') }}</option>
      <option v-for="client in clients" :key="client.id" :value="client.id">
        {{ client.nom }}
      </option>
    </select>

    <input type="number" v-model="points" />
    <button @click="ajouterPoints">{{ t('form.addPoints') }}</button>

    <h2>{{ t('list.clients') }}</h2>
    <ul>
      <li v-for="client in clients" :key="client.id">
        {{ client.nom }} ({{ client.email }}) - {{ client.points }} {{ t('form.points') }}
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useClientStore } from '@/stores/clientStore'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const store = useClientStore()
const nom = ref('')
const email = ref('')
const selectedClientId = ref('')
const points = ref(0)

const ajouterClient = () => {
  if (nom.value && email.value) {
    store.ajouterClient({ nom: nom.value, email: email.value })
    nom.value = ''
    email.value = ''
  }
}

const ajouterPoints = () => {
  if (selectedClientId.value && points.value) {
    store.ajouterPoints(parseInt(selectedClientId.value), parseInt(points.value))
    points.value = 0
  }
}

const clients = store.clients
</script>
