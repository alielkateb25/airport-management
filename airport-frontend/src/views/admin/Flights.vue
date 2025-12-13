<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Manage Flights</h1>
      <button @click="openCreateModal"
        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition">
        + Add Flight
      </button>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input v-model="searchQuery" @input="handleSearch" type="text" placeholder="Search flight number..."
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
        <select v-model="statusFilter" @change="handleSearch"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="">All Status</option>
          <option value="scheduled">Scheduled</option>
          <option value="delayed">Delayed</option>
          <option value="cancelled">Cancelled</option>
          <option value="completed">Completed</option>
        </select>
        <input v-model="dateFrom" @change="handleSearch" type="date"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
        <input v-model="dateTo" @change="handleSearch" type="date"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="text-gray-600">Loading flights...</div>
    </div>

    <!-- Flights Table -->
    <div v-else class="bg-white rounded-lg shadow overflow-hidden overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Flight #</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Airline</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departure</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arrival</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="flight in flights" :key="flight.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
              {{ flight.flight_number }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ flight.airline?.name }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ flight.origin_airport?.code }} → {{ flight.destination_airport?.code }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ formatDateTime(flight.departure_time) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ formatDateTime(flight.arrival_time) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
              ${{ flight.base_price }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="{
                'bg-green-100 text-green-800': flight.status === 'scheduled',
                'bg-yellow-100 text-yellow-800': flight.status === 'delayed',
                'bg-red-100 text-red-800': flight.status === 'cancelled',
                'bg-gray-100 text-gray-800': flight.status === 'completed'
              }" class="px-2 py-1 text-xs font-semibold rounded-full">
                {{ flight.status }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
              <button @click="openEditModal(flight)" class="text-blue-600 hover:text-blue-900">
                Edit
              </button>
              <button @click="deleteFlight(flight.id)" class="text-red-600 hover:text-red-900">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../api/axios'

const flights = ref([])
const loading = ref(false)
const searchQuery = ref('')
const statusFilter = ref('')
const dateFrom = ref('')
const dateTo = ref('')

onMounted(() => {
  fetchFlights()
})

const fetchFlights = async () => {
  loading.value = true
  try {
    const response = await axios.get('/flights', {
      params: {
        search: searchQuery.value,
        status: statusFilter.value,
        date_from: dateFrom.value,
        date_to: dateTo.value
      }
    })
    flights.value = response.data.data
  } catch (err) {
    console.error('Failed to fetch flights:', err)
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  fetchFlights()
}

const formatDateTime = (datetime) => {
  if (!datetime) return 'N/A'
  return new Date(datetime).toLocaleString()
}

const deleteFlight = async (id) => {
  if (!confirm('Are you sure you want to delete this flight?')) return

  try {
    await axios.delete(`/staff/flights/${id}`)
    fetchFlights()
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to delete flight')
  }
}
</script>