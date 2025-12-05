import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import AboutView from '@/views/AboutView.vue'
import HelloWorld from '@/components/HelloWorld.vue'

const routes = [
  { path: '/', name: 'home', component: HomeView },
  { path: '/hello', name: 'hello', component: HelloWorld },
  { path: '/about', name: 'about', component: AboutView },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
