<template>
  <div class="max-w-4xl mx-auto px-6 py-10 space-y-12 bg-gray-950 rounded-2xl">

    <!-- Profile Section -->
    <section>
      <h1 class="text-3xl font-bold text-white mb-1">Welcome, {{ auth.user?.name }}</h1>
      <p class="text-gray-400 mb-6">Role: <span class="capitalize text-red-400">{{ auth.user?.role }}</span></p>

      <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
        <h2 class="text-lg font-semibold text-white mb-4">Edit Profile</h2>
        <form @submit.prevent="saveProfile" class="space-y-4 max-w-sm">
          <div>
            <label class="text-gray-400 text-sm block mb-1">Full Name</label>
            <input v-model="profileForm.name" type="text" required
              class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
          </div>
          <div>
            <label class="text-gray-400 text-sm block mb-1">Email</label>
            <input v-model="profileForm.email" type="email" required
              class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
          </div>
          <div>
            <label class="text-gray-400 text-sm block mb-1">Phone Number</label>
            <input v-model="profileForm.phone" type="tel" placeholder="01012345678"
              pattern="^(?:\+20|0)1[0125][0-9]{8}$" title="Enter a valid Egyptian mobile number, e.g. 01012345678"
              class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
          </div>
          <div>
            <label class="text-gray-400 text-sm block mb-1">Address</label>
            <input v-model="profileForm.address" type="text" placeholder="Street, City, Country"
              class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
          </div>
          <p v-if="profileErr" class="text-red-400 text-sm">{{ profileErr }}</p>
          <p v-if="profileOk"  class="text-green-400 text-sm">{{ profileOk }}</p>
          <button type="submit" :disabled="profileSaving"
            class="bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white px-5 py-2 rounded-lg font-semibold">
            {{ profileSaving ? 'Saving...' : 'Save Changes' }}
          </button>
        </form>
      </div>
    </section>

    <!-- Staff Overview Section (employee/admin) -->
    <section v-if="auth.isEmployee">
      <h2 class="text-xl font-semibold text-white mb-4">Overview</h2>
      <div v-if="statsLoading" class="text-gray-400">Loading...</div>
      <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <RouterLink to="/admin/orders" class="bg-gray-900 border border-gray-800 rounded-xl p-5 hover:border-red-600 transition">
          <p class="text-gray-400 text-sm">Pending Orders</p>
          <p class="text-3xl font-black text-white mt-1">{{ stats.pending_orders }}</p>
        </RouterLink>
        <RouterLink to="/admin/appointments" class="bg-gray-900 border border-gray-800 rounded-xl p-5 hover:border-red-600 transition">
          <p class="text-gray-400 text-sm">Pending Appointments</p>
          <p class="text-3xl font-black text-white mt-1">{{ stats.pending_appointments }}</p>
        </RouterLink>
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
          <p class="text-gray-400 text-sm">Sold By Me</p>
          <p class="text-3xl font-black text-white mt-1">{{ stats.my_sales_count }}</p>
        </div>

        <template v-if="auth.isAdmin">
          <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
            <p class="text-gray-400 text-sm">Cars in Inventory</p>
            <p class="text-3xl font-black text-white mt-1">{{ stats.total_cars }}</p>
          </div>
          <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
            <p class="text-gray-400 text-sm">Cars Sold / Leased</p>
            <p class="text-3xl font-black text-white mt-1">{{ stats.cars_sold }}</p>
          </div>
          <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 sm:col-span-2">
            <p class="text-gray-400 text-sm">Total Deal Value (completed orders)</p>
            <p class="text-3xl font-black text-white mt-1">&euro;{{ Number(stats.total_deal_value).toLocaleString() }}</p>
          </div>
          <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
            <p class="text-gray-400 text-sm">Clients</p>
            <p class="text-3xl font-black text-white mt-1">{{ stats.total_clients }}</p>
          </div>
          <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
            <p class="text-gray-400 text-sm">Staff (employees + admins)</p>
            <p class="text-3xl font-black text-white mt-1">{{ stats.total_staff }}</p>
          </div>
        </template>
      </div>

      <div v-if="auth.isAdmin" class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
          <h3 class="text-white font-semibold mb-4">Top Salesmen (This Month)</h3>
          <div v-if="analyticsLoading" class="text-gray-400 text-sm">Loading...</div>
          <div v-else-if="!topSalesmen.length" class="text-gray-500 text-sm">No employees yet.</div>
          <div v-else class="space-y-3">
            <div v-for="s in topSalesmen" :key="s.employee_id">
              <div class="flex justify-between text-sm mb-1">
                <span class="text-gray-300">{{ s.name }}</span>
                <span class="text-white font-semibold">{{ s.sales_count }}</span>
              </div>
              <div class="w-full bg-gray-800 rounded-full h-3">
                <div class="bg-red-600 h-3 rounded-full transition-all"
                  :style="{ width: (s.sales_count / maxSalesCount * 100) + '%' }"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
          <h3 class="text-white font-semibold mb-4">Top 3 Cars (This Month)</h3>
          <div v-if="analyticsLoading" class="text-gray-400 text-sm">Loading...</div>
          <div v-else-if="!topCars.length" class="text-gray-500 text-sm">No sales recorded yet this month.</div>
          <div v-else class="grid grid-cols-3 gap-3">
            <div v-for="(c, i) in topCars" :key="c.car_id" class="text-center">
              <div class="relative">
                <img :src="`/${c.image_path}`" :alt="`${c.brand} ${c.model}`" class="w-full h-20 object-cover rounded-lg" />
                <span class="absolute top-1 left-1 bg-red-600 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full">{{ i + 1 }}</span>
              </div>
              <p class="text-white text-sm font-medium mt-2">{{ c.brand }} {{ c.model }}</p>
              <p class="text-gray-400 text-xs">{{ c.units_sold }} sold</p>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <RouterLink to="/admin/cars" class="bg-gray-800 hover:bg-gray-700 text-white text-center py-3 rounded-lg text-sm font-semibold">Manage Cars</RouterLink>
        <RouterLink to="/admin/orders" class="bg-gray-800 hover:bg-gray-700 text-white text-center py-3 rounded-lg text-sm font-semibold">Manage Orders</RouterLink>
        <RouterLink to="/admin/appointments" class="bg-gray-800 hover:bg-gray-700 text-white text-center py-3 rounded-lg text-sm font-semibold">Manage Appointments</RouterLink>
        <RouterLink to="/admin/users" class="bg-gray-800 hover:bg-gray-700 text-white text-center py-3 rounded-lg text-sm font-semibold">{{ auth.isAdmin ? 'Manage Users' : 'View Customers' }}</RouterLink>
      </div>
    </section>

    <!-- Appointments Section (client only) -->
    <section v-if="!auth.isEmployee">
      <h2 class="text-xl font-semibold text-white mb-4">My Appointments</h2>
      <div v-if="apptLoading" class="text-gray-400">Loading...</div>
      <div v-else-if="!appointments.length" class="text-gray-500">No appointments booked yet.</div>
      <div v-else class="space-y-3">
        <div v-for="a in appointments" :key="a.appointment_id"
          class="bg-gray-900 border border-gray-800 rounded-lg p-4 flex justify-between items-center">
          <div>
            <p class="text-white font-medium">{{ a.brand }} {{ a.model }}</p>
            <p class="text-gray-400 text-sm">{{ new Date(a.appointment_date).toLocaleString('en-GB', { dateStyle: 'medium', timeStyle: 'short' }) }}</p>
          </div>
          <StatusBadge :status="a.status" />
        </div>
      </div>
    </section>

    <!-- My Cars Section (client only) -->
    <section v-if="!auth.isEmployee">
      <h2 class="text-xl font-semibold text-white mb-4">My Cars</h2>
      <div v-if="myCarsLoading" class="text-gray-400">Loading...</div>
      <div v-else-if="!myCars.length" class="text-gray-500">You don't own or lease any cars yet.</div>
      <div v-else class="space-y-3">
        <div v-for="o in myCars" :key="o.id" class="bg-gray-900 border border-gray-800 rounded-lg overflow-hidden">
          <button type="button" class="w-full flex justify-between items-center p-4 text-left hover:bg-gray-800/60" @click="toggleCar(o)">
            <div class="flex items-center gap-4">
              <img :src="`/${o.image_path}`" :alt="`${o.brand} ${o.model}`" class="w-16 h-12 object-cover rounded" />
              <div>
                <p class="text-white font-medium">{{ o.brand }} {{ o.model }} ({{ o.year }})</p>
                <p class="text-gray-400 text-sm capitalize">{{ o.order_type }} &middot; Completed {{ formatDMY(o.completed_at) }}</p>
              </div>
            </div>
            <span class="text-gray-500 text-xl leading-none">{{ expandedOrderId === o.id ? '−' : '+' }}</span>
          </button>

          <div v-if="expandedOrderId === o.id" class="border-t border-gray-800 p-4 space-y-4">
            <button type="button" @click="sellThroughDealership(o)"
              class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
              Sell through the dealership
            </button>

            <div v-if="o.order_type === 'lease'">
              <h3 class="text-white font-semibold mb-2">Lease Installments</h3>
              <div v-if="installmentsLoading && !installmentsByOrder[o.id]" class="text-gray-400 text-sm">Loading...</div>
              <table v-else-if="installmentsByOrder[o.id]?.length" class="w-full text-sm text-left text-gray-300">
                <thead class="text-gray-500 uppercase text-xs">
                  <tr>
                    <th class="py-2 pr-2">#</th>
                    <th class="py-2 pr-2">Due Date</th>
                    <th class="py-2 pr-2">Amount</th>
                    <th class="py-2 pr-2">Status</th>
                    <th class="py-2"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="inst in installmentsByOrder[o.id]" :key="inst.id" class="border-t border-gray-800">
                    <td class="py-2 pr-2">{{ inst.installment_no }}</td>
                    <td class="py-2 pr-2">{{ formatDMY(inst.due_date) }}</td>
                    <td class="py-2 pr-2">&euro;{{ Number(inst.amount).toLocaleString() }}</td>
                    <td class="py-2 pr-2">
                      <span class="capitalize" :class="inst.status === 'paid' ? 'text-emerald-400' : 'text-amber-400'">{{ inst.status }}</span>
                    </td>
                    <td class="py-2">
                      <button v-if="inst.status === 'due'" type="button" @click="openPayConfirm(inst)"
                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs">
                        Pay
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Fake Payment Confirm Overlay -->
    <div v-if="showPayConfirm" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
      <div class="bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-sm text-center">
        <h2 class="text-white font-bold text-lg mb-2">Confirm Payment</h2>
        <p class="text-gray-400 text-sm mb-1">Installment #{{ payTarget?.installment_no }}</p>
        <p class="text-3xl font-black text-white mb-4">&euro;{{ Number(payTarget?.amount).toLocaleString() }}</p>
        <p class="text-gray-500 text-xs mb-6">Simulated payment for demo purposes &mdash; no real money is charged.</p>
        <p v-if="payErr" class="text-red-400 text-sm mb-4">{{ payErr }}</p>
        <div class="flex gap-3">
          <button type="button" @click="confirmPay" :disabled="paySaving"
            class="flex-1 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white py-2 rounded-lg font-semibold">
            {{ paySaving ? 'Processing...' : 'Confirm Payment' }}
          </button>
          <button type="button" @click="showPayConfirm = false" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-2 rounded-lg">
            Cancel
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'
import StatusBadge from '../components/StatusBadge.vue'
import client from '../api/client.js'

