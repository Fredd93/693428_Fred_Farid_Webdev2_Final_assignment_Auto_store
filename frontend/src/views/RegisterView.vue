<template>
  <div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-8 w-full max-w-md">
      <h2 class="text-2xl font-bold text-white mb-6 text-center">Create Account</h2>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="text-gray-400 text-sm block mb-1">Full Name</label>
          <input v-model="name" type="text" required
            class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
        </div>
        <div>
          <label class="text-gray-400 text-sm block mb-1">Email</label>
          <input v-model="email" type="email" required
            class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
        </div>
        <div>
          <label class="text-gray-400 text-sm block mb-1">Password</label>
          <input v-model="password" type="password" required minlength="6"
            class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
        </div>

        <p v-if="error" class="text-red-400 text-sm">{{ error }}</p>

        <button type="submit" :disabled="loading"
          class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-semibold disabled:opacity-50">
          {{ loading ? 'Creating...' : 'Create Account' }}
        </button>
      </form>

      <p class="text-center text-gray-500 text-sm mt-4">
        Already have an account? <RouterLink to="/login" class="text-red-400 hover:text-red-300">Sign in</RouterLink>
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
const name     = ref('')
const email    = ref('')
const password = ref('')
const loading  = ref(false)
const error    = ref('')

async function submit() {
  loading.value = true
  error.value   = ''
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
