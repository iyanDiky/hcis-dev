import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/auth',
      component: () => import('../layouts/AuthLayout.vue'),
      children: [
        {
          path: '/login',
          name: 'login',
          component: () => import('../views/Login.vue')
        }
      ]
    },
    {
      path: '/',
      component: () => import('../layouts/MainLayout.vue'),
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'dashboard',
          component: () => import('../views/Dashboard.vue')
        },
        {
          path: '/provinsi',
          name: 'Provinsi',
          component: () => import('../views/master/Provinsi/ProvinsiList.vue')
        },
        {
          path: '/kota',
          name: 'KotaKabupaten',
          component: () => import('../views/master/Kota/KotaList.vue')
        },
        {
          path: '/sdm-jenis',
          name: 'SdmJenis',
          component: () => import('../views/sdm/SdmJenis/SdmJenisList.vue')
        },
        {
          path: '/sdm-data',
          name: 'SdmData',
          component: () => import('../views/sdm/SdmData/SdmDataList.vue')
        }
      ]
    }
  ]
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('auth_token')
  if (to.meta.requiresAuth && !token) {
    next({ name: 'login' })
  } else if (to.name === 'login' && token) {
    next({ name: 'dashboard' })
  } else {
    next()
  }
})

export default router
