<template>
  <div class="fade-in">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-8 tracking-tight">Agent Dashboard</h1>
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
      <div v-for="card in statCards" :key="card.label" :class="card.bg + ' rounded-xl shadow-lg p-6 flex flex-col items-start justify-between relative overflow-hidden group transition-transform hover:scale-105'">
        <div class="absolute right-4 top-4 opacity-10 text-7xl group-hover:opacity-20 transition">{{ card.icon }}</div>
        <div class="z-10">
          <p class="text-gray-500 text-sm font-medium mb-2">{{ card.label }}</p>
          <div v-if="loadingStats" class="h-8 w-24 bg-gray-200 rounded animate-pulse mb-1"></div>
          <p v-else :class="card.text + ' text-4xl font-extrabold'">{{ card.value }}</p>
        </div>
      </div>
    </div>
    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-lg p-8 mb-10">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">Quick Actions</h2>
      <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
        <router-link to="/agent/search-flights" class="bg-purple-500 hover:bg-purple-600 text-white p-5 rounded-lg text-center font-semibold shadow transition flex flex-col items-center">
          <span class="text-3xl mb-2">🔍</span>
          Search Flights
        </router-link>
        <router-link to="/agent/create-booking" class="bg-green-500 hover:bg-green-600 text-white p-5 rounded-lg text-center font-semibold shadow transition flex flex-col items-center">
          <span class="text-3xl mb-2">➕</span>
          New Booking
        </router-link>
        <router-link to="/agent/my-bookings" class="bg-blue-500 hover:bg-blue-600 text-white p-5 rounded-lg text-center font-semibold shadow transition flex flex-col items-center">
          <span class="text-3xl mb-2">📋</span>
          My Bookings
        </router-link>
      </div>
    </div>
    <!-- Welcome Message -->
    <div class="bg-white rounded-xl shadow-lg p-8">
      <h2 class="text-2xl font-bold text-gray-900 mb-4">Welcome to Agent Portal</h2>
      <p class="text-gray-600">Start by searching for flights and creating bookings for your customers.</p>
    </div>
  </div>
</template>
<script setup>
import { ref, computed, onMounted } from 'vue'

const loadingStats = ref(true)
const stats = ref({
  myBookings: 0,
  todayBookings: 0,
  totalRevenue: 0
})

const statCards = computed(() => [
  {
    label: 'My Bookings',
    value: stats.value.myBookings,
    icon: '🎫',
    bg: 'bg-gradient-to-br from-purple-100 to-purple-50',
    text: 'text-purple-700'
  },
  {
    label: "Today's Bookings",
    value: stats.value.todayBookings,
    icon: '📅',
    bg: 'bg-gradient-to-br from-green-100 to-green-50',
    text: 'text-green-700'
  },
  {
    label: 'Total Revenue',
    value: `$${stats.value.totalRevenue}`,
    icon: '💰',
    bg: 'bg-gradient-to-br from-blue-100 to-blue-50',
    text: 'text-blue-700'
  }
])

onMounted(() => {
  // Simulate API call
  setTimeout(() => {
    stats.value = {
      myBookings: 0,
      todayBookings: 0,
      totalRevenue: 0
    }
    loadingStats.value = false
  }, 1200)
})
</script>

<style scoped>
.fade-in {
  animation: fadeIn 0.7s;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: none; }
}
.rounded-xl {
  border-radius: 1rem;
}
.shadow-lg {
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.12);
}
.transition-transform {
  transition: transform 0.2s cubic-bezier(.4,0,.2,1);
}
.group:hover .group-hover\:opacity-20 {
  opacity: 0.2;
}
.animate-pulse {
  animation: pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.4; }
}
</style>