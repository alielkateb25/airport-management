<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Staff Dashboard</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <!-- Today's Flights -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm">Today's Flights</p>
            <p class="text-3xl font-bold text-green-600">{{ stats.todayFlights }}</p>
          </div>
          <div class="bg-green-100 p-3 rounded-full">
            <span class="text-3xl">✈️</span>
          </div>
        </div>
      </div>

      <!-- Pending Bookings -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm">Pending Bookings</p>
            <p class="text-3xl font-bold text-yellow-600">{{ stats.pendingBookings }}</p>
          </div>
          <div class="bg-yellow-100 p-3 rounded-full">
            <span class="text-3xl">⏳</span>
          </div>
        </div>
      </div>

      <!-- Total Passengers -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm">Total Passengers</p>
            <p class="text-3xl font-bold text-purple-600">{{ stats.totalPassengers }}</p>
          </div>
          <div class="bg-purple-100 p-3 rounded-full">
            <span class="text-3xl">👥</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
      <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <router-link 
          to="/staff/flights" 
          class="bg-green-500 hover:bg-green-600 text-white p-4 rounded-lg text-center transition"
        >
          <div class="text-2xl mb-2">✈️</div>
          <div class="font-semibold">Manage Flights</div>
        </router-link>
        <router-link 
          to="/staff/bookings" 
          class="bg-blue-500 hover:bg-blue-600 text-white p-4 rounded-lg text-center transition"
        >
          <div class="text-2xl mb-2">🎫</div>
          <div class="font-semibold">View Bookings</div>
        </router-link>
        <router-link 
          to="/staff/passengers" 
          class="bg-purple-500 hover:bg-purple-600 text-white p-4 rounded-lg text-center transition"
        >
          <div class="text-2xl mb-2">👥</div>
          <div class="font-semibold">Manage Passengers</div>
        </router-link>
        <button 
          class="bg-orange-500 hover:bg-orange-600 text-white p-4 rounded-lg text-center transition"
        >
          <div class="text-2xl mb-2">📊</div>
          <div class="font-semibold">View Reports</div>
        </button>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-xl font-bold text-gray-800 mb-4">Welcome to Staff Portal</h2>
      <p class="text-gray-600">Use the navigation above to manage flights, bookings, and passengers.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const stats = ref({
  todayFlights: 0,
  pendingBookings: 0,
  totalPassengers: 0
})

onMounted(() => {
  // Fetch stats from API
  stats.value = {
    todayFlights: 0,
    pendingBookings: 0,
    totalPassengers: 0
  }
})
</script>