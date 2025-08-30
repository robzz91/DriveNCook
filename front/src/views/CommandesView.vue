<template>
  <div>
    <h1>{{ $t("commandes.title") }}</h1>
    <h2>{{ $t("commandes.list") }}</h2>
    <CommandeList />
    <h2>{{ $t("commandes.add") }}</h2>
    <CommandeForm />
  </div>
</template>

<script>
import CommandeList from "@/components/CommandeList.vue";
import CommandeForm from "@/components/CommandeForm.vue";

export default {
  components: { CommandeList, CommandeForm }
};
</script>
