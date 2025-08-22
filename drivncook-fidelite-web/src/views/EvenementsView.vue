<template>
  <div>
    <h1>{{ t('evenements.title') }}</h1>

    <div>
      <input v-model="titre" :placeholder="t('form.titre')" />
      <input v-model="description" :placeholder="t('form.description')" />
      <input v-model="date" type="date" :placeholder="t('form.date')" />
      <button @click="ajouterEvenement">{{ t('form.addEvenement') }}</button>
    </div>

    <h2>{{ t('list.evenements') }}</h2>
    <ul>
      <li v-for="(evt, index) in evenements" :key="index">
        {{ evt.date }} - {{ evt.titre }} : {{ evt.description }}
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useEvenementStore } from '@/stores/evenementStore'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const store = useEvenementStore()

const titre = ref('')
const description = ref('')
const date = ref('')

const ajouterEvenement = () => {
  if (titre.value && description.value && date.value) {
    store.ajouterEvenement({
      titre: titre.value,
      description: description.value,
      date: date.value
    })
    titre.value = ''
    description.value = ''
    date.value = ''
  }
}

const evenements = store.evenements
</script>
