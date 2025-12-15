<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Search Flights</h1>

    <!-- Search Form -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <form @submit.prevent="searchFlights" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Origin -->
          <div>
            <label class="block text-gray-700 font-semibold mb-2">From (City or Code)</label>
            <input 
              v-model="searchForm.origin"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
              placeholder="e.g., JFK or New York"
              required
            />
          </div>

          <!-- Destination -->
          <div>
            <label class="block text-gray-700 font-semibold mb-2">To (City or Code)</label>
            <input 
              v-model="searchForm.destination"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
              placeholder="e.g., LAX or Los Angeles"
              required
            />
          </div>

          <!-- Departure Date -->
          <div>
            <label class="block text-gray-700 font-semibold mb-2">Departure Date</label>
            <input 
              v-model="searchForm.departure_date"
              type="date"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
              required
            />
          </div>

          <!-- Passengers -->
          <div>
            <label class="block text-gray-700 font-semibold mb-2">Passengers</label>
            <input 
              v-model.number="searchForm.passengers"
              type="number"
              min="1"
              max="10"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
              required
            />
          </div>
        </div>

        <button 
          type="submit"
          :disabled="searching"
          class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-lg font-semibold transition disabled:bg-gray-400"
        >
          {{ searching ? 'Searching...' : '🔍 Search Flights' }}
        </button>
      </form>
    </div>

    <!-- Search Results -->
    <div v-if="searchResults.length > 0" class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-6 bg-gray-50 border-b">
        <h2 class="text-xl font-bold text-gray-800">Available Flights ({{ searchResults.length }})</h2>
      </div>
      
      <div class="divide-y divide-gray-200">
        <div v-for="flight in searchResults" :key="flight.id" class="p-6 hover:bg-gray-50 transition">
          <div class="flex justify-between items-center">
            <div class="flex-1">
              <div class="flex items-center space-x-4 mb-2">
                <span class="text-lg font-bold text-gray-900">{{ flight.flight_number }}</span>
                <span class="text-sm text-gray-600">{{ flight.airline?.name }}</span>
              </div>
              
              <div class="flex items-center space-x-8 text-sm">
                <div>
                  <p class="text-gray-600">From</p>
                  <p class="font-semibold">{{ flight.origin_airport?.code }}</p>
                  <p class="text-xs text-gray-500">{{ formatTime(flight.departure_time) }}</p>
                </div>
                <div class="text-gray-400">→</div>
                <div>
                  <p class="text-gray-600">To</p>
                  <p class="font-semibold">{{ flight.destination_airport?.code }}</p>
                  <p class="text-xs text-gray-500">{{ formatTime(flight.arrival_time) }}</p>
                </div>
              </div>

              <div class="mt-2 text-sm text-gray-600">
                Available Seats: <span class="font-semibold">{{ flight.available_seats }}</span>
              </div>
            </div>

            <div class="text-right ml-6">
              <p class="text-2xl font-bold text-green-600 mb-2">${{ flight.base_price }}</p>
              <router-link 
                :to="{ name: 'AgentCreateBooking', query: { flight_id: flight.id } }"
                class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-semibold transition inline-block"
              >
                Book Now
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- No Results -->
    <div v-else-if="searched && searchResults.length === 0" class="bg-white rounded-lg shadow p-12 text-center">
      <p class="text-gray-600">No flights found matching your search criteria.</p>
    </div>
  </div>
</template>



<script setup>
  import { ref } from 'vue'
  import { useRouter } from 'vue-router'
  import { useBookingStore } from '../../stores/booking'
  
  const router = useRouter()
  const bookingStore = useBookingStore()
  
  const form = ref({
    passenger_id: '',
    flight_id: '',
    seat_number: '',
    class: 'economy',
    total_price: 0
  })
  
  const error = ref(null)
  const submitting = ref(false)
  
  const handleSubmit = async () => {
    submitting.value = true
    error.value = null
    
    try {
      await bookingStore.createBooking(form.value)
      router.push('/agent/my-bookings')
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create booking'
    } finally {
      submitting.value = false
    }
  }
</script>
<!-- <script setup>
import { ref } from 'vue'
import { useFlightStore } from '../../stores/flight'

const flightStore = useFlightStore()

const searchForm = ref({
  origin: '',
  destination: '',
  departure_date: '',
  passengers: 1
})

const searchResults = ref([])
const searching = ref(false)
const searched = ref(false)

const searchFlights = async () => {
  searching.value = true
  searched.value = false
  
  try {
    const results = await flightStore.searchFlights(searchForm.value)
    searchResults.value = results
    searched.value = true
  } catch (err) {
    console.error('Search failed:', err)
  } finally {
    searching.value = false
  }
}

const formatTime = (datetime) => {
  if (!datetime) return ''
  return new Date(datetime).toLocaleTimeString('en-US', { 
    hour: '2-digit', 
    minute: '2-digit' 
  })
}
</script> -->