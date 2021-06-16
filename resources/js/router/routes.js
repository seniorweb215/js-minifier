export const routes = [
    {
        path: "/",
        name: "dashboard",
        component: () => import("../views/dashboard/Dashboard")
    },
    {
        path: "/theme/:id",
        name: "theme",
        component: () => import("../views/theme/Theme")
    },
    { path: "*", redirect: { name: "error" } }
]