<template>
  <section class="auth-shell">
    <div class="auth-card glass-panel">
      <div class="section-eyebrow">Account access</div>
      <h2 class="auth-title mt-3">Sign In</h2>
      <p class="auth-copy mt-3">
        Access your dashboard, saved orders, and appointments in the redesigned storefront.
      </p>

      <form class="mt-8 space-y-4" @submit.prevent="submit">
        <div>
          <label class="auth-label">Email</label>
          <input
            v-model="email"
            type="email"
            required
            class="inventory-input w-full rounded-xl px-3 py-2"
          />
        </div>
        <div>
          <label class="auth-label">Password</label>
          <input
            v-model="password"
            type="password"
            required
            class="inventory-input w-full rounded-xl px-3 py-2"
          />
        </div>

        <p v-if="error" class="site-status-message error">{{ error }}</p>

        <button
          type="submit"
          :disabled="loading"
          class="site-btn-primary w-full px-4 py-3 text-sm font-semibold disabled:opacity-50"
        >
          {{ loading ? 'Signing in...' : 'Sign In' }}
        </button>
      </form>

      <p class="mt-5 text-center text-sm text-slate-500">
        No account?
        <RouterLink to="/register" class="font-semibold text-blue-600">Register</RouterLink>
      </p>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'

const auth = useAuthStore()
const router = useRouter()
const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

async function submit() {
  loading.value = true
  error.value = ''
  try {
    const user = await auth.login(email.value, password.value)
    router.push(['admin', 'employee'].includes(user.role) ? '/admin/cars' : '/dashboard')
  } catch (e) {
    error.value = e.response?.data?.error ?? 'Login failed'
  } finally {
    loading.value = false
  }
}
</script>
