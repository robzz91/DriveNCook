import { defineStore } from 'pinia'

export const usePlatStore = defineStore('plat', {
  state: () => ({
    plats: []
  }),
  actions: {
    ajouterPlat(nom, prix) {
      this.plats.push({ nom, prix })
    }
  }
})
