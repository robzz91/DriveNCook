<template>
  <div>
    <h1>{{ $t("clients.title") }}</h1>
    <h2>{{ $t("clients.list") }}</h2>
    <ClientList />
    <h2>{{ $t("clients.add") }}</h2>
    <ClientForm />
  </div>
</template>

<script>
import ClientList from "@/components/ClientList.vue";
import ClientForm from "@/components/ClientForm.vue";

export default {
  components: { ClientList, ClientForm }
};
</script>
