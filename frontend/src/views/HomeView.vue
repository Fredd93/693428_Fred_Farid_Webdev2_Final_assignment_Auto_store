<template>
  <section class="px-6 py-16 max-w-7xl mx-auto">
    <div class="text-center mb-12">
      <h1 class="text-5xl font-bold text-white mb-4">Find Your <span class="text-red-500">Dream Car</span></h1>
      <p class="text-gray-400 text-lg">Browse our premium selection of vehicles available for purchase or lease</p>
      <RouterLink to="/cars" class="inline-block mt-6 bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold">
        View Inventory
      </RouterLink>
    </div>

    <div v-if="loading" class="text-center text-gray-400">Loading...</div>
    <template v-else>
      <h2 class="text-2xl font-semibold text-white mb-4">Featured Cars</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <CarCard v-for="car in featured" :key="car.id" :car="car" />
      </div>
    </template>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import CarCard from '../components/CarCard.vue'
import client from '../api/client.js'

const featured = ref([])
const loading  = ref(true)

onMounted(async () => {
  const { data } = await client.get('/cars', { params: { limit: 8 } })
  featured.value = data.data
  loading.value  = false
})
</script>
