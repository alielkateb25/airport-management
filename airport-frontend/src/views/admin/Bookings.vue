<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Bookings</h1>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input 
          v-model="searchQuery"
          @input="handleSearch"
          type="text"
          placeholder="Search booking reference or passenger..."
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <select 
          v-model="statusFilter"
          @change="handleSearch"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">All Status</option>
          <option value="confirmed">Confirmed</option>
          <option value="pending">Pending</option>
          <option value="cancelled">Cancelled</option>
        </select>
        <input 
          v-model="dateFrom"
          @change="handleSearch"
          type="date"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="text-gray-600">Loading bookings...</div>
    </div>

    <!-- Bookings Table -->
    <div v-else class="bg-white rounded-lg shadow overflow-hidden overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Passenger</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Flight</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seat</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="booking in bookings" :key="booking.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
              {{ booking.booking_reference }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ booking.passenger?.first_name }} {{ booking.passenger?.last_name }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ booking.flight?.flight_number }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ booking.seat_number || 'N/A' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              <span class="capitalize">{{ booking.class }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
              ${{ booking.total_price }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                :class="{
                  'bg-green-100 text-green-800': booking.status === 'confirmed',
                  'bg-yellow-100 text-yellow-800': booking.status === 'pending',
                  'bg-red-100 text-red-800': booking.status === 'cancelled'
                }"
                class="px-2 py-1 text-xs font-semibold rounded-full"
              >
                {{ booking.status }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
              <button 
                @click="viewBooking(booking)"
                class="text-blue-600 hover:text-blue-900"
              >
                View
              </button>
              <button 
                v-if="booking.status !== 'cancelled'"
                @click="cancelBooking(booking.id)"
                class="text-red-600 hover:text-red-900"
              >
                Cancel
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Empty State -->
      <div v-if="bookings.length === 0" class="text-center py-12">
        <p class="text-gray-600">No bookings found.</p>
      </div>
    </div>

    <!-- View Booking Modal -->
    <div v-if="showViewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4">
        <h2 class="text-2xl font-bold mb-6">Booking Details</h2>
        
        <div v-if="selectedBooking" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-gray-600 text-sm">Booking Reference</p>
              <p class="font-semibold">{{ selectedBooking.booking_reference }}</p>
            </div>
            <div>
              <p class="text-gray-600 text-sm">Status</p>
              <p class="font-semibold capitalize">{{ selectedBooking.status }}</p>
            </div>
            <div>
              <p class="text-gray-600 text-sm">Passenger</p>
              <p class="font-semibold">{{ selectedBooking.passenger?.first_name }} {{ selectedBooking.passenger?.last_name }}</p>
            </div>
            <div>
              <p class="text-gray-600 text-sm">Email</p>
              <p class="font-semibold">{{ selectedBooking.passenger?.email }}</p>
            </div>
            <div>
              <p class="text-gray-600 text-sm">Flight Number</p>
              <p class="font-semibold">{{ selectedBooking.flight?.flight_number }}</p>
            </div>
            <div>
              <p class="text-gray-600 text-sm">Seat Number</p>
              <p class="font-semibold">{{ selectedBooking.seat_number || 'Not assigned' }}</p>
            </div>
            <div>
              <p class="text-gray-600 text-sm">Class</p>
              <p class="font-semibold capitalize">{{ selectedBooking.class }}</p>
            </div>
            <div>
              <p class="text-gray-600 text-sm">Total Price</p>
              <p class="font-semibold text-green-600">${{ selectedBooking.total_price }}</p>
            </div>
          </div>
        </div>

        <div class="mt-6">
          <button 
            @click="closeViewModal"
            class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 rounded-lg font-semibold transition"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../api/axios'

const bookings = ref([])
const loading = ref(false)
const searchQuery = ref('')
const statusFilter = ref('')
const dateFrom = ref('')
const showViewModal = ref(false)
const selectedBooking = ref(null)

onMounted(() => {
  fetchBookings()
})

const fetchBookings = async () => {
  loading.value = true
  try {
    const response = await axios.get('/staff/bookings', {
      params: {
        search: searchQuery.value,
        status: statusFilter.value,
        date_from: dateFrom.value
      }
    })
    bookings.value = response.data.data
  } catch (err) {
    console.error('Failed to fetch bookings:', err)
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  fetchBookings()
}

const viewBooking = (booking) => {
  selectedBooking.value = booking
  showViewModal.value = true
}

const closeViewModal = () => {
  showViewModal.value = false
  selectedBooking.value = null
}

const cancelBooking = async (id) => {
  if (!confirm('Are you sure you want to cancel this booking?')) return
  
  try {
    await axios.delete(`/staff/bookings/${id}`)
    fetchBookings()
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to cancel booking')
  }
}
</script>