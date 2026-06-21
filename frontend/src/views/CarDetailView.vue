<template>
  <section class="site-container px-1 py-10">
    <div v-if="loading" class="site-status-message">Loading vehicle...</div>
    <div v-else-if="!car" class="site-status-message error">Car not found.</div>
    <template v-else>
      <div class="detail-hero">
        <ImageCarousel
          :images="car.images?.length ? car.images : [car.image_path]"
          :alt="`${car.brand} ${car.model}`"
          aspect-ratio="4/3"
        />

        <div class="glass-panel rounded-[28px] p-7">
          <div class="section-eyebrow">Live inventory</div>
          <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-950">{{ car.brand }} {{ car.model }}</h1>

          <div class="mt-4 flex flex-wrap gap-2">
            <span class="inventory-chip rounded-full px-3 py-1 text-xs font-semibold">{{ car.year }}</span>
            <span class="inventory-chip rounded-full px-3 py-1 text-xs font-semibold">{{ car.transmission }}</span>
            <span class="inventory-chip rounded-full px-3 py-1 text-xs font-semibold">{{ car.color }}</span>
            <span class="inventory-chip rounded-full px-3 py-1 text-xs font-semibold capitalize">{{ car.status }}</span>
          </div>

          <p class="section-copy mt-5 text-base">{{ car.description }}</p>
          <div class="mt-6 text-3xl font-black tracking-tight text-slate-950">
            EUR {{ Number(car.price).toLocaleString() }}
          </div>

          <div class="mt-6 flex flex-col gap-3 sm:flex-row">
            <button class="site-btn-primary flex-1 px-5 py-3 text-sm font-semibold" @click="placeOrder('purchase')">
              Buy Now
            </button>
            <button
              v-if="car.lease_available"
              class="site-btn-secondary flex-1 px-5 py-3 text-sm font-semibold"
              @click="showLease = true"
            >
              Lease
            </button>
          </div>

          <button class="site-btn-secondary mt-3 w-full px-5 py-3 text-sm font-semibold" @click="showAppt = true">
            Book a Test Drive
          </button>

          <p v-if="orderMsg" class="site-status-message success mt-4">{{ orderMsg }}</p>
          <p v-if="orderErr" class="site-status-message error mt-4">{{ orderErr }}</p>
        </div>
      </div>

      <div class="glass-panel mt-8 rounded-[28px] p-7">
        <div class="section-eyebrow">Vehicle details</div>
        <h2 class="mt-3 text-2xl font-extrabold tracking-tight text-slate-950">Everything you need before booking</h2>

        <div class="detail-spec-grid mt-6 text-sm">
          <div v-if="car.engine_spec" class="detail-spec-row">
            <span class="text-slate-500">Engine</span>
            <span class="font-semibold text-slate-900">{{ car.engine_spec }}</span>
          </div>
          <div class="detail-spec-row">
            <span class="text-slate-500">Transmission</span>
            <span class="font-semibold text-slate-900">{{ car.transmission }}</span>
          </div>
          <div v-if="car.car_condition" class="detail-spec-row">
            <span class="text-slate-500">Condition</span>
            <span class="font-semibold text-slate-900">{{ car.car_condition }}</span>
          </div>
          <div class="detail-spec-row">
            <span class="text-slate-500">Color</span>
            <span class="font-semibold text-slate-900">{{ car.color }}</span>
          </div>
          <div class="detail-spec-row">
            <span class="text-slate-500">Year</span>
            <span class="font-semibold text-slate-900">{{ car.year }}</span>
          </div>
          <div class="detail-spec-row">
            <span class="text-slate-500">Status</span>
            <span
              class="capitalize font-semibold"
              :class="car.status === 'available' ? 'text-emerald-700' : car.status === 'sold' ? 'text-red-700' : 'text-amber-700'"
            >
              {{ car.status }}
            </span>
          </div>
          <div class="detail-spec-row">
            <span class="text-slate-500">Lease Available</span>
            <span class="font-semibold" :class="car.lease_available ? 'text-emerald-700' : 'text-slate-500'">
              {{ car.lease_available ? 'Yes' : 'No' }}
            </span>
          </div>
          <div v-if="car.on_sale" class="detail-spec-row">
            <span class="text-slate-500">Discount</span>
            <span class="font-semibold text-red-700">{{ car.discount }}%</span>
          </div>
        </div>

        <div v-if="car.lease_available && car.lease_terms" class="mt-6 rounded-2xl border border-white/70 bg-white/60 px-5 py-4">
          <p class="text-sm font-semibold text-slate-500">Lease Terms</p>
          <p class="mt-1 text-sm text-slate-800">{{ car.lease_terms }}</p>
        </div>
      </div>
    </template>

    <div v-if="showLease" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/45 p-4 backdrop-blur-sm">
      <div class="glass-panel w-full max-w-md rounded-[28px] p-6">
        <h2 class="text-xl font-extrabold tracking-tight text-slate-950">Lease Request for {{ car.brand }} {{ car.model }}</h2>
        <form class="space-y-4 text-sm" @submit.prevent="submitLease">
          <div>
            <label class="auth-label">Down Payment (EUR)</label>
            <input
              v-model="leaseForm.down_payment"
              type="number"
              min="0"
              step="0.01"
              required
              class="inventory-input w-full rounded-xl px-3 py-2"
            />
          </div>
          <div>
            <label class="auth-label">Lease Term</label>
            <select v-model="leaseForm.lease_years" required class="inventory-select w-full rounded-xl px-3 py-2">
              <option :value="24">24 months</option>
              <option :value="36">36 months</option>
              <option :value="48">48 months</option>
              <option :value="60">60 months</option>
            </select>
          </div>
          <p v-if="orderErr" class="site-status-message error">{{ orderErr }}</p>
          <p v-if="orderMsg" class="site-status-message success">{{ orderMsg }}</p>
          <div class="flex gap-3 pt-1">
            <button type="submit" class="site-btn-primary flex-1 px-4 py-2 font-semibold">Submit Request</button>
            <button type="button" class="site-btn-secondary flex-1 px-4 py-2 font-semibold" @click="showLease = false">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>

    <div v-if="showAppt" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/45 p-4 backdrop-blur-sm">
      <div class="glass-panel w-full max-w-md rounded-[28px] p-6">
        <h2 class="text-xl font-extrabold tracking-tight text-slate-950">Book a Test Drive</h2>
        <form class="space-y-4 text-sm" @submit.prevent="bookAppointment">
          <template v-if="!auth.isLoggedIn">
            <div>
              <label class="auth-label">Full Name</label>
              <input v-model="apptForm.name" type="text" required class="inventory-input w-full rounded-xl px-3 py-2" />
            </div>
            <div>
              <label class="auth-label">Email</label>
              <input v-model="apptForm.email" type="email" required class="inventory-input w-full rounded-xl px-3 py-2" />
            </div>
          </template>
          <div>
            <label class="auth-label">Preferred Date &amp; Time</label>
            <input
              v-model="apptForm.date"
              type="datetime-local"
              required
              class="inventory-input w-full rounded-xl px-3 py-2"
            />
          </div>
          <div>
            <label class="auth-label">Phone Number</label>
            <input
              v-model="apptForm.phone"
              type="tel"
              placeholder="+31 6 00000000"
              class="inventory-input w-full rounded-xl px-3 py-2"
            />
          </div>
          <p v-if="apptErr" class="site-status-message error">{{ apptErr }}</p>
          <p v-if="apptMsg" class="site-status-message success">{{ apptMsg }}</p>
          <div class="flex gap-3 pt-1">
            <button
              type="submit"
              :disabled="apptSaving"
              class="site-btn-primary flex-1 px-4 py-2 font-semibold disabled:opacity-50"
            >
              {{ apptSaving ? 'Booking...' : 'Confirm Booking' }}
            </button>
            <button type="button" class="site-btn-secondary flex-1 px-4 py-2 font-semibold" @click="showAppt = false">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'
