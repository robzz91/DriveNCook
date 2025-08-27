<script setup>
import { useI18n } from "vue-i18n";
const { t, locale } = useI18n();
</script>

<template>
  <nav class="nav">
    <router-link to="/clients">{{ t('menu.clients') }}</router-link>
    <router-link to="/commandes">{{ t('menu.commandes') }}</router-link>
    <router-link to="/plats">{{ t('menu.plats') }}</router-link>
    <router-link to="/evenements">{{ t('menu.evenements') }}</router-link>

    <span class="spacer"></span>

    <button @click="locale.value='fr'">FR</button>
    <button @click="locale.value='en'">EN</button>
  </nav>
</template>

<style scoped>
.nav { display:flex; gap:1rem; padding:0.75rem 1rem; border-bottom:1px solid #ddd; }
.spacer { flex:1; }
a.router-link-active { font-weight: 700; }
button { padding: .25rem .5rem; }
</style>
