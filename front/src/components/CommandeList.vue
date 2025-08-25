<template>
  <div>
    <h2>{{ $t('commandesTitle') }}</h2>

    <ul>
      <li v-for="commande in commandes" :key="commande.id">
        {{ commande.plat }} - {{ commande.client }}
        <span v-if="commande.paye">✅ {{ $t('paye') }}</span>
        <button v-else @click="payer(commande)">{{ $t('payer') }}</button>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { useCommandeStore } from '../stores/commandeStore'
import { storeToRefs } from 'pinia'

const store = useCommandeStore()
const { commandes } = storeToRefs(store)

function payer(commande) {
  commande.paye = true
}
</script>
