<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Admin Dashboard</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Total Airports -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm">Total Airports</p>
            <p class="text-3xl font-bold text-blue-600">{{ stats.airports }}</p>
          </div>
          <div class="bg-blue-100 p-3 rounded-full">
            <span class="text-3xl">🏢</span>
          </div>
        </div>
      </div>

      <!-- Total Airlines -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm">Total Airlines</p>
            <p class="text-3xl font-bold text-green-600">{{ stats.airlines }}</p>
          </div>
          <div class="bg-green-100 p-3 rounded-full">
            <span class="text-3xl">✈️</span>
          </div>
        </div>
      </div>

      <!-- Total Flights -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm">Total Flights</p>
            <p class="text-3xl font-bold text-purple-600">{{ stats.flights }}</p>
          </div>
          <div class="bg-purple-100 p-3 rounded-full">
            <span class="text-3xl">🛫</span>
          </div>
        </div>
      </div>

      <!-- Total Bookings -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm">Total Bookings</p>
            <p class="text-3xl font-bold text-orange-600">{{ stats.bookings }}</p>
          </div>
          <div class="bg-orange-100 p-3 rounded-full">
            <span class="text-3xl">🎫</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
      <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <router-link 
          to="/admin/airports" 
          class="bg-blue-500 hover:bg-blue-600 text-white p-4 rounded-lg text-center transition"
        >
          <div class="text-2xl mb-2">🏢</div>
          <div class="font-semibold">Manage Airports</div>
        </router-link>
        <router-link 
          to="/admin/airlines" 
          class="bg-green-500 hover:bg-green-600 text-white p-4 rounded-lg text-center transition"
        >
          <div class="text-2xl mb-2">✈️</div>
          <div class="font-semibold">Manage Airlines</div>
        </router-link>
        <router-link 
          to="/admin/aircraft" 
          class="bg-purple-500 hover:bg-purple-600 text-white p-4 rounded-lg text-center transition"
        >
          <div class="text-2xl mb-2">🛩️</div>
          <div class="font-semibold">Manage Aircraft</div>
        </router-link>
        <router-link 
          to="/admin/users" 
          class="bg-orange-500 hover:bg-orange-600 text-white p-4 rounded-lg text-center transition"
        >
          <div class="text-2xl mb-2">👥</div>
          <div class="font-semibold">Manage Users</div>
        </router-link>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-xl font-bold text-gray-800 mb-4">System Overview</h2>
      <p class="text-gray-600">Welcome to the Airport Management System Admin Dashboard. Use the navigation above to manage different aspects of the system.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const stats = ref({
  airports: 0,
  airlines: 0,
  flights: 0,
  bookings: 0
})

onMounted(() => {
  // In a real app, fetch these from the API
  stats.value = {
    airports: 3,
    airlines: 2,
    flights: 0,
    bookings: 0
  }
})
</script>