import { defineStore } from 'pinia'
import { ref } from 'vue'
import { fetchCarFilters, fetchCarsPage } from '../api/cars.js'

export const useCarsStore = defineStore('cars', () => {
  const cars       = ref([])
  const meta       = ref({})
  const filterOpts = ref({ brands: [], years: [], transmissions: [], price_bounds: { min_price: 0, max_price: 0 } })

  async function fetchCars(params = {}) {
    const response = await fetchCarsPage(params)
    cars.value = response.data
    meta.value = response.meta
  }

  async function fetchFilterOptions() {
    filterOpts.value = await fetchCarFilters()
  }

  return { cars, meta, filterOpts, fetchCars, fetchFilterOptions }
})
