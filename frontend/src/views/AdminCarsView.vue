<template>
  <div class="max-w-7xl mx-auto px-6 py-10">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-white">Car Inventory</h1>
      <button @click="openAdd" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">+ Add Car</button>
    </div>

    <div v-if="loading" class="text-gray-400">Loading...</div>
    <table v-else class="w-full text-sm text-left text-gray-300">
      <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
        <tr>
          <th class="px-4 py-3">Car</th>
          <th class="px-4 py-3">Year</th>
          <th class="px-4 py-3">Price</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="car in cars" :key="car.id" class="border-b border-gray-800 hover:bg-gray-900">
          <td class="px-4 py-3 font-medium text-white">{{ car.brand }} {{ car.model }}</td>
          <td class="px-4 py-3">{{ car.year }}</td>
          <td class="px-4 py-3">€{{ Number(car.price).toLocaleString() }}</td>
          <td class="px-4 py-3 capitalize">{{ car.status }}</td>
          <td class="px-4 py-3 flex gap-2">
            <button @click="openEdit(car)" class="text-blue-400 hover:text-blue-300">Edit</button>
            <button @click="deleteCar(car.id)" class="text-red-400 hover:text-red-300">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
    <Pagination :meta="meta" @change="load" />

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
      <div class="bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <h2 class="text-white font-bold text-lg mb-4">{{ editing ? 'Edit Car' : 'Add Car' }}</h2>
        <form @submit.prevent="submitCar" class="space-y-3 text-sm">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="text-gray-400 block mb-1">Brand</label>
              <input v-model="form.brand" required class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2" />
            </div>
            <div>
              <label class="text-gray-400 block mb-1">Model</label>
              <input v-model="form.model" required class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2" />
            </div>
            <div>
              <label class="text-gray-400 block mb-1">Year</label>
              <input v-model="form.year" type="number" required class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2" />
            </div>
            <div>
              <label class="text-gray-400 block mb-1">Transmission</label>
              <input v-model="form.transmission" required class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2" />
            </div>
            <div>
              <label class="text-gray-400 block mb-1">Price (€)</label>
              <input v-model="form.price" type="number" required class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2" />
            </div>
            <div>
              <label class="text-gray-400 block mb-1">Status</label>
              <select v-model="form.status" class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2">
                <option>available</option><option>sold</option><option>reserved</option>
              </select>
            </div>
          </div>
          <div>
            <label class="text-gray-400 block mb-1">Description</label>
            <textarea v-model="form.description" rows="2" class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2"></textarea>
          </div>
          <div>
            <label class="text-gray-400 block mb-1">Image</label>
            <input type="file" @change="e => form.imageFile = e.target.files[0]" accept="image/*" class="text-gray-300" />
          </div>
          <div class="flex gap-3 mt-2">
            <label class="flex items-center gap-2 text-gray-300">
              <input v-model="form.on_sale" type="checkbox" class="accent-red-500" /> On Sale
            </label>
            <label class="flex items-center gap-2 text-gray-300">
              <input v-model="form.lease_available" type="checkbox" class="accent-red-500" /> Lease Available
            </label>
          </div>
          <p v-if="formErr" class="text-red-400">{{ formErr }}</p>
          <div class="flex gap-3 pt-2">
            <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg">Save</button>
            <button type="button" @click="showModal = false" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-2 rounded-lg">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Pagination from '../components/Pagination.vue'
import client from '../api/client.js'

const cars      = ref([])
const meta      = ref({})
const loading   = ref(true)
const showModal = ref(false)
const editing   = ref(null)
const formErr   = ref('')
const form      = ref({})

async function load(page = 1) {
  loading.value = true
  const { data } = await client.get('/cars', { params: { page, limit: 15 } })
  cars.value    = data.data
  meta.value    = data.meta
  loading.value = false
}

function openAdd() {
  editing.value   = null
  form.value      = { brand:'', model:'', year:'', transmission:'', price:'', status:'available', description:'', on_sale:false, lease_available:false, imageFile:null }
  showModal.value = true
}

function openEdit(car) {
  editing.value   = car.id
  form.value      = { ...car, on_sale: !!car.on_sale, lease_available: !!car.lease_available, imageFile: null }
  showModal.value = true
}

async function deleteCar(id) {
  if (!confirm('Delete this car?')) return
  await client.delete(`/cars/${id}`)
  await load()
}

async function submitCar() {
  formErr.value = ''
  const fd = new FormData()
  Object.entries(form.value).forEach(([k, v]) => {
    if (k === 'imageFile') { if (v) fd.append('image_path', v) }
    else fd.append(k, v === true ? 'yes' : v === false ? 'no' : v)
  })
  try {
    if (editing.value) {
      await client.put(`/cars/${editing.value}`, fd, { headers: { 'Content-Type': 'multipart/form-data' } })
    } else {
      await client.post('/cars', fd, { headers: { 'Content-Type': 'multipart/form-data' } })
    }
    showModal.value = false
    await load()
  } catch (e) {
    formErr.value = e.response?.data?.error ?? 'Save failed'
  }
}

onMounted(() => load())
</script>
