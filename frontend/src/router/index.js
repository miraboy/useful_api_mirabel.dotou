import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import RegisterView from "../views/RegisterView.vue";
import LoginView from "../views/LoginView.vue";
import DashboardView from "../views/DashboardView.vue";
import DashboardHome from "../views/DashboardHome.vue";
import WalletView from "../views/WalletView.vue";
import UrlShortenerView from "../views/UrlShortenerView.vue";

const routes = [
    {
        path: "/",
        redirect: "/login",
    },
    {
        path: "/login",
        name: "Login",
        component: LoginView,
        meta: { guest: true },
    },
    {
        path: "/register",
        name: "Register",
        component: RegisterView,
        meta: { guest: true },
    },
    {
        path: "/dashboard",
        component: DashboardView,
        meta: { requiresAuth: true },
        children: [
            {
                path: "",
                name: "DashboardHome",
                component: DashboardHome,
            },
            {
                path: "wallet",
                name: "Wallet",
                component: WalletView,
            },
            {
                path: "url-shortener",
                name: "UrlShortener",
                component: UrlShortenerView,
            },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Navigation guard
router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next("/login");
    } else if (to.meta.guest && authStore.isAuthenticated) {
        next("/dashboard");
    } else {
        next();
    }
});

export default router;
