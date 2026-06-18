<template>
  <div v-if="slides.length" class="relative w-full overflow-hidden rounded-xl"
    @mouseenter="pause" @mouseleave="resume">

    <!-- Slides -->
    <div class="flex transition-transform duration-500 ease-in-out"
      :style="{ transform: `translateX(-${current * 100}%)` }">
      <RouterLink
        v-for="car in slides" :key="car.id"
        :to="`/cars/${car.id}`"
        class="relative min-w-full h-72 md:h-96 block group">
        <img
          :src="`/${car.thumbnail || car.image_path}`"
          :alt="`${car.brand} ${car.model}`"
          class="w-full h-full object-cover" />
        <!-- Gradient overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent" />
        <!-- Info -->
        <div class="absolute bottom-0 left-0 p-6">
          <span class="bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded mb-2 inline-block">SALE</span>
          <h3 class="text-white text-2xl font-bold">{{ car.brand }} {{ car.model }}</h3>
          <p class="text-gray-300 text-sm mb-1">{{ car.year }} · {{ car.transmission }}</p>
          <p class="text-red-400 text-xl font-bold">€{{ Number(car.price).toLocaleString() }}</p>
        </div>
        <!-- Arrow hint on hover -->
        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
          <span class="text-white text-sm bg-black/40 px-4 py-2 rounded-lg">View Car</span>
        </div>
      </RouterLink>
    </div>

    <!-- Prev / Next buttons -->
    <button @click.prevent="prev"
      class="absolute left-3 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/80 text-white w-9 h-9 rounded-full flex items-center justify-center transition">
      &#8249;
    </button>
    <button @click.prevent="next"
      class="absolute right-3 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/80 text-white w-9 h-9 rounded-full flex items-center justify-center transition">
      &#8250;
    </button>

    <!-- Dot indicators -->
    <div class="absolute bottom-4 right-6 flex gap-1.5">
      <button
        v-for="(_, i) in slides" :key="i"
        @click.prevent="current = i"
        :class="['w-2 h-2 rounded-full transition', i === current ? 'bg-red-500' : 'bg-white/50']" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import client from '../api/client.js'

const slides  = ref([])
const current = ref(0)
let timer     = null

function next() { current.value = (current.value + 1) % slides.value.length }
function prev() { current.value = (current.value - 1 + slides.value.length) % slides.value.length }

function start() { timer = setInterval(next, 4000) }
function pause() { clearInterval(timer) }
function resume() { start() }

onMounted(async () => {
  try {
    const { data } = await client.get('/cars', { params: { on_sale: 1, limit: 10 } })
    const onSale   = data.data ?? []
    // Shuffle and take up to 4
    const shuffled = onSale.sort(() => Math.random() - 0.5).slice(0, 4)
    slides.value   = shuffled
    if (slides.value.length > 1) start()
  } catch { /* no on-sale cars, component stays hidden */ }
})

onUnmounted(() => clearInterval(timer))
</script>
