<template>
  <section class="px-6 py-16 max-w-7xl mx-auto space-y-12">

    <!-- Hero -->
    <div class="text-center">
      <h1 class="text-5xl font-bold text-white mb-4">Find Your <span class="text-red-500">Dream Car</span></h1>
      <p class="text-gray-400 text-lg">Browse our premium selection of vehicles available for purchase or lease</p>
      <RouterLink to="/cars" class="inline-block mt-6 bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold">
        View Inventory
      </RouterLink>
    </div>

    <!-- On-sale carousel -->
    <div>
      <h2 class="text-xl font-semibold text-white mb-3">Cars on Sale</h2>
      <SaleCarousel />
    </div>

    <!-- Featured cars grid -->
    <div>
      <h2 class="text-2xl font-semibold text-white mb-4">Featured Cars</h2>
      <div v-if="loading" class="text-gray-400">Loading...</div>
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <CarCard v-for="car in featured" :key="car.id" :car="car" />
      </div>
    </div>

    <!-- MOTD banner -->
    <div v-if="motd" class="relative bg-gray-900 border border-gray-700 rounded-xl px-6 py-4 flex items-start gap-4">
      <span class="text-red-500 text-xl mt-0.5">&#9432;</span>
      <p v-if="!editingMotd" class="text-gray-300 flex-1">{{ motd }}</p>
      <div v-else class="flex-1 flex gap-2">
        <input v-model="motdDraft" type="text"
          class="flex-1 bg-gray-800 border border-gray-700 text-white rounded px-3 py-1.5 text-sm focus:outline-none focus:border-red-500" />
        <button @click="saveMotd" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-sm">Save</button>
        <button @click="editingMotd = false" class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-1.5 rounded text-sm">Cancel</button>
      </div>
      <button v-if="auth.isEmployee && !editingMotd" @click="startEditMotd"
        class="text-gray-500 hover:text-white text-xs ml-auto shrink-0">Edit</button>
    </div>

  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth.js'
import CarCard       from '../components/CarCard.vue'
import SaleCarousel  from '../components/SaleCarousel.vue'
import client from '../api/client.js'

const auth    = useAuthStore()
const featured = ref([])
const loading  = ref(true)

// MOTD
const motd        = ref('')
const motdDraft   = ref('')
const editingMotd = ref(false)

function startEditMotd() {
  motdDraft.value   = motd.value
  editingMotd.value = true
}

async function saveMotd() {
  try {
    const { data } = await client.put('/motd', { message: motdDraft.value })
    motd.value      = data.message
    editingMotd.value = false
  } catch {
    // silently keep editing open
  }
}

onMounted(async () => {
  try {
    const [carsRes, motdRes] = await Promise.all([
      client.get('/cars', { params: { limit: 8 } }),
      client.get('/motd'),
    ])
    featured.value = carsRes.data.data
    motd.value     = motdRes.data.message
  } catch (e) {
    // featured stays empty, motd stays blank
  } finally {
    loading.value = false
  }
})
</script>
