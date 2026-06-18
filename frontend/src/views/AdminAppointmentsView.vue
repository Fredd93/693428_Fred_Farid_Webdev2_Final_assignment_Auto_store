<template>
  <div class="max-w-7xl mx-auto px-6 py-10">
    <h1 class="text-2xl font-bold text-white mb-6">Appointments</h1>

    <div v-if="loading" class="text-gray-400">Loading...</div>
    <table v-else class="w-full text-sm text-left text-gray-300">
      <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
        <tr>
          <th class="px-4 py-3">#</th>
          <th class="px-4 py-3">Client</th>
          <th class="px-4 py-3">Phone</th>
          <th class="px-4 py-3">Car</th>
          <th class="px-4 py-3">Date &amp; Time</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3">Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="a in appointments" :key="a.appointment_id" class="border-b border-gray-800 hover:bg-gray-900">
          <td class="px-4 py-3">{{ a.appointment_id }}</td>
          <td class="px-4 py-3">
            <p class="text-white">{{ a.client_name }}</p>
            <p class="text-gray-500 text-xs">{{ a.client_email }}</p>
          </td>
          <td class="px-4 py-3">{{ a.client_phone || '—' }}</td>
          <td class="px-4 py-3">{{ a.brand }} {{ a.model }}</td>
          <td class="px-4 py-3">{{ formatDate(a.appointment_date) }}</td>
          <td class="px-4 py-3"><StatusBadge :status="a.status" /></td>
          <td class="px-4 py-3">
            <select @change="e => updateStatus(a.appointment_id, e.target.value)" :value="a.status"
              class="bg-gray-800 border border-gray-700 text-white rounded px-2 py-1 text-xs">
              <option>pending</option>
              <option>confirmed</option>
              <option>cancelled</option>
            </select>
          </td>
        </tr>
      </tbody>
    </table>
    <Pagination :meta="meta" @change="load" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import StatusBadge from '../components/StatusBadge.vue'
import Pagination  from '../components/Pagination.vue'
import client from '../api/client.js'

const appointments = ref([])
const meta         = ref({})
const loading      = ref(true)

function formatDate(dt) {
  return new Date(dt).toLocaleString('en-GB', { dateStyle: 'medium', timeStyle: 'short' })
}

async function load(page = 1) {
  loading.value = true
  const { data } = await client.get('/appointments', { params: { page, limit: 15 } })
  appointments.value = data.data
  meta.value         = data.meta
  loading.value      = false
}

async function updateStatus(id, status) {
  await client.put(`/appointments/${id}`, { status })
  await load()
}

onMounted(() => load())
</script>
