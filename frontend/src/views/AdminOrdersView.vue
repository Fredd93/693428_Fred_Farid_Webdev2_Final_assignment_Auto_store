<template>
  <div class="max-w-7xl mx-auto px-6 py-10">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-white">All Orders</h1>
      <button @click="exportCsv"
        class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
        Export CSV
      </button>
    </div>

    <div v-if="loading" class="text-gray-400">Loading...</div>
    <table v-else class="w-full text-sm text-left text-gray-300">
      <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
        <tr>
          <th class="px-4 py-3">#</th>
          <th class="px-4 py-3">Client</th>
          <th class="px-4 py-3">Car</th>
          <th class="px-4 py-3">Type</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3">Date</th>
          <th class="px-4 py-3">Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="o in orders" :key="o.id" class="border-b border-gray-800 hover:bg-gray-900">
          <td class="px-4 py-3">{{ o.id }}</td>
          <td class="px-4 py-3">{{ o.client_name }}</td>
          <td class="px-4 py-3">{{ o.brand }} {{ o.model }}</td>
          <td class="px-4 py-3 capitalize">{{ o.order_type }}</td>
          <td class="px-4 py-3"><StatusBadge :status="o.status" /></td>
          <td class="px-4 py-3">{{ new Date(o.created_at).toLocaleDateString() }}</td>
          <td class="px-4 py-3">
            <select @change="e => updateStatus(o.id, e.target.value)" :value="o.status"
              class="bg-gray-800 border border-gray-700 text-white rounded px-2 py-1 text-xs">
              <option>pending</option>
              <option>approved</option>
              <option>denied</option>
              <option>completed</option>
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

const orders  = ref([])
const meta    = ref({})
const loading = ref(true)

async function load(page = 1) {
  loading.value = true
  try {
    const { data } = await client.get('/orders', { params: { page, limit: 15 } })
    orders.value  = data.data
    meta.value    = data.meta
  } catch {
    orders.value = []
  } finally {
    loading.value = false
  }
}

async function updateStatus(id, status) {
  try {
    await client.put(`/orders/${id}`, { status })
    await load()
  } catch (e) {
    alert(e.response?.data?.error ?? 'Failed to update order status')
  }
}

async function exportCsv() {
  try {
    const { data } = await client.get('/orders/export', { responseType: 'blob' })
    const url  = URL.createObjectURL(new Blob([data], { type: 'text/csv' }))
    const link = document.createElement('a')
    link.href  = url
    link.download = `orders_${new Date().toISOString().slice(0, 10)}.csv`
    link.click()
    URL.revokeObjectURL(url)
  } catch {
    alert('Export failed. Please try again.')
  }
}

onMounted(() => load())
</script>
