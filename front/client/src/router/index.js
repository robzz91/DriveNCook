import { createRouter, createWebHistory } from "vue-router";
import HomeView from "@/views/HomeView.vue";
import ClientsView from "@/views/ClientsView.vue";
import PlatsView from "@/views/PlatsView.vue";
import CommandesView from "@/views/CommandesView.vue";
import EvenementsView from "@/views/EvenementsView.vue";
import LoginView from "@/views/LoginView.vue";
import RegisterView from "@/views/RegisterView.vue";

import { useAuthStore } from "@/stores/authStore";

// Définition des routes
const routes = [
  {
    path: "/",
    name: "home",
    component: HomeView,
  },
  {
    path: "/clients",
    name: "clients",
    component: ClientsView,
    meta: { requiresAuth: true }, // protégé
  },
  {
    path: "/plats",
    name: "plats",
    component: PlatsView,
    meta: { requiresAuth: true }, // protégé
  },
  {
    path: "/commandes",
    name: "commandes",
    component: CommandesView,
    meta: { requiresAuth: true }, // protégé
  },
  {
    path: "/evenements",
    name: "evenements",
    component: EvenementsView,
    meta: { requiresAuth: true }, // protégé
  },
  {
    path: "/login",
    name: "login",
    component: LoginView,
  },
  {
    path: "/register",
    name: "register",
    component: RegisterView,
  },
];

// Création du router
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

// Middleware global
router.beforeEach((to, from, next) => {
  const auth = useAuthStore();

  if (to.meta.requiresAuth && !auth.isAuthenticated()) {
    // Si la route est protégée et que l'utilisateur n'est pas connecté
    next("/login");
  } else {
    next();
  }
});

export default router;
