<template>
  <div class="glass-panel relative overflow-hidden rounded-[24px]" :style="{ aspectRatio }">
    <img
      :src="images[current]"
      :alt="alt"
      class="h-full w-full object-cover transition-opacity duration-300"
    />

    <template v-if="images.length > 1">
      <button
        class="site-btn-secondary absolute left-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full text-lg font-bold"
        @click.prevent="prev"
      >
        &lt;
      </button>
      <button
        class="site-btn-secondary absolute right-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full text-lg font-bold"
        @click.prevent="next"
      >
        &gt;
      </button>

      <div class="absolute bottom-4 left-1/2 flex -translate-x-1/2 gap-2">
        <button
          v-for="(_, i) in images"
          :key="i"
          :class="['h-2.5 rounded-full transition-all', i === current ? 'w-7 bg-white' : 'w-2.5 bg-white/45']"
          @click.prevent="current = i"
        />
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  images: { type: Array, required: true },
  alt: { type: String, default: 'Car image' },
  aspectRatio: { type: String, default: '16/9' },
})

const current = ref(0)

function prev() {
  current.value = (current.value - 1 + props.images.length) % props.images.length
}

function next() {
  current.value = (current.value + 1) % props.images.length
}
</script>
