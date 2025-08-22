import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useEvenementStore = defineStore('evenementStore', () => {
  const evenements = ref([])

  const ajouterEvenement = (evenement) => {
    evenements.value.push(evenement)
  }

  return { evenements, ajouterEvenement }
})
