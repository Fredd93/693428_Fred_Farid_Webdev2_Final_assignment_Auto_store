import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'

const routes = [
  { path: '/',     redirect: '/home' },
  { path: '/home', component: () => import('../views/HomeView.vue') },
  { path: '/cars',          component: () => import('../views/CarsView.vue') },
  { path: '/cars/:id',      component: () => import('../views/CarDetailView.vue') },
  { path: '/login',         component: () => import('../views/LoginView.vue') },
  { path: '/register',      component: () => import('../views/RegisterView.vue') },
  {
    path: '/dashboard',
    component: () => import('../views/DashboardView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/cars',
    component: () => import('../views/AdminCarsView.vue'),
    meta: { requiresAuth: true, requiresRole: 'employee' }
  },
  {
    path: '/admin/orders',
    component: () => import('../views/AdminOrdersView.vue'),
    meta: { requiresAuth: true, requiresRole: 'employee' }
  },
  {
    path: '/admin/users',
    component: () => import('../views/AdminUsersView.vue'),
    meta: { requiresAuth: true, requiresRole: 'admin' }
  },
  {
    path: '/admin/appointments',
    component: () => import('../views/AdminAppointmentsView.vue'),
    meta: { requiresAuth: true, requiresRole: 'employee' }
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isLoggedIn) return '/login'
  if (to.meta.requiresRole === 'admin'    && !auth.isAdmin)    return '/dashboard'
  if (to.meta.requiresRole === 'employee' && !auth.isEmployee) return '/dashboard'
})

export default router
