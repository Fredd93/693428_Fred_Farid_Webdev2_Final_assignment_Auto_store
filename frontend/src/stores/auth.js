import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import client from '../api/client.js'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('token') || null)
  const user  = ref(JSON.parse(localStorage.getItem('user') || 'null'))

  const isLoggedIn  = computed(() => !!token.value)
  const isAdmin     = computed(() => user.value?.role === 'admin')
  const isEmployee  = computed(() => ['admin','employee'].includes(user.value?.role))

  function _persist(t, u) {
    token.value = t
    user.value  = u
    localStorage.setItem('token', t)
    localStorage.setItem('user',  JSON.stringify(u))
  }

  async function login(email, password) {
    const { data } = await client.post('/auth/login', { email, password })
    _persist(data.token, data.user)
    return data.user
  }

  async function register(name, email, password) {
    const { data } = await client.post('/auth/register', { name, email, password })
    _persist(data.token, data.user)
    return data.user
  }

  function logout() {
    token.value = null
    user.value  = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }

  return { token, user, isLoggedIn, isAdmin, isEmployee, login, register, logout }
})
