import { createRouter, createWebHistory } from "vue-router";
import ClientsView from "@/views/ClientsView.vue";
import CommandesView from "@/views/CommandesView.vue";
import EvenementsView from "@/views/EvenementsView.vue";
import PlatsView from "@/views/PlatsView.vue";

const routes = [
  { path: "/", redirect: "/clients" },
  { path: "/clients", name: "Clients", component: ClientsView },
  { path: "/commandes", name: "Commandes", component: CommandesView },
  { path: "/evenements", name: "Événements", component: EvenementsView },
  { path: "/plats", name: "Plats", component: PlatsView },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