const auth   = useAuthStore()
const router = useRouter()

// Profile
const profileForm  = ref({
  name:    auth.user?.name    ?? '',
  email:   auth.user?.email   ?? '',
  phone:   auth.user?.phone   ?? '',
  address: auth.user?.address ?? '',
})
const profileSaving = ref(false)
const profileErr   = ref('')
const profileOk    = ref('')

async function saveProfile() {
  profileSaving.value = true
  profileErr.value    = ''
  profileOk.value     = ''
  try {
    const { data } = await client.put(`/users/${auth.user.id}`, {
      name:    profileForm.value.name,
      email:   profileForm.value.email,
      phone:   profileForm.value.phone,
      address: profileForm.value.address,
    })
    auth.user.name    = data.name
    auth.user.email   = data.email
    auth.user.phone   = data.phone
    auth.user.address = data.address
    localStorage.setItem('user', JSON.stringify(auth.user))
    profileOk.value = 'Profile updated successfully.'
  } catch (e) {
    profileErr.value = e.response?.data?.error ?? 'Failed to update profile.'
  } finally {
    profileSaving.value = false
  }
}

// Staff overview
const stats        = ref({})
const statsLoading = ref(true)

async function loadStats() {
  try {
    const { data } = await client.get('/stats')
    stats.value = data
  } catch {
    stats.value = {}
  } finally {
    statsLoading.value = false
  }
}

