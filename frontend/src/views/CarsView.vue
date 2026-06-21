<template>
  <section class="site-container px-1 py-10">
    <div class="mb-10">
      <div class="section-eyebrow">Current stock</div>
      <h1 class="section-title max-w-3xl text-[clamp(2.2rem,5vw,4.2rem)]">
        Browse the live inventory in the new showroom design
      </h1>
      <p class="section-copy mt-4 max-w-3xl text-lg">
        Same backend data, updated presentation. Filter by make, year, transmission, price, and sale status.
      </p>
    </div>

    <div class="glass-panel rounded-[26px] p-5">
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <select v-model="filters.brand" @change="load(1)" class="inventory-select rounded-xl px-4 py-3 text-sm">
          <option value="">Any Make</option>
          <option v-for="b in filterOpts.brands" :key="b">{{ b }}</option>
        </select>

        <select v-model="filters.year" @change="load(1)" class="inventory-select rounded-xl px-4 py-3 text-sm">
          <option value="">Any Year</option>
          <option v-for="y in filterOpts.years" :key="y">{{ y }}</option>
        </select>

        <select v-model="filters.transmission" @change="load(1)" class="inventory-select rounded-xl px-4 py-3 text-sm">
          <option value="">Any Transmission</option>
          <option v-for="t in filterOpts.transmissions" :key="t">{{ t }}</option>
        </select>

        <input v-model="filters.max_price" @change="load(1)" type="number" placeholder="Max price" class="inventory-input rounded-xl px-4 py-3 text-sm" />

        <label class="inventory-chip flex items-center justify-center gap-3 rounded-xl px-4 py-3 text-sm font-medium">
          <input v-model="filters.on_sale" @change="load(1)" type="checkbox" class="accent-blue-600" />
          On Sale only
        </label>
      </div>
    </div>

    <div class="mt-10 flex items-center justify-between gap-4">
      <p class="text-sm font-medium text-slate-600">{{ meta.total }} vehicles found</p>
    </div>

    <div v-if="loading" class="mt-8 text-sm text-slate-500">Loading inventory...</div>
    <div v-else class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
      <CarCard v-for="car in cars" :key="car.id" :car="car" />
    </div>

    <Pagination :meta="meta" @change="load" />
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import CarCard from '../components/CarCard.vue'
import Pagination from '../components/Pagination.vue'
import { useCarsStore } from '../stores/cars.js'

const carsStore = useCarsStore()
const { cars, meta, filterOpts } = storeToRefs(carsStore)
const filters = ref({ brand: '', year: '', transmission: '', max_price: '', on_sale: false })
const loading = ref(true)

async function load(page = 1) {
  loading.value = true
  try {
    await carsStore.fetchCars({
      page,
      limit: 12,
      ...filters.value,
    })
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await carsStore.fetchFilterOptions()
  await load()
})
</script>
