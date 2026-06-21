<template>
  <nav class="site-nav">
    <div class="site-nav-inner flex items-center justify-between px-1 py-4">
      <RouterLink to="/home" class="site-nav-brand">
        Grand Transmission <span>Auto</span>
      </RouterLink>

      <div class="flex items-center gap-5 text-sm font-medium">
        <RouterLink to="/cars" class="site-nav-link">Inventory</RouterLink>

        <template v-if="auth.isLoggedIn">
          <RouterLink v-if="auth.isEmployee" to="/admin/cars" class="site-nav-link">Manage Cars</RouterLink>
          <RouterLink v-if="auth.isEmployee" to="/admin/orders" class="site-nav-link">Orders</RouterLink>
          <RouterLink v-if="auth.isEmployee" to="/admin/appointments" class="site-nav-link">Appointments</RouterLink>
          <RouterLink v-if="auth.isAdmin" to="/admin/users" class="site-nav-link">Users</RouterLink>
          <RouterLink to="/dashboard" class="site-nav-link">Dashboard</RouterLink>
          <button @click="handleLogout" class="site-btn-secondary px-4 py-2 text-sm font-semibold">
            Logout
          </button>
        </template>
        <template v-else>
          <RouterLink to="/login" class="site-nav-link">Login</RouterLink>
          <RouterLink to="/register" class="site-btn-primary px-4 py-2 text-sm font-semibold">
            Register
          </RouterLink>
        </template>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useAuthStore } from '../stores/auth.js'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

function handleLogout() {
  auth.logout()
  router.push('/home')
}
</script>
