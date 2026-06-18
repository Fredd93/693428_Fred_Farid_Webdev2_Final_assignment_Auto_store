<template>
  <div class="relative w-full overflow-hidden rounded-xl bg-gray-900" :style="{ aspectRatio }">
    <img
      :src="`/${images[current]}`"
      :alt="alt"
      class="w-full h-full object-cover transition-opacity duration-300"
    />

    <template v-if="images.length > 1">
      <button
        @click.prevent="prev"
        class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/75 text-white rounded-full w-8 h-8 flex items-center justify-center"
      >‹</button>
      <button
        @click.prevent="next"
        class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/75 text-white rounded-full w-8 h-8 flex items-center justify-center"
      >›</button>

      <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-1.5">
        <button
          v-for="(_, i) in images"
          :key="i"
          @click.prevent="current = i"
          :class="['w-2 h-2 rounded-full transition-colors', i === current ? 'bg-white' : 'bg-white/40']"
        />
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  images:      { type: Array,  required: true },
  alt:         { type: String, default: 'Car image' },
  aspectRatio: { type: String, default: '16/9' },
})

const current = ref(0)
function prev() { current.value = (current.value - 1 + props.images.length) % props.images.length }
function next() { current.value = (current.value + 1) % props.images.length }
</script>
