<template>
  <section class="auth-shell">
    <div class="auth-card glass-panel">
      <div class="section-eyebrow">New account</div>
      <h2 class="auth-title mt-3">Create Account</h2>
      <p class="auth-copy mt-3">
        Register once and use the same backend-powered dashboard for purchases, leasing, and appointments.
      </p>

      <form class="mt-8 space-y-4" @submit.prevent="submit">
        <div>
          <label class="auth-label">Full Name</label>
          <input
            v-model="name"
            type="text"
            required
            class="inventory-input w-full rounded-xl px-3 py-2"
          />
        </div>
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
            minlength="6"
            class="inventory-input w-full rounded-xl px-3 py-2"
          />
        </div>

        <p v-if="error" class="site-status-message error">{{ error }}</p>

        <button
          type="submit"
          :disabled="loading"
          class="site-btn-primary w-full px-4 py-3 text-sm font-semibold disabled:opacity-50"
        >
          {{ loading ? 'Creating...' : 'Create Account' }}
        </button>
      </form>

      <p class="mt-5 text-center text-sm text-slate-500">
        Already have an account?
        <RouterLink to="/login" class="font-semibold text-blue-600">Sign in</RouterLink>
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
const name = ref('')
const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

async function submit() {
  loading.value = true
  error.value = ''
  try {
    await auth.register(name.value, email.value, password.value)
    router.push('/dashboard')
  } catch (e) {
    error.value = e.response?.data?.error ?? 'Registration failed'
  } finally {
    loading.value = false
  }
}
</script>
