<template>
  <div class="form-container">
    <h2>{{ $t("clients.add") }}</h2>
    <form @submit.prevent="ajouter">
      <div class="form-group">
        <label for="nom">{{ $t("clients.name") }}</label>
        <input id="nom" v-model="nom" required />
      </div>

      <div class="form-group">
        <label for="email">{{ $t("clients.email") }}</label>
        <input id="email" v-model="email" type="email" required />
      </div>

      <button type="submit" class="btn-primary">
        {{ $t("actions.save") }}
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useClientStore } from "@/stores/clientStore";

const store = useClientStore();
const nom = ref("");
const email = ref("");

function ajouter() {
  store.ajouterClient({ nom: nom.value, email: email.value });
  nom.value = "";
  email.value = "";
}
</script>
