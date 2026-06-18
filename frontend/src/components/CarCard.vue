<template>
  <RouterLink :to="`/cars/${car.id}`"
    class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden hover:border-red-500 transition group">
    <img :src="`/${car.image_path}`" :alt="`${car.brand} ${car.model}`"
      class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300" />
    <div class="p-4">
      <h3 class="text-white font-semibold">{{ car.brand }} {{ car.model }}</h3>
      <p class="text-gray-400 text-sm mt-1">{{ car.year }} · {{ car.transmission }}</p>
      <div class="flex items-center justify-between mt-3">
        <span class="text-red-400 font-bold text-lg">€{{ Number(car.price).toLocaleString() }}</span>
        <span v-if="car.on_sale" class="bg-red-600 text-white text-xs px-2 py-0.5 rounded">SALE</span>
      </div>
    </div>
  </RouterLink>
</template>

<script setup>
defineProps({ car: Object })
</script>
