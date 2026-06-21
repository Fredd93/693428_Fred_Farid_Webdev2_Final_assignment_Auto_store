<template>
  <div v-if="meta.pages > 1" class="mt-10 flex items-center justify-center gap-3">
    <button @click="$emit('change', meta.page - 1)" :disabled="meta.page <= 1" class="site-btn-secondary h-10 w-10 text-lg disabled:opacity-40">‹</button>
    <span class="text-sm font-medium text-slate-600">Page {{ meta.page }} of {{ meta.pages }}</span>
    <button @click="$emit('change', meta.page + 1)" :disabled="meta.page >= meta.pages" class="site-btn-secondary h-10 w-10 text-lg disabled:opacity-40">›</button>
  </div>
</template>

<script setup>
defineProps({ meta: Object })
defineEmits(['change'])
</script>
