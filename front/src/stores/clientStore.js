import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useClientStore = defineStore('clientStore', () => {
  const clients = ref([])
  let idCounter = 1

  // Ajoute un nouveau client avec une carte de fidélité
  function ajouterClient(nom, email) {
    clients.value.push({
      id: idCounter++,
      nom,
      email,
      carte: {
        points: 0,
      },
    })
  }

  // Ajoute des points de fidélité à un client existant
  function ajouterPoints(clientId, points) {
    const client = clients.value.find(c => c.id === clientId)
    if (client) {
      client.carte.points += parseInt(points)
    }
  }

  return {
    clients,
    ajouterClient,
    ajouterPoints,
  }
})