import ImageCarousel from '../components/ImageCarousel.vue'
import client from '../api/client.js'
import { fetchCarById } from '../api/cars.js'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()
const car = ref(null)
const loading = ref(true)
const orderMsg = ref('')
const orderErr = ref('')

const showLease = ref(false)
const leaseForm = ref({ down_payment: '', lease_years: 36 })

async function submitLease() {
  if (!auth.isLoggedIn) {
    router.push('/login')
    return
  }

  orderErr.value = ''
  orderMsg.value = ''
  try {
    await client.post('/orders', {
      car_id: car.value.id,
      order_type: 'lease',
      down_payment: leaseForm.value.down_payment,
      lease_years: leaseForm.value.lease_years,
    })
    orderMsg.value = 'Lease request submitted! We will review and get back to you.'
    showLease.value = false
    leaseForm.value = { down_payment: '', lease_years: 36 }
  } catch (e) {
    orderErr.value = e.response?.data?.error ?? 'Failed to submit lease request'
  }
}

const showAppt = ref(false)
const apptForm = ref({ name: '', email: '', date: '', phone: '' })
const apptSaving = ref(false)
const apptMsg = ref('')
const apptErr = ref('')

onMounted(async () => {
  try {
    car.value = await fetchCarById(route.params.id)
  } catch {
    car.value = null
  }
  loading.value = false
})

async function bookAppointment() {
  apptSaving.value = true
  apptErr.value = ''
  apptMsg.value = ''
  try {
    await client.post('/appointments', {
      car_id: car.value.id,
      appointment_date: apptForm.value.date,
      client_phone: apptForm.value.phone,
      client_name: apptForm.value.name,
      client_email: apptForm.value.email,
    })
    apptMsg.value = 'Test drive booked! We will confirm shortly.'
    apptForm.value = { name: '', email: '', date: '', phone: '' }
  } catch (e) {
    apptErr.value = e.response?.data?.error ?? 'Booking failed.'
  } finally {
    apptSaving.value = false
  }
}

async function placeOrder(type) {
  if (!auth.isLoggedIn) {
    router.push('/login')
    return
  }

  try {
    await client.post('/orders', { car_id: car.value.id, order_type: type })
    orderMsg.value = `${type === 'purchase' ? 'Purchase' : 'Lease'} request submitted!`
  } catch (e) {
    orderErr.value = e.response?.data?.error ?? 'Failed to place order'
  }
}
</script>
