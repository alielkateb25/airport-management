<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="bg-purple-600 text-white shadow-lg">
      <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
          <div class="flex items-center space-x-8">
            <h1 class="text-2xl font-bold">✈️ Agent Portal</h1>
            <div class="hidden md:flex space-x-6">
              <router-link to="/agent/dashboard" class="hover:text-purple-200 transition">
                Dashboard
              </router-link>
              <router-link to="/agent/search-flights" class="hover:text-purple-200 transition">
                Search Flights
              </router-link>
              <router-link to="/agent/create-booking" class="hover:text-purple-200 transition">
                New Booking
              </router-link>
              <router-link to="/agent/my-bookings" class="hover:text-purple-200 transition">
                My Bookings
              </router-link>
              <router-link to="/agent/passengers" class="hover:text-purple-200 transition">
                Passengers
              </router-link>
            </div>
          </div>
          
          <div class="flex items-center space-x-4">
            <span class="text-sm">{{ authStore.userName }}</span>
            <span class="bg-purple-500 px-3 py-1 rounded-full text-xs">Agent</span>
            <button 
              @click="handleLogout" 
              class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded transition"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()

const handleLogout = () => {
  if (confirm('Are you sure you want to logout?')) {
    authStore.logout()
  }
}
</script>

<style scoped>
.router-link-active {
  font-weight: bold;
  text-decoration: underline;
}
</style>