// client/src/router/index.js
import { createRouter, createWebHistory } from "vue-router";

import Home from "@/views/HomeView.vue";
import ClientsView from "@/views/ClientsView.vue";
import PlatsView from "@/views/PlatsView.vue";
import CommandesView from "@/views/CommandesView.vue";
import EvenementsView from "@/views/EvenementsView.vue";
import LoginView from "@/views/LoginView.vue"; // à créer à l'étape 5

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: "/", name: "home", component: Home }, // public
    { path: "/login", name: "login", component: LoginView }, // public
    { path: "/clients", name: "clients", component: ClientsView, meta: { requiresAuth: true } },
    { path: "/plats", name: "plats", component: PlatsView, meta: { requiresAuth: true } },
    { path: "/commandes", name: "commandes", component: CommandesView, meta: { requiresAuth: true } },
    { path: "/evenements", name: "evenements", component: EvenementsView, meta: { requiresAuth: true } },
  ],
});

// Garde simple: nécessite un token pour routes protégées
router.beforeEach((to, from, next) => {
  if (!to.meta.requiresAuth) return next();
  const hasToken = !!localStorage.getItem("token");
  if (!hasToken) {
    return next({ name: "login", query: { redirect: to.fullPath } });
  }
  next();
});

export default router;
