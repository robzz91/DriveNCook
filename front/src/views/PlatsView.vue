<template>
  <div>
    <h1>{{ t('plats.title') }}</h1>

    <input type="text" v-model="nomPlat" :placeholder="t('form.nomPlat')" />
    <input type="number" v-model="prix" :placeholder="t('form.prix')" />
    <button @click="ajouterPlat">{{ t('form.addPlat') }}</button>

    <h2>{{ t('list.plats') }}</h2>
    <ul>
      <li v-for="plat in plats" :key="plat.id">
        {{ plat.nom }} - {{ plat.prix }} €
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { usePlatStore } from '@/stores/platStore'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const store = usePlatStore()
const nomPlat = ref('')
const prix = ref(0)

const ajouterPlat = () => {
  if (nomPlat.value && prix.value) {
    store.ajouterPlat({ nom: nomPlat.value, prix: parseFloat(prix.value) })
    nomPlat.value = ''
    prix.value = 0
  }
}

const plats = store.plats
</script>
