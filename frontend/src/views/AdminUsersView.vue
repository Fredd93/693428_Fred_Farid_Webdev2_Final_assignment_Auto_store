<template>
  <div class="max-w-5xl mx-auto px-6 py-10">
    <h1 class="text-2xl font-bold text-white mb-6">User Management</h1>

    <div v-if="loading" class="text-gray-400">Loading...</div>
    <table v-else class="w-full text-sm text-left text-gray-300">
      <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
        <tr>
          <th class="px-4 py-3">Name</th>
          <th class="px-4 py-3">Email</th>
          <th class="px-4 py-3">Role</th>
          <th class="px-4 py-3">Joined</th>
          <th class="px-4 py-3">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="u in users" :key="u.id" class="border-b border-gray-800 hover:bg-gray-900">
          <td class="px-4 py-3 text-white">{{ u.name }}</td>
          <td class="px-4 py-3">{{ u.email }}</td>
          <td class="px-4 py-3">
            <select @change="e => changeRole(u.id, e.target.value)" :value="u.role"
              class="bg-gray-800 border border-gray-700 text-white rounded px-2 py-1 text-xs capitalize">
              <option>client</option>
              <option>employee</option>
              <option>admin</option>
            </select>
          </td>
          <td class="px-4 py-3">{{ new Date(u.created_at).toLocaleDateString() }}</td>
          <td class="px-4 py-3">
            <button @click="deleteUser(u.id)" class="text-red-400 hover:text-red-300 text-xs">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
    <Pagination :meta="meta" @change="load" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Pagination from '../components/Pagination.vue'
import client from '../api/client.js'

const users   = ref([])
const meta    = ref({})
const loading = ref(true)

async function load(page = 1) {
  loading.value = true
  const { data } = await client.get('/users', { params: { page, limit: 15 } })
  users.value   = data.data
  meta.value    = data.meta
  loading.value = false
}

async function changeRole(id, role) {
  await client.put(`/users/${id}`, { role })
}

async function deleteUser(id) {
  if (!confirm('Delete this user?')) return
  await client.delete(`/users/${id}`)
  await load()
}

onMounted(() => load())
</script>
