import { createRouter, createWebHistory } from "vue-router";

// Import des vues
import ClientsView from "../views/ClientsView.vue";
import PlatsView from "../views/PlatsView.vue";
import CommandesView from "../views/CommandesView.vue";
import EvenementsView from "../views/EvenementsView.vue";

const routes = [
  {
    path: "/",
    redirect: "/clients", // redirection par défaut
  },
  {
    path: "/clients",
    name: "Clients",
    component: ClientsView,
  },
  {
    path: "/plats",
    name: "Plats",
    component: PlatsView,
  },
  {
    path: "/commandes",
    name: "Commandes",
    component: CommandesView,
  },
  {
    path: "/evenements",
    name: "Evenements",
    component: EvenementsView,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
