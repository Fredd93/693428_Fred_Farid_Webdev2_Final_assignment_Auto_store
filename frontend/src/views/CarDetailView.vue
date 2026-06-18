<template>
  <div class="max-w-5xl mx-auto px-6 py-10">
    <div v-if="loading" class="text-gray-400">Loading...</div>
    <div v-else-if="!car" class="text-red-400">Car not found.</div>
    <template v-else>
      <div class="grid md:grid-cols-2 gap-10">
        <img :src="`/${car.image_path}`" :alt="`${car.brand} ${car.model}`"
          class="w-full rounded-xl object-cover" />

        <div>
          <h1 class="text-3xl font-bold text-white mb-2">{{ car.brand }} {{ car.model }}</h1>
          <p class="text-gray-400 mb-4">{{ car.year }} · {{ car.transmission }} · {{ car.color }}</p>
          <p class="text-gray-300 mb-6">{{ car.description }}</p>

          <div class="text-2xl font-bold text-red-400 mb-6">€{{ Number(car.price).toLocaleString() }}</div>

          <div class="flex gap-3">
            <button @click="placeOrder('purchase')"
              class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-semibold">
              Buy Now
            </button>
            <button v-if="car.lease_available" @click="placeOrder('lease')"
              class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-3 rounded-lg font-semibold">
              Lease
            </button>
          </div>

          <p v-if="orderMsg" class="mt-4 text-green-400 text-sm">{{ orderMsg }}</p>
          <p v-if="orderErr" class="mt-4 text-red-400 text-sm">{{ orderErr }}</p>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'
import client from '../api/client.js'

const route    = useRoute()
const router   = useRouter()
const auth     = useAuthStore()
const car      = ref(null)
const loading  = ref(true)
const orderMsg = ref('')
const orderErr = ref('')

onMounted(async () => {
  try {
    const { data } = await client.get(`/cars/${route.params.id}`)
    car.value = data
  } catch { car.value = null }
  loading.value = false
})

async function placeOrder(type) {
  if (!auth.isLoggedIn) { router.push('/login'); return }
  try {
    await client.post('/orders', { car_id: car.value.id, order_type: type })
    orderMsg.value = `${type === 'purchase' ? 'Purchase' : 'Lease'} request submitted!`
  } catch (e) {
    orderErr.value = e.response?.data?.error ?? 'Failed to place order'
  }
}
</script>
