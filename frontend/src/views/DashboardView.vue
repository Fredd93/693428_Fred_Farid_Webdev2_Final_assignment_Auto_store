<template>
  <div class="max-w-4xl mx-auto px-6 py-10 space-y-12">

    <!-- Profile Section -->
    <section>
      <h1 class="text-3xl font-bold text-white mb-1">Welcome, {{ auth.user?.name }}</h1>
      <p class="text-gray-400 mb-6">Role: <span class="capitalize text-red-400">{{ auth.user?.role }}</span></p>

      <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
        <h2 class="text-lg font-semibold text-white mb-4">Edit Profile</h2>
        <form @submit.prevent="saveProfile" class="space-y-4 max-w-sm">
          <div>
            <label class="text-gray-400 text-sm block mb-1">Full Name</label>
            <input v-model="profileForm.name" type="text" required
              class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
          </div>
          <div>
            <label class="text-gray-400 text-sm block mb-1">Email</label>
            <input v-model="profileForm.email" type="email" required
              class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
          </div>
          <p v-if="profileErr" class="text-red-400 text-sm">{{ profileErr }}</p>
          <p v-if="profileOk"  class="text-green-400 text-sm">{{ profileOk }}</p>
          <button type="submit" :disabled="profileSaving"
            class="bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white px-5 py-2 rounded-lg font-semibold">
            {{ profileSaving ? 'Saving...' : 'Save Changes' }}
          </button>
        </form>
      </div>
    </section>

    <!-- Appointments Section -->
    <section>
      <h2 class="text-xl font-semibold text-white mb-4">My Appointments</h2>
      <div v-if="apptLoading" class="text-gray-400">Loading...</div>
      <div v-else-if="!appointments.length" class="text-gray-500">No appointments booked yet.</div>
      <div v-else class="space-y-3">
        <div v-for="a in appointments" :key="a.appointment_id"
          class="bg-gray-900 border border-gray-800 rounded-lg p-4 flex justify-between items-center">
          <div>
            <p class="text-white font-medium">{{ a.brand }} {{ a.model }}</p>
            <p class="text-gray-400 text-sm">{{ new Date(a.appointment_date).toLocaleString('en-GB', { dateStyle: 'medium', timeStyle: 'short' }) }}</p>
          </div>
          <StatusBadge :status="a.status" />
        </div>
      </div>
    </section>

    <!-- Orders Section -->
    <section>
      <h2 class="text-xl font-semibold text-white mb-4">My Orders</h2>

      <div v-if="loading" class="text-gray-400">Loading...</div>
      <div v-else-if="!orders.length" class="text-gray-500">No orders yet.</div>
      <div v-else class="space-y-3">
        <div v-for="o in orders" :key="o.id"
          class="bg-gray-900 border border-gray-800 rounded-lg p-4 flex justify-between items-center">
          <div>
            <p class="text-white font-medium">{{ o.brand }} {{ o.model }}</p>
            <p class="text-gray-400 text-sm capitalize">{{ o.order_type }} · {{ new Date(o.created_at).toLocaleDateString() }}</p>
          </div>
          <StatusBadge :status="o.status" />
        </div>
      </div>
      <Pagination :meta="meta" @change="load" />
    </section>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth.js'
import StatusBadge from '../components/StatusBadge.vue'
import Pagination  from '../components/Pagination.vue'
import client from '../api/client.js'

const auth = useAuthStore()

// Profile
const profileForm  = ref({ name: auth.user?.name ?? '', email: auth.user?.email ?? '' })
const profileSaving = ref(false)
const profileErr   = ref('')
const profileOk    = ref('')

async function saveProfile() {
  profileSaving.value = true
  profileErr.value    = ''
  profileOk.value     = ''
  try {
    const { data } = await client.put(`/users/${auth.user.id}`, {
      name:  profileForm.value.name,
      email: profileForm.value.email,
    })
    // Sync updated values back into the Pinia store + localStorage
    auth.user.name  = data.name
    auth.user.email = data.email
    localStorage.setItem('user', JSON.stringify(auth.user))
    profileOk.value = 'Profile updated successfully.'
  } catch (e) {
    profileErr.value = e.response?.data?.error ?? 'Failed to update profile.'
  } finally {
    profileSaving.value = false
  }
}

// Appointments
const appointments = ref([])
const apptLoading  = ref(true)

async function loadAppointments() {
  try {
    const { data } = await client.get('/appointments', { params: { limit: 10 } })
    appointments.value = data.data
  } catch {
    appointments.value = []
  } finally {
    apptLoading.value = false
  }
}

// Orders
const orders  = ref([])
const meta    = ref({})
const loading = ref(true)

async function load(page = 1) {
  try {
    const { data } = await client.get('/orders', { params: { page, limit: 10 } })
    orders.value  = data.data
    meta.value    = data.meta
  } catch {
    orders.value = []
  } finally {
    loading.value = false
  }
}

onMounted(() => { load(); loadAppointments() })
</script>
