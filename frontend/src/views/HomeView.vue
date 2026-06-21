<template>
  <section class="site-container px-1 py-10">
    <div class="grid gap-8 lg:grid-cols-[1fr_1.1fr] lg:items-center">
      <div class="space-y-6">
        <div class="section-eyebrow">Grand Transmission Auto</div>
        <h1 class="section-title">
          Find Your <span class="site-link-accent">Dream Car</span>
        </h1>
        <p class="section-copy max-w-xl text-lg">
          Browse our premium selection of purchase and lease-ready vehicles with the same live inventory data behind the scenes.
        </p>
        <div class="flex flex-wrap gap-3">
          <RouterLink to="/cars" class="site-btn-primary px-6 py-3 text-sm font-semibold">
            Browse stock
          </RouterLink>
          <RouterLink to="/dashboard" class="site-btn-secondary px-6 py-3 text-sm font-semibold">
            Dashboard
          </RouterLink>
        </div>
      </div>

      <div class="glass-panel w-full max-w-[760px] justify-self-center rounded-[28px] p-5">
        <SaleCarousel />
      </div>
    </div>

    <div v-if="motd" class="glass-panel mt-8 rounded-2xl px-6 py-4">
      <div v-if="!editingMotd" class="flex items-start gap-4">
        <p class="flex-1 text-sm font-semibold tracking-wide text-slate-800">{{ motd }}</p>
        <button v-if="auth.isEmployee" @click="startEditMotd" class="text-xs font-semibold text-blue-600">Edit</button>
      </div>
      <div v-else class="flex flex-col gap-3 md:flex-row">
        <input v-model="motdDraft" type="text" class="inventory-input min-w-0 flex-1 rounded-xl px-4 py-2 text-sm" />
        <button @click="saveMotd" class="site-btn-primary px-4 py-2 text-sm font-semibold">Save</button>
        <button @click="editingMotd = false" class="site-btn-secondary px-4 py-2 text-sm font-semibold">Cancel</button>
      </div>
    </div>

    <div class="mt-14 flex items-end justify-between gap-4">
      <div>
        <div class="section-eyebrow">Current stock</div>
        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Featured cars</h2>
      </div>
      <RouterLink to="/cars" class="text-sm font-semibold text-blue-600">View all</RouterLink>
    </div>

    <div v-if="loading" class="mt-8 text-sm text-slate-500">Loading inventory...</div>
    <div v-else class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
      <CarCard v-for="car in featured" :key="car.id" :car="car" />
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth.js'
import CarCard from '../components/CarCard.vue'
import SaleCarousel from '../components/SaleCarousel.vue'
import client from '../api/client.js'
import { fetchCarsPage } from '../api/cars.js'

const auth = useAuthStore()
const featured = ref([])
const loading = ref(true)
const motd = ref('')
const motdDraft = ref('')
const editingMotd = ref(false)

function startEditMotd() {
  motdDraft.value = motd.value
  editingMotd.value = true
}

async function saveMotd() {
  try {
    const { data } = await client.put('/motd', { message: motdDraft.value })
    motd.value = data.message
    editingMotd.value = false
  } catch {}
}

onMounted(async () => {
  try {
    const [{ data: cars }, motdRes] = await Promise.all([
      fetchCarsPage({ limit: 8 }),
      client.get('/motd').catch(() => ({ data: { message: '' } })),
    ])
    featured.value = cars
    motd.value = motdRes.data.message ?? ''
  } catch {
    featured.value = []
  } finally {
    loading.value = false
  }
})
</script>
