import { createRouter, createWebHistory } from "vue-router";

import HomeView from "../views/HomeView.vue";
import ClientsView from "../views/ClientsView.vue";
import CommandesView from "../views/CommandesView.vue";
import EvenementsView from "../views/EvenementsView.vue";
import PlatsView from "../views/PlatsView.vue";

const routes = [
  { path: "/", name: "home", component: HomeView },
  { path: "/clients", name: "clients", component: ClientsView },
  { path: "/commandes", name: "commandes", component: CommandesView },
  { path: "/evenements", name: "evenements", component: EvenementsView },
  { path: "/plats", name: "plats", component: PlatsView },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
