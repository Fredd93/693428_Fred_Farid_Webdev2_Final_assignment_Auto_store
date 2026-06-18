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
          <th class="px-4 py-3">Details</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3">Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="o in orders" :key="o.id" class="border-b border-gray-800 hover:bg-gray-900">
          <td class="px-4 py-3">{{ o.id }}</td>
          <td class="px-4 py-3">{{ o.client_name }}</td>
          <td class="px-4 py-3">{{ o.brand }} {{ o.model }}</td>
          <td class="px-4 py-3 capitalize">{{ o.order_type }}</td>
          <td class="px-4 py-3 text-xs text-gray-400">
            <template v-if="o.order_type === 'lease'">
              <span v-if="o.down_payment">↓ €{{ Number(o.down_payment).toLocaleString() }}</span>
              <span v-if="o.lease_years" class="ml-1">· {{ o.lease_years }}mo</span>
            </template>
            <span v-else>—</span>
          </td>
          <td class="px-4 py-3"><StatusBadge :status="o.status" /></td>
          <td class="px-4 py-3">
            <button @click="openReview(o)"
              class="text-xs bg-gray-700 hover:bg-gray-600 text-white px-3 py-1 rounded">
              Review
            </button>
          </td>
        </tr>
      </tbody>
    </table>
    <Pagination :meta="meta" @change="load" />

    <!-- Review modal -->
    <div v-if="reviewing" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
      <div class="bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-lg">
        <h2 class="text-white font-bold text-lg mb-1">Review Order #{{ reviewing.id }}</h2>
        <p class="text-gray-400 text-sm mb-4">
          {{ reviewing.client_name }} — {{ reviewing.brand }} {{ reviewing.model }}
          <span class="capitalize ml-1">({{ reviewing.order_type }})</span>
        </p>

        <div class="space-y-4 text-sm">
          <!-- Status -->
          <div>
            <label class="text-gray-400 block mb-1">Decision</label>
            <select v-model="form.status"
              class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2">
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="denied">Denied</option>
              <option value="completed">Completed</option>
            </select>
          </div>

          <!-- Final price (purchase approval) -->
          <div v-if="form.status === 'approved' && reviewing.order_type === 'purchase'">
            <label class="text-gray-400 block mb-1">Agreed Price (€)</label>
            <input v-model="form.final_price" type="number" min="0" step="0.01"
              class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
          </div>

          <!-- Lease fields (lease approval) -->
          <template v-if="reviewing.order_type === 'lease'">
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="text-gray-400 block mb-1">Down Payment (€)</label>
                <input v-model="form.down_payment" type="number" min="0" step="0.01"
                  class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
              </div>
              <div>
                <label class="text-gray-400 block mb-1">Term</label>
                <select v-model="form.lease_years"
                  class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2">
                  <option :value="null">— unchanged —</option>
                  <option :value="24">24 months</option>
                  <option :value="36">36 months</option>
                  <option :value="48">48 months</option>
                  <option :value="60">60 months</option>
                </select>
              </div>
            </div>
          </template>

          <!-- Reason -->
          <div>
            <label class="text-gray-400 block mb-1">
              {{ form.status === 'denied' ? 'Reason for denial *' : 'Note to client (optional)' }}
            </label>
            <textarea v-model="form.reason" rows="3"
              :placeholder="form.status === 'denied' ? 'Explain why the request was denied...' : 'Any additional information for the client...'"
              class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500 resize-none"></textarea>
          </div>

          <p v-if="formErr" class="text-red-400">{{ formErr }}</p>

          <div class="flex gap-3 pt-1">
            <button @click="submitReview" :disabled="saving"
              class="flex-1 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white py-2 rounded-lg font-semibold">
              {{ saving ? 'Saving...' : 'Confirm' }}
            </button>
            <button @click="reviewing = null"
              class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-2 rounded-lg">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import StatusBadge from '../components/StatusBadge.vue'
import Pagination  from '../components/Pagination.vue'
import client from '../api/client.js'

const orders    = ref([])
const meta      = ref({})
const loading   = ref(true)
const reviewing = ref(null)
const saving    = ref(false)
const formErr   = ref('')
const form      = ref({})

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

function openReview(order) {
  reviewing.value = order
  formErr.value   = ''
  form.value = {
    status:       order.status,
    reason:       order.reason       ?? '',
    final_price:  order.final_price  ?? '',
    down_payment: order.down_payment ?? '',
    lease_years:  order.lease_years  ?? null,
  }
}

async function submitReview() {
  if (form.value.status === 'denied' && !form.value.reason?.trim()) {
    formErr.value = 'A reason is required when denying a request.'
    return
  }
  saving.value  = true
  formErr.value = ''
  try {
    await client.put(`/orders/${reviewing.value.id}`, {
      status:       form.value.status,
      reason:       form.value.reason       || null,
      final_price:  form.value.final_price  || null,
      down_payment: form.value.down_payment || null,
      lease_years:  form.value.lease_years  || null,
    })
    reviewing.value = null
    await load()
  } catch (e) {
    formErr.value = e.response?.data?.error ?? 'Failed to update order'
  } finally {
    saving.value = false
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
