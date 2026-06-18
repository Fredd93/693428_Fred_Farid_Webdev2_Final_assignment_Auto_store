import { defineStore } from 'pinia'
import { ref } from 'vue'
import client from '../api/client.js'

export const useCarsStore = defineStore('cars', () => {
  const cars       = ref([])
  const meta       = ref({})
  const filterOpts = ref({})

  async function fetchCars(params = {}) {
    const { data } = await client.get('/cars', { params })
    cars.value = data.data
    meta.value = data.meta
  }

  async function fetchFilterOptions() {
    const { data } = await client.get('/cars/filters')
    filterOpts.value = data
  }

  return { cars, meta, filterOpts, fetchCars, fetchFilterOptions }
})
