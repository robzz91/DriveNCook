<template>
  <div>
    <select v-model="locale">
      <option value="fr">Français</option>
      <option value="en">English</option>
    </select>

    <nav>
      <RouterLink to="/clients">{{ t('menu.clients') }}</RouterLink> |
      <RouterLink to="/commandes">{{ t('menu.commandes') }}</RouterLink> |
      <RouterLink to="/plats">{{ t('menu.plats') }}</RouterLink> |
      <RouterLink to="/evenements">{{ t('menu.evenements') }}</RouterLink>
    </nav>

    <router-view />
  </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n'

const { locale, t } = useI18n()
</script>
