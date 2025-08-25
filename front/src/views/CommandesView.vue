<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold mb-4">{{ $t('commandes.title') }}</h2>

    <!-- Sélection du client -->
    <div class="mb-4">
      <label class="block mb-1">{{ $t('form.selectClient') }}</label>
      <select v-model="selectedClient" class="border p-2 w-full rounded">
        <option disabled value="">{{ $t('form.selectClient') }}</option>
        <option v-for="(client, index) in clients" :key="index" :value="client.nom">
          {{ client.nom }}
        </option>
      </select>
    </div>

    <!-- Sélection du plat -->
    <div class="mb-4">
      <label class="block mb-1">{{ $t('form.selectPlat') }}</label>
      <select v-model="selectedPlat" class="border p-2 w-full rounded">
        <option disabled value="">{{ $t('form.selectPlat') }}</option>
        <option v-for="(plat, index) in plats" :key="index" :value="plat.nom">
          {{ plat.nom }} - {{ plat.prix }} €
        </option>
      </select>
    </div>

    <!-- Bouton pour passer une commande -->
    <button
      @click="passerCommande"
      class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600 mb-6"
    >
      {{ $t('form.placeOrder') }}
    </button>

    <!-- Liste des commandes -->
    <h3 class="text-xl font-semibold mb-2">{{ $t('list.commandes') }}</h3>
    <ul class="space-y-2">
      <li
        v-for="(commande, index) in commandes"
        :key="index"
        class="flex justify-between items-center border p-3 rounded bg-gray-50"
      >
        <span>
          {{ commande.client }} - {{ commande.plat }} - {{ commande.prix }} €
        </span>

        <!-- Bouton Payer uniquement si non payé -->
        <button
          v-if="!commande.paye"
          @click="payerCommande(index)"
          class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600"
        >
          {{ $t('form.payer') }}
        </button>

        <!-- Message payé -->
        <span v-else class="text-green-600 font-bold">
          {{ $t('form.paid') }}
        </span>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useClientStore } from '../stores/clientStore';
import { usePlatStore } from '../stores/platStore';

const clientStore = useClientStore();
const platStore = usePlatStore();

const selectedClient = ref('');
const selectedPlat = ref('');
const commandes = ref([]);

const clients = clientStore.clients;
const plats = platStore.plats;

onMounted(() => {
  const savedCommandes = localStorage.getItem('commandes');
  if (savedCommandes) {
    commandes.value = JSON.parse(savedCommandes);
  }
});

function passerCommande() {
  const plat = plats.value.find((p) => p.nom === selectedPlat.value);
  if (!plat || !selectedClient.value) return;

  const nouvelleCommande = {
    id: Date.now(),
    client: selectedClient.value,
    plat: selectedPlat.value,
    prix: plat.prix,
    paye: false
  };

  commandes.value.push(nouvelleCommande);
  localStorage.setItem('commandes', JSON.stringify(commandes.value));

  selectedClient.value = '';
  selectedPlat.value = '';
}

function payerCommande(index) {
  const commande = commandes.value[index];
  if (commande && !commande.paye) {
    commande.paye = true;
    alert(`✅ Paiement simulé pour la commande : ${commande.plat}`);
    localStorage.setItem('commandes', JSON.stringify(commandes.value));
  }
}
</script>
