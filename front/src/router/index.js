// front/src/router/index.js
import { createRouter, createWebHistory } from "vue-router";

import ClientsView from "@/views/ClientsView.vue";
import CommandesView from "@/views/CommandesView.vue";
import PlatsView from "@/views/PlatsView.vue";
import EvenementsView from "@/views/EvenementsView.vue";

const routes = [
  { path: "/", redirect: "/clients" },
  { path: "/clients", component: ClientsView },
  { path: "/commandes", component: CommandesView },
  { path: "/plats", component: PlatsView },
  { path: "/evenements", component: EvenementsView },
];

export default createRouter({
  history: createWebHistory(),
  routes,
});