// Sales telemetry (admin only)
const topSalesmen      = ref([])
const topCars          = ref([])
const analyticsLoading = ref(true)

const maxSalesCount = computed(() => Math.max(1, ...topSalesmen.value.map(s => s.sales_count)))

async function loadAnalytics() {
  try {
    const [salesRes, carsRes] = await Promise.all([
      client.get('/stats/top-salesmen'),
      client.get('/stats/top-cars'),
    ])
    topSalesmen.value = salesRes.data
    topCars.value     = carsRes.data
  } catch {
    topSalesmen.value = []
    topCars.value     = []
  } finally {
    analyticsLoading.value = false
  }
}

// Appointments
const appointments = ref([])
const apptLoading  = ref(true)

async function loadAppointments() {
  try {
    const { data } = await client.get('/appointments', { params: { limit: 10 } })
    appointments.value = data.data
  } catch {
    appointments.value = []
  } finally {
    apptLoading.value = false
  }
}

// My Cars
const myCars              = ref([])
const myCarsLoading       = ref(true)
const expandedOrderId     = ref(null)
const installmentsByOrder = ref({})
const installmentsLoading = ref(false)

function formatDMY(dateStr) {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  const dd = String(d.getDate()).padStart(2, '0')
  const mm = String(d.getMonth() + 1).padStart(2, '0')
  return `${dd}/${mm}/${d.getFullYear()}`
}

