<template>
  <nav v-if="meta.pages > 1" class="mt-10 flex items-center justify-center gap-2">
    <button
      @click="$emit('change', meta.page - 1)"
      :disabled="meta.page <= 1"
      class="site-btn-secondary h-10 w-10 text-lg disabled:opacity-40"
    >‹</button>

    <template v-for="(p, i) in pageItems" :key="i">
      <span v-if="p === '...'" class="px-1 text-sm text-slate-400">…</span>
      <button
        v-else
        @click="$emit('change', p)"
        class="h-10 min-w-10 rounded-xl px-3 text-sm font-semibold transition"
        :class="p === meta.page ? 'bg-blue-600 text-white' : 'site-btn-secondary'"
      >{{ p }}</button>
    </template>

    <button
      @click="$emit('change', meta.page + 1)"
      :disabled="meta.page >= meta.pages"
      class="site-btn-secondary h-10 w-10 text-lg disabled:opacity-40"
    >›</button>
  </nav>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({ meta: Object })
defineEmits(['change'])

const pageItems = computed(() => {
  const { page, pages } = props.meta
  const window = 2
  const items = [1]

  const start = Math.max(2, page - window)
  const end   = Math.min(pages - 1, page + window)

  if (start > 2) items.push('...')
  for (let p = start; p <= end; p++) items.push(p)
  if (end < pages - 1) items.push('...')
  if (pages > 1) items.push(pages)

  return items
})
</script>
