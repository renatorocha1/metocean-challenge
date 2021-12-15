import Vue from "vue"
import VueRouter from "vue-router"

import HomeController from "./pages/HomeComponent"
import NotFoundComponent from "./pages/NotFoundComponent"

Vue.use(VueRouter)

const routes = [
  {
    path: "/",
    component: HomeController,
    name: "home",
  },
  {
    path: "*",
    component: NotFoundComponent,
    name: "home",
  },
]

const router = new VueRouter({
  mode: "history",
  routes,
})

export default router
