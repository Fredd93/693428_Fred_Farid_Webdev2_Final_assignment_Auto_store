<template>
  <div class="max-w-5xl mx-auto px-6 py-10">
    <div v-if="loading" class="text-gray-400">Loading...</div>
    <div v-else-if="!car" class="text-red-400">Car not found.</div>
    <template v-else>
      <div class="grid md:grid-cols-2 gap-10">
        <ImageCarousel
          :images="car.images?.length ? car.images : [car.image_path]"
          :alt="`${car.brand} ${car.model}`"
          aspect-ratio="4/3"
        />

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

          <button @click="showAppt = true"
            class="mt-3 w-full border border-gray-600 hover:border-red-500 text-gray-300 hover:text-white py-3 rounded-lg font-semibold transition">
            Book a Test Drive
          </button>

          <p v-if="orderMsg" class="mt-4 text-green-400 text-sm">{{ orderMsg }}</p>
          <p v-if="orderErr" class="mt-4 text-red-400 text-sm">{{ orderErr }}</p>
        </div>
      </div>
    </template>

    <!-- Appointment modal -->
    <div v-if="showAppt" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
      <div class="bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-md">
        <h2 class="text-white font-bold text-lg mb-4">Book a Test Drive</h2>
        <form @submit.prevent="bookAppointment" class="space-y-4 text-sm">
          <div>
            <label class="text-gray-400 block mb-1">Preferred Date &amp; Time</label>
            <input v-model="apptForm.date" type="datetime-local" required
              class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
          </div>
          <div>
            <label class="text-gray-400 block mb-1">Phone Number</label>
            <input v-model="apptForm.phone" type="tel" placeholder="+31 6 00000000"
              class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
          </div>
          <p v-if="apptErr" class="text-red-400">{{ apptErr }}</p>
          <p v-if="apptMsg" class="text-green-400">{{ apptMsg }}</p>
          <div class="flex gap-3 pt-1">
            <button type="submit" :disabled="apptSaving"
              class="flex-1 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white py-2 rounded-lg font-semibold">
              {{ apptSaving ? 'Booking...' : 'Confirm Booking' }}
            </button>
            <button type="button" @click="showAppt = false"
              class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-2 rounded-lg">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'
import ImageCarousel from '../components/ImageCarousel.vue'
import client from '../api/client.js'

const route    = useRoute()
const router   = useRouter()
const auth     = useAuthStore()
const car      = ref(null)
const loading  = ref(true)
const orderMsg = ref('')
const orderErr = ref('')

// Appointment
const showAppt  = ref(false)
const apptForm  = ref({ date: '', phone: '' })
const apptSaving = ref(false)
const apptMsg   = ref('')
const apptErr   = ref('')

onMounted(async () => {
  try {
    const { data } = await client.get(`/cars/${route.params.id}`)
    car.value = data
  } catch { car.value = null }
  loading.value = false
})

async function bookAppointment() {
  if (!auth.isLoggedIn) { router.push('/login'); return }
  apptSaving.value = true
  apptErr.value    = ''
  apptMsg.value    = ''
  try {
    await client.post('/appointments', {
      car_id:           car.value.id,
      appointment_date: apptForm.value.date,
      client_phone:     apptForm.value.phone,
    })
    apptMsg.value   = 'Test drive booked! We will confirm shortly.'
    apptForm.value  = { date: '', phone: '' }
  } catch (e) {
    apptErr.value = e.response?.data?.error ?? 'Booking failed.'
  } finally {
    apptSaving.value = false
  }
}

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
