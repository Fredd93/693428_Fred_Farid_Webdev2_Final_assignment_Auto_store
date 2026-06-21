<template>
  <div
    v-if="slides.length"
    class="relative overflow-hidden rounded-[24px] bg-slate-100"
    @mouseenter="pause"
    @mouseleave="resume"
  >
    <div
      class="flex transition-transform duration-500 ease-in-out"
      :style="{ transform: `translateX(-${current * 100}%)` }"
    >
      <RouterLink
        v-for="car in slides"
        :key="car.id"
        :to="`/cars/${car.id}`"
        class="group relative block min-w-full h-80 md:h-[28rem]"
      >
        <img
          :src="car.thumbnail || car.image_path"
          :alt="`${car.brand} ${car.model}`"
          class="h-full w-full object-cover"
        />
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/70 via-slate-950/20 to-transparent" />

        <div class="absolute left-5 top-5">
          <span class="public-badge inline-flex px-3 py-1 text-[11px] font-extrabold uppercase tracking-[0.24em]">
            On Sale
          </span>
        </div>

        <div class="absolute inset-x-0 bottom-0 top-0 p-6 md:p-8">
          <div class="flex h-full flex-col">
            <div class="mx-auto mt-0 inline-flex flex-wrap justify-center gap-3 rounded-2xl px-4 py-3 glass-panel">
              <span class="inventory-chip rounded-full px-3 py-1 text-xs font-semibold">{{ car.year }}</span>
              <span class="inventory-chip rounded-full px-3 py-1 text-xs font-semibold">{{ car.transmission }}</span>
              <span class="inventory-chip rounded-full px-3 py-1 text-xs font-semibold">{{ car.color || 'Showroom' }}</span>
            </div>

            <div class="mt-auto mx-auto max-w-2xl pb-4 text-center md:pb-6">
              <h3 class="mt-2 text-3xl font-black tracking-tight text-white md:text-4xl">
                {{ car.brand }} {{ car.model }}
              </h3>
              <p class="mt-2 text-sm text-slate-200">
                Live stock from the same backend, now presented in the redesigned showroom.
              </p>
              <p class="mt-4 text-2xl font-black tracking-tight text-white">
                EUR {{ Number(car.price).toLocaleString() }}
              </p>
            </div>
          </div>
        </div>

        <div class="absolute inset-0 flex items-center justify-center opacity-0 transition group-hover:opacity-100">
          <span class="site-btn-secondary px-4 py-2 text-sm font-semibold">View car</span>
        </div>
      </RouterLink>
    </div>

    <button
      class="site-btn-secondary absolute left-4 top-1/2 h-10 w-10 -translate-y-1/2 rounded-full text-lg font-bold"
      @click.prevent="prev"
    >
      &lt;
    </button>
    <button
      class="site-btn-secondary absolute right-4 top-1/2 h-10 w-10 -translate-y-1/2 rounded-full text-lg font-bold"
      @click.prevent="next"
    >
      &gt;
    </button>

    <div class="absolute bottom-5 right-6 flex gap-2">
      <button
        v-for="(_, i) in slides"
        :key="i"
        :class="['h-2.5 rounded-full transition-all', i === current ? 'w-7 bg-white' : 'w-2.5 bg-white/45']"
        @click.prevent="current = i"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { fetchCarsPage } from '../api/cars.js'

const slides = ref([])
const current = ref(0)
let timer = null

function next() {
  current.value = (current.value + 1) % slides.value.length
}

function prev() {
  current.value = (current.value - 1 + slides.value.length) % slides.value.length
}

function start() {
  timer = setInterval(next, 4000)
}

function pause() {
  clearInterval(timer)
}

function resume() {
  start()
}

onMounted(async () => {
  try {
    const { data } = await fetchCarsPage({ on_sale: true, limit: 10 })
    const onSale = data ?? []
    const shuffled = onSale.sort(() => Math.random() - 0.5).slice(0, 4)
    slides.value = shuffled
    if (slides.value.length > 1) start()
  } catch {
    // Keep the section hidden when no sale inventory is available.
  }
})

onUnmounted(() => clearInterval(timer))
</script>
