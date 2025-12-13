<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Agent Dashboard</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <!-- My Bookings -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm">My Bookings</p>
            <p class="text-3xl font-bold text-purple-600">{{ stats.myBookings }}</p>
          </div>
          <div class="bg-purple-100 p-3 rounded-full">
            <span class="text-3xl">🎫</span>
          </div>
        </div>
      </div>

      <!-- Today's Bookings -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm">Today's Bookings</p>
            <p class="text-3xl font-bold text-green-600">{{ stats.todayBookings }}</p>
          </div>
          <div class="bg-green-100 p-3 rounded-full">
            <span class="text-3xl">📅</span>
          </div>
        </div>
      </div>
      <!-- Total Revenue -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm">Total Revenue</p>
            <p class="text-3xl font-bold text-blue-600">${{ stats.totalRevenue }}</p>
          </div>
          <div class="bg-blue-100 p-3 rounded-full">
            <span class="text-3xl">💰</span>
          </div>
        </div>
      </div>
    </div>
    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
      <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h2>
      <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <router-link to="/agent/search-flights"
          class="bg-purple-500 hover:bg-purple-600 text-white p-4 rounded-lg text-center transition">
          <div class="text-2xl mb-2">🔍</div>
          <div class="font-semibold">Search Flights</div>
        </router-link>
        <router-link to="/agent/create-booking"
          class="bg-green-500 hover:bg-green-600 text-white p-4 rounded-lg text-center transition">
          <div class="text-2xl mb-2">➕</div>
          <div class="font-semibold">New Booking</div>
        </router-link>
        <router-link to="/agent/my-bookings"
          class="bg-blue-500 hover:bg-blue-600 text-white p-4 rounded-lg text-center transition">
          <div class="text-2xl mb-2">📋</div>
          <div class="font-semibold">My Bookings</div>
        </router-link>
      </div>
    </div>
    <!-- Welcome Message -->
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-xl font-bold text-gray-800 mb-4">Welcome to Agent Portal</h2>
      <p class="text-gray-600">Start by searching for flights and creating bookings for your customers.</p>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
const stats = ref({
  myBookings: 0,
  todayBookings: 0,
  totalRevenue: 0
})
onMounted(() => {
  stats.value = {
    myBookings: 0,
    todayBookings: 0,
    totalRevenue: 0
  }
})
</script>