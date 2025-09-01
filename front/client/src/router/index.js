// client/src/router/index.js
import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/authStore";

// Views
import Register from "@/views/RegisterView.vue";
import Login from "@/views/LoginView.vue";
import ClientsView from "@/views/ClientsView.vue";
import PlatsView from "@/views/PlatsView.vue";
import CommandesView from "@/views/CommandesView.vue";
import EvenementsView from "@/views/EvenementsView.vue";

const routes = [
  {
    path: "/register",
    name: "Register",
    component: Register,
    meta: { guest: true },
  },
  {
    path: "/login",
    name: "Login",
    component: Login,
    meta: { guest: true },
  },
  {
    path: "/clients",
    name: "Clients",
    component: ClientsView,
    meta: { requiresAuth: true },
  },
  {
    path: "/plats",
    name: "Plats",
    component: PlatsView,
    meta: { requiresAuth: true },
  },
  {
    path: "/commandes",
    name: "Commandes",
    component: CommandesView,
    meta: { requiresAuth: true },
  },
  {
    path: "/evenements",
    name: "Evenements",
    component: EvenementsView,
    meta: { requiresAuth: true },
  },
  {
    path: "/:pathMatch(.*)*",
    redirect: "/login",
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

/**
 * Navigation guard global
 */
router.beforeEach((to) => {
  const auth = useAuthStore();

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: "Login" };
  }

  if (to.meta.guest && auth.isAuthenticated) {
    return { name: "Clients" }; // ou autre route par défaut après login
  }

  return true;
});

export default router;
