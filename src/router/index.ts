import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import SkillCardView from '@/views/SkillCard/SkillCardView.vue'
import SkillCardRegisterView from '@/views/SkillCard/SkillCardRegisterView.vue'
import SkillCardLogInView from '@/views/SkillCard/SkillCardLogInView.vue'

const routes = [
  {path: '/', name: 'home', component: HomeView},
  {path: '/skill_card', name: 'skill_card', component: SkillCardView},
  {path: '/skill_card/register', name: 'skill_card_register', component: SkillCardRegisterView},
  {path: '/skill_card/login', name: 'skill_card_login', component: SkillCardLogInView}
]

const router= createRouter({
  history: createWebHistory(),
  routes,
})

export default router
