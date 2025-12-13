<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="bg-green-600 text-white shadow-lg">
      <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
          <div class="flex items-center space-x-8">
            <h1 class="text-2xl font-bold">✈️ Staff Portal</h1>
            <div class="hidden md:flex space-x-6">
              <router-link to="/staff/dashboard" class="hover:text-green-200 transition">
                Dashboard
              </router-link>
              <router-link to="/staff/flights" class="hover:text-green-200 transition">
                Flights
              </router-link>
              <router-link to="/staff/bookings" class="hover:text-green-200 transition">
                Bookings
              </router-link>
              <router-link to="/staff/passengers" class="hover:text-green-200 transition">
                Passengers
              </router-link>
            </div>
          </div>
          
          <div class="flex items-center space-x-4">
            <span class="text-sm">{{ authStore.userName }}</span>
            <span class="bg-green-500 px-3 py-1 rounded-full text-xs">Staff</span>
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