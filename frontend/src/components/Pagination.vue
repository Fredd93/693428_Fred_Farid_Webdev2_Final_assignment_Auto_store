<template>
  <div v-if="meta.pages > 1" class="flex items-center justify-center gap-2 mt-8">
    <button @click="$emit('change', meta.page - 1)" :disabled="meta.page <= 1"
      class="px-3 py-1 bg-gray-800 text-white rounded disabled:opacity-40">‹</button>

    <span class="text-gray-400 text-sm">Page {{ meta.page }} of {{ meta.pages }}</span>

    <button @click="$emit('change', meta.page + 1)" :disabled="meta.page >= meta.pages"
      class="px-3 py-1 bg-gray-800 text-white rounded disabled:opacity-40">›</button>
  </div>
</template>

<script setup>
defineProps({ meta: Object })
defineEmits(['change'])
</script>
