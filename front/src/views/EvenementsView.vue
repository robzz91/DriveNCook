<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">{{ $t("evenements.title") }}</h1>

    <!-- Formulaire d'ajout -->
    <div class="mb-6 border rounded p-4 bg-gray-50">
      <h2 class="text-lg font-semibold mb-2">{{ $t("evenements.add") }}</h2>
      <EvenementForm />
    </div>

    <!-- Liste des événements -->
    <div>
      <h2 class="text-lg font-semibold mb-2">{{ $t("evenements.list") }}</h2>
      <EvenementList />
    </div>
  </div>
</template>

<script setup>
import EvenementList from "@/components/EvenementList.vue";
import EvenementForm from "@/components/EvenementForm.vue";
</script>