async function loadMyCars() {
  myCarsLoading.value = true
  try {
    const { data } = await client.get('/orders/my-cars')
    myCars.value = data
  } catch {
    myCars.value = []
  } finally {
    myCarsLoading.value = false
  }
}

async function toggleCar(order) {
  if (expandedOrderId.value === order.id) {
    expandedOrderId.value = null
    return
  }
  expandedOrderId.value = order.id

  if (order.order_type === 'lease' && !installmentsByOrder.value[order.id]) {
    installmentsLoading.value = true
    try {
      const { data } = await client.get(`/orders/${order.id}/installments`)
      installmentsByOrder.value = { ...installmentsByOrder.value, [order.id]: data }
    } catch {
      installmentsByOrder.value = { ...installmentsByOrder.value, [order.id]: [] }
    } finally {
      installmentsLoading.value = false
    }
  }
}

function sellThroughDealership(order) {
  router.push(`/cars/${order.car_id}?intent=sell_back`)
}

// Fake installment payment
const showPayConfirm = ref(false)
const payTarget       = ref(null)
const paySaving        = ref(false)
const payErr          = ref('')

function openPayConfirm(installment) {
  payTarget.value = installment
  payErr.value    = ''
  showPayConfirm.value = true
}

async function confirmPay() {
  if (!payTarget.value) return
  paySaving.value = true
  payErr.value     = ''
  try {
    const { data }  = await client.post(`/installments/${payTarget.value.id}/pay`)
    const orderId   = data.order_id
    installmentsByOrder.value = {
      ...installmentsByOrder.value,
      [orderId]: (installmentsByOrder.value[orderId] || []).map(i => (i.id === data.id ? data : i)),
    }
    showPayConfirm.value = false
  } catch (e) {
    payErr.value = e.response?.data?.error ?? 'Payment failed.'
  } finally {
    paySaving.value = false
  }
}

onMounted(() => {
  if (auth.isEmployee) {
    loadStats()
    if (auth.isAdmin) loadAnalytics()
  } else {
    loadAppointments()
    loadMyCars()
  }
})
</script>
