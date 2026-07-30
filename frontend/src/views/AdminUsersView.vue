<template>
  <div class="max-w-5xl mx-auto px-6 py-10 bg-gray-950 rounded-2xl">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-white">{{ auth.isAdmin ? 'User Management' : 'Customers' }}</h1>
      <button v-if="auth.isAdmin" @click="openAdd" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">
        + Add Employee
      </button>
    </div>

    <div v-if="loading" class="text-gray-400">Loading...</div>
    <table v-else class="w-full text-sm text-left text-gray-300">
      <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
        <tr>
          <th class="px-4 py-3">Name</th>
          <th class="px-4 py-3">Email</th>
          <th class="px-4 py-3">Phone</th>
          <th class="px-4 py-3">Address</th>
          <th class="px-4 py-3">Role</th>
          <th class="px-4 py-3">Joined</th>
          <th v-if="auth.isAdmin" class="px-4 py-3">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="u in users" :key="u.id" class="border-b border-gray-800 hover:bg-gray-900">
          <td class="px-4 py-3 text-white">{{ u.name }}</td>
          <td class="px-4 py-3">{{ u.email }}</td>
          <td class="px-4 py-3">{{ u.phone || '—' }}</td>
          <td class="px-4 py-3">{{ u.address || '—' }}</td>
          <td class="px-4 py-3">
            <select v-if="auth.isAdmin" @change="e => changeRole(u.id, e.target.value)" :value="u.role"
              class="bg-gray-800 border border-gray-700 text-white rounded px-2 py-1 text-xs capitalize">
              <option>client</option>
              <option>employee</option>
              <option>admin</option>
            </select>
            <span v-else class="capitalize">{{ u.role }}</span>
          </td>
          <td class="px-4 py-3">{{ new Date(u.created_at).toLocaleDateString() }}</td>
          <td v-if="auth.isAdmin" class="px-4 py-3 space-x-3">
            <button @click="openEdit(u)" class="text-blue-400 hover:text-blue-300 text-xs">Edit</button>
            <button @click="deleteUser(u.id)" class="text-red-400 hover:text-red-300 text-xs">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
    <Pagination :meta="meta" @change="load" />

    <!-- Edit User modal -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
      <div class="bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-md">
        <h2 class="text-white font-bold text-lg mb-4">Edit User</h2>
        <form @submit.prevent="submitEdit" class="space-y-4 text-sm">
          <div>
            <label class="text-gray-400 block mb-1">Full Name</label>
            <input v-model="editForm.name" type="text" required
              class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
          </div>
          <div>
            <label class="text-gray-400 block mb-1">Email</label>
            <input v-model="editForm.email" type="email" required
              class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
          </div>
          <div>
            <label class="text-gray-400 block mb-1">Role</label>
            <select v-model="editForm.role"
              class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2">
              <option value="client">Client</option>
              <option value="employee">Employee</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <p v-if="editErr" class="text-red-400">{{ editErr }}</p>
          <div class="flex gap-3 pt-1">
            <button type="submit" :disabled="editSaving"
              class="flex-1 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white py-2 rounded-lg font-semibold">
              {{ editSaving ? 'Saving...' : 'Save Changes' }}
            </button>
            <button type="button" @click="showEditModal = false"
              class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-2 rounded-lg">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Add Employee modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
      <div class="bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-md">
        <h2 class="text-white font-bold text-lg mb-4">Add Employee</h2>
        <form @submit.prevent="submitAdd" class="space-y-4 text-sm">
          <div>
            <label class="text-gray-400 block mb-1">Full Name</label>
            <input v-model="form.name" type="text" required
              class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
          </div>
          <div>
            <label class="text-gray-400 block mb-1">Email</label>
            <input v-model="form.email" type="email" required
              class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
          </div>
          <div>
            <label class="text-gray-400 block mb-1">Password</label>
            <input v-model="form.password" type="password" required minlength="6"
              class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
          </div>
          <div>
            <label class="text-gray-400 block mb-1">Role</label>
            <select v-model="form.role"
              class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2">
              <option value="employee">Employee</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <p v-if="formErr" class="text-red-400">{{ formErr }}</p>
          <div class="flex gap-3 pt-1">
            <button type="submit" :disabled="saving"
              class="flex-1 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white py-2 rounded-lg font-semibold">
              {{ saving ? 'Creating...' : 'Create Account' }}
            </button>
            <button type="button" @click="showModal = false"
              class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-2 rounded-lg">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Pagination from '../components/Pagination.vue'
import client from '../api/client.js'
import { useAuthStore } from '../stores/auth.js'

const auth      = useAuthStore()
const users     = ref([])
const meta      = ref({})
const loading   = ref(true)
const showModal = ref(false)
const saving    = ref(false)
const formErr   = ref('')
const form      = ref({ name: '', email: '', password: '', role: 'employee' })

const showEditModal = ref(false)
const editSaving    = ref(false)
const editErr       = ref('')
const editForm      = ref({ id: null, name: '', email: '', role: 'client' })

async function load(page = 1) {
  loading.value = true
  try {
    const { data } = await client.get('/users', { params: { page, limit: 15 } })
    users.value   = data.data
    meta.value    = data.meta
  } catch {
    // table stays empty
  } finally {
    loading.value = false
  }
}

async function changeRole(id, role) {
  try {
    await client.put(`/users/${id}`, { role })
  } catch (e) {
    alert(e.response?.data?.error ?? 'Failed to update role')
  }
}

async function deleteUser(id) {
  if (!confirm('Permanently delete this user and all their data?')) return
  try {
    await client.delete(`/users/${id}`)
    await load()
  } catch (e) {
    alert(e.response?.data?.error ?? 'Failed to delete user')
  }
}

function openEdit(u) {
  editForm.value = { id: u.id, name: u.name, email: u.email, role: u.role }
  editErr.value  = ''
  showEditModal.value = true
}

async function submitEdit() {
  editErr.value    = ''
  editSaving.value = true
  try {
    await client.put(`/users/${editForm.value.id}`, {
      name:  editForm.value.name,
      email: editForm.value.email,
      role:  editForm.value.role,
    })
    showEditModal.value = false
    await load()
  } catch (e) {
    editErr.value = e.response?.data?.error ?? 'Failed to update user'
  } finally {
    editSaving.value = false
  }
}

function openAdd() {
  form.value    = { name: '', email: '', password: '', role: 'employee' }
  formErr.value = ''
  showModal.value = true
}

async function submitAdd() {
  formErr.value = ''
  saving.value  = true
  try {
    await client.post('/users', form.value)
    showModal.value = false
    await load()
  } catch (e) {
    formErr.value = e.response?.data?.error ?? 'Failed to create account'
  } finally {
    saving.value = false
  }
}

onMounted(() => load())
</script>
