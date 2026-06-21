<template>
  <RouterLink :to="`/cars/${car.id}`" class="public-card glass-panel group block transition duration-200 hover:-translate-y-1">
    <img :src="car.thumbnail || car.image_path" :alt="`${car.brand} ${car.model}`" class="public-card-image transition-transform duration-300 group-hover:scale-[1.02]" />
    <div class="p-5">
      <div class="mb-1 text-[11px] font-bold uppercase tracking-[0.18em] text-blue-600">{{ car.brand }}</div>
      <h3 class="text-xl font-bold tracking-tight text-slate-900">{{ car.model }}</h3>
      <p class="mt-2 text-sm text-slate-600">{{ car.year }} · {{ car.transmission }}</p>
      <div class="mt-5 flex items-center justify-between">
        <span class="text-2xl font-extrabold tracking-tight text-slate-900">€{{ Number(car.price).toLocaleString() }}</span>
        <span v-if="car.on_sale" class="public-badge px-3 py-1 text-[11px] font-bold uppercase tracking-[0.12em]">On sale</span>
      </div>
    </div>
  </RouterLink>
</template>

<script setup>
defineProps({ car: Object })
</script>
