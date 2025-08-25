// src/stores/commandeStore.js
import { defineStore } from 'pinia'

export const useCommandeStore = defineStore('commande', {
  state: () => ({
    commandes: [],
    paiements: []
  }),
  actions: {
    ajouterCommande(commande) {
      this.commandes.push(commande)
    },
    simulerPaiement(commandeId) {
      const commande = this.commandes.find(c => c.id === commandeId)
      if (commande) {
        this.paiements.push({
          id: commandeId,
          statut: 'payé',
          date: new Date().toLocaleString()
        })
      }
    }
  }
})
