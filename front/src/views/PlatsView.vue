<template>
  <div>
    <h1>{{ $t("plats.title") }}</h1>
    <h2>{{ $t("plats.list") }}</h2>
    <PlatList />
    <h2>{{ $t("plats.add") }}</h2>
    <PlatForm />
  </div>
</template>

<script>
import PlatList from "@/components/PlatList.vue";
import PlatForm from "@/components/PlatForm.vue";

export default {
  components: { PlatList, PlatForm }
};
</script>
