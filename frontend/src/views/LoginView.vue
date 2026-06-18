<template>
  <div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-8 w-full max-w-md">
      <h2 class="text-2xl font-bold text-white mb-6 text-center">Sign In</h2>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="text-gray-400 text-sm block mb-1">Email</label>
          <input v-model="email" type="email" required
            class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
        </div>
        <div>
          <label class="text-gray-400 text-sm block mb-1">Password</label>
          <input v-model="password" type="password" required
            class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
        </div>

        <p v-if="error" class="text-red-400 text-sm">{{ error }}</p>

        <button type="submit" :disabled="loading"
          class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-semibold disabled:opacity-50">
          {{ loading ? 'Signing in...' : 'Sign In' }}
        </button>
      </form>

      <p class="text-center text-gray-500 text-sm mt-4">
        No account? <RouterLink to="/register" class="text-red-400 hover:text-red-300">Register</RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'

const auth     = useAuthStore()
const router   = useRouter()
const email    = ref('')
const password = ref('')
const loading  = ref(false)
const error    = ref('')

async function submit() {
  loading.value = true
  error.value   = ''
  try {
    const user = await auth.login(email.value, password.value)
    router.push(['admin','employee'].includes(user.role) ? '/admin/cars' : '/dashboard')
  } catch (e) {
    error.value = e.response?.data?.error ?? 'Login failed'
  } finally {
    loading.value = false
  }
}
</script>
