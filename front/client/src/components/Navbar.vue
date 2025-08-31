<template>
  <nav class="navbar">
    <div class="navbar-logo">DriveNCook</div>
    <ul class="navbar-links">
      <li><router-link to="/">{{ $t('menu.home') }}</router-link></li>
      <li><router-link to="/clients">{{ $t('menu.clients') }}</router-link></li>
      <li><router-link to="/commandes">{{ $t('menu.commandes') }}</router-link></li>
      <li><router-link to="/evenements">{{ $t('menu.evenements') }}</router-link></li>
      <li><router-link to="/plats">{{ $t('menu.plats') }}</router-link></li>
    </ul>
    <div class="navbar-lang">
      <button @click="setLang('fr')">FR</button>
      <button @click="setLang('en')">EN</button>
    </div>
  </nav>
</template>

<script setup>
import { useI18n } from "vue-i18n";
const { locale } = useI18n();

function setLang(lang) {
  locale.value = lang;
}
</script>
