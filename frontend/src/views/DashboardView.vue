<template>
  <div class="max-w-4xl mx-auto px-6 py-10">
    <h1 class="text-3xl font-bold text-white mb-2">Welcome, {{ auth.user?.name }}</h1>
    <p class="text-gray-400 mb-8">Role: <span class="capitalize text-red-400">{{ auth.user?.role }}</span></p>

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
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth.js'
import StatusBadge from '../components/StatusBadge.vue'
import Pagination  from '../components/Pagination.vue'
import client from '../api/client.js'

const auth    = useAuthStore()
const orders  = ref([])
const meta    = ref({})
const loading = ref(true)

async function load(page = 1) {
  const { data } = await client.get('/orders', { params: { page, limit: 10 } })
  orders.value  = data.data
  meta.value    = data.meta
  loading.value = false
}

onMounted(() => load())
</script>
