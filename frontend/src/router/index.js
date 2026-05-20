import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/Login.vue')
    },
    {
      path: '/',
      name: 'dashboard',
      component: () => import('../components/DashboardLayout.vue'),
      meta: { requiresAuth: false }, // for PoC, we might disable this temporarily
      children: [
        {
          path: '',
          name: 'home',
          component: () => import('../views/Home.vue')
        }
      ]
    }
  ]
})

export default router
