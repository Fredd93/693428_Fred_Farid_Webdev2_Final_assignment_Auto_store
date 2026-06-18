<template>
  <div class="max-w-7xl mx-auto px-6 py-10 flex gap-8">
    <!-- Sidebar filters -->
    <aside class="w-64 shrink-0">
      <h2 class="text-white font-semibold mb-4">Filter</h2>

      <div class="space-y-4 text-sm">
        <div>
          <label class="text-gray-400 block mb-1">Brand</label>
          <select v-model="filters.brand" @change="load(1)"
            class="w-full bg-gray-800 border border-gray-700 text-white rounded px-2 py-1">
            <option value="">All</option>
            <option v-for="b in filterOpts.brands" :key="b">{{ b }}</option>
          </select>
        </div>

        <div>
          <label class="text-gray-400 block mb-1">Year</label>
          <select v-model="filters.year" @change="load(1)"
            class="w-full bg-gray-800 border border-gray-700 text-white rounded px-2 py-1">
            <option value="">All</option>
            <option v-for="y in filterOpts.years" :key="y">{{ y }}</option>
          </select>
        </div>

        <div>
          <label class="text-gray-400 block mb-1">Transmission</label>
          <select v-model="filters.transmission" @change="load(1)"
            class="w-full bg-gray-800 border border-gray-700 text-white rounded px-2 py-1">
            <option value="">All</option>
            <option v-for="t in filterOpts.transmissions" :key="t">{{ t }}</option>
          </select>
        </div>

        <div>
          <label class="text-gray-400 block mb-1">Max Price (€)</label>
          <input v-model="filters.max_price" @change="load(1)" type="number"
            class="w-full bg-gray-800 border border-gray-700 text-white rounded px-2 py-1" />
        </div>

        <label class="flex items-center gap-2 text-gray-300 cursor-pointer">
          <input v-model="filters.on_sale" @change="load(1)" type="checkbox" class="accent-red-500" />
          On Sale only
        </label>
      </div>
    </aside>

    <!-- Grid -->
    <div class="flex-1">
      <div v-if="loading" class="text-gray-400">Loading...</div>
      <template v-else>
        <p class="text-gray-400 text-sm mb-4">{{ meta.total }} vehicles found</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <CarCard v-for="car in cars" :key="car.id" :car="car" />
        </div>
        <Pagination :meta="meta" @change="load" />
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import CarCard    from '../components/CarCard.vue'
import Pagination from '../components/Pagination.vue'
import client from '../api/client.js'

const cars       = ref([])
const meta       = ref({})
const filterOpts = ref({ brands: [], years: [], transmissions: [] })
const filters    = ref({ brand: '', year: '', transmission: '', max_price: '', on_sale: false })
const loading    = ref(true)

async function load(page = 1) {
  loading.value = true
  const params = { page, limit: 12 }
  if (filters.value.brand)        params.brand        = filters.value.brand
  if (filters.value.year)         params.year         = filters.value.year
  if (filters.value.transmission) params.transmission = filters.value.transmission
  if (filters.value.max_price)    params.max_price    = filters.value.max_price
  if (filters.value.on_sale)      params.on_sale      = 1
  const { data } = await client.get('/cars', { params })
  cars.value    = data.data
  meta.value    = data.meta
  loading.value = false
}

onMounted(async () => {
  const { data } = await client.get('/cars/filters')
  filterOpts.value = data
  await load()
})
</script>
