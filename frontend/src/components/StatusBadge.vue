<template>
  <span :class="colours[status] ?? 'bg-gray-700 text-gray-200'"
    class="text-xs px-2 py-0.5 rounded-full font-medium capitalize">
    {{ status }}
  </span>
</template>

<script setup>
defineProps({ status: String })
const colours = {
  pending:   'bg-yellow-700 text-yellow-200',
  approved:  'bg-green-700  text-green-200',
  denied:    'bg-red-700    text-red-200',
  completed: 'bg-blue-700   text-blue-200',
}
</script>
