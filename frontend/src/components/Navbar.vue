<template>
  <nav class="bg-gray-900 border-b border-gray-800 px-6 py-3 flex items-center justify-between">
    <RouterLink to="/" class="text-white font-bold text-xl tracking-wide">
      GT <span class="text-red-500">Auto</span>
    </RouterLink>

    <div class="flex items-center gap-6 text-sm">
      <RouterLink to="/cars" class="text-gray-300 hover:text-white">Inventory</RouterLink>

      <template v-if="auth.isLoggedIn">
        <RouterLink v-if="auth.isEmployee" to="/admin/cars"   class="text-gray-300 hover:text-white">Manage Cars</RouterLink>
        <RouterLink v-if="auth.isEmployee" to="/admin/orders"       class="text-gray-300 hover:text-white">Orders</RouterLink>
        <RouterLink v-if="auth.isEmployee" to="/admin/appointments" class="text-gray-300 hover:text-white">Appointments</RouterLink>
        <RouterLink v-if="auth.isAdmin"    to="/admin/users"  class="text-gray-300 hover:text-white">Users</RouterLink>
        <RouterLink to="/dashboard" class="text-gray-300 hover:text-white">Dashboard</RouterLink>
        <button @click="handleLogout" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">
          Logout
        </button>
      </template>
      <template v-else>
        <RouterLink to="/login"    class="text-gray-300 hover:text-white">Login</RouterLink>
        <RouterLink to="/register" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">Register</RouterLink>
      </template>
    </div>
  </nav>
</template>

<script setup>
import { useAuthStore } from '../stores/auth.js'
import { useRouter } from 'vue-router'
const auth   = useAuthStore()
const router = useRouter()
function handleLogout() { auth.logout(); router.push('/') }
</script>
