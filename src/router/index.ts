import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

import ThreeView from '../views/ThreeView.vue'
import QuizzView from '@/views/QuizzView.vue'
import ChatBot from '../views/ChatbotView.vue'
import AudioView from '../views/AudioView.vue'

const routes = [
  { path: '/', name: 'home', component: HomeView },
  {path: '/cours', name: 'cours', component: ThreeView },
  {path: '/quizz', name: 'quizz', component: QuizzView},
  {path: '/chatbot', name: 'chatbot', component: ChatBot},
  {path: '/audio', name: 'audio', component: AudioView},
  {path: '/random', name: 'random', redirect: () => {
      const availableRoutes = routes.filter(r => r.path !== '/random');
      const randomIndex = Math.floor(Math.random() * availableRoutes.length);
      return availableRoutes[randomIndex].path;
    }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
