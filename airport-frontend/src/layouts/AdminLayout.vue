<!-- <template>
  <div class="dashboard-container">
    <aside class="sidebar" :class="{ 'sidebar-collapsed': sidebarCollapsed }">
      <div class="sidebar-header">
        <div class="logo">
          <span class="logo-icon">✈️</span>
          <span class="logo-text" v-show="!sidebarCollapsed">Airport MS</span>
        </div>
        <button @click="toggleSidebar" class="toggle-btn">
          <span v-if="sidebarCollapsed">→</span>
          <span v-else>←</span>
        </button>
      </div>

      <nav class="sidebar-nav">
        <router-link to="/admin/dashboard" class="sidebar-link">
          <span class="link-icon">📊</span>
          <span class="link-text" v-show="!sidebarCollapsed">Dashboard</span>
        </router-link>
        
        <router-link to="/admin/airports" class="sidebar-link">
          <span class="link-icon">🏢</span>
          <span class="link-text" v-show="!sidebarCollapsed">Airports</span>
        </router-link>
        
        <router-link to="/admin/airlines" class="sidebar-link">
          <span class="link-icon">✈️</span>
          <span class="link-text" v-show="!sidebarCollapsed">Airlines</span>
        </router-link>
        
        <router-link to="/admin/aircraft" class="sidebar-link">
          <span class="link-icon">🛩️</span>
          <span class="link-text" v-show="!sidebarCollapsed">Aircraft</span>
        </router-link>
        
        <router-link to="/admin/flights" class="sidebar-link">
          <span class="link-icon">🛫</span>
          <span class="link-text" v-show="!sidebarCollapsed">Flights</span>
        </router-link>
        
        <router-link to="/admin/bookings" class="sidebar-link">
          <span class="link-icon">🎫</span>
          <span class="link-text" v-show="!sidebarCollapsed">Bookings</span>
        </router-link>
        
        <router-link to="/admin/users" class="sidebar-link">
          <span class="link-icon">👥</span>
          <span class="link-text" v-show="!sidebarCollapsed">Users</span>
        </router-link>
      </nav>
    </aside>

    <div class="main-content">
      <header class="navbar">
        <div class="navbar-left">
          <h1 class="page-title">{{ pageTitle }}</h1>
        </div>
        
        <div class="navbar-right">
          <div class="user-menu">
            <div class="user-info">
              <span class="user-avatar">{{ userInitials }}</span>
              <div class="user-details">
                <span class="user-name">{{ authStore.userName }}</span>
                <span class="user-email">{{ authStore.user?.email }}</span>
              </div>
            </div>
            <button @click="handleLogout" class="logout-btn">
              <span>🚪</span>
              Logout
            </button>
          </div>
        </div>
      </header>

      <main class="content-area">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const route = useRoute()
const sidebarCollapsed = ref(false)

const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value
}

const pageTitle = computed(() => {
  const titles = {
    'AdminDashboard': 'Dashboard',
    'AdminAirports': 'Airports Management',
    'AdminAirlines': 'Airlines Management',
    'AdminAircraft': 'Aircraft Management',
    'AdminFlights': 'Flights Management',
    'AdminBookings': 'Bookings Management',
    'AdminUsers': 'Users Management'
  }
  return titles[route.name] || 'Admin Panel'
})

const userInitials = computed(() => {
  const name = authStore.userName || 'User'
  return name.split(' ').map(n => n[0]).join('').toUpperCase()
})

const handleLogout = () => {
  if (confirm('Are you sure you want to logout?')) {
    authStore.logout()
  }
}
</script>

<style scoped>
.dashboard-container {
  display: flex;
  height: 100vh;
  background: #f5f7fa;
}

.sidebar {
  width: 260px;
  background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
  color: white;
  display: flex;
  flex-direction: column;
  transition: width 0.3s ease;
  position: relative;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.sidebar-collapsed {
  width: 80px;
}

.sidebar-header {
  padding: 24px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.logo {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 20px;
  font-weight: 700;
}

.logo-icon {
  font-size: 28px;
}

.toggle-btn {
  background: rgba(255, 255, 255, 0.1);
  border: none;
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.toggle-btn:hover {
  background: rgba(255, 255, 255, 0.2);
}

.sidebar-nav {
  flex: 1;
  padding: 20px 0;
  overflow-y: auto;
}

.sidebar-link {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 14px 20px;
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  transition: all 0.3s ease;
  margin: 4px 12px;
  border-radius: 10px;
  font-size: 15px;
}

.sidebar-link:hover {
  background: rgba(255, 255, 255, 0.1);
  color: white;
  transform: translateX(5px);
}

.sidebar-link.router-link-active {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.link-icon {
  font-size: 22px;
  min-width: 22px;
}

.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.navbar {
  background: white;
  padding: 20px 32px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  z-index: 10;
}

.page-title {
  font-size: 24px;
  font-weight: 700;
  color: #2c3e50;
}

.navbar-right {
  display: flex;
  align-items: center;
  gap: 24px;
}

.user-menu {
  display: flex;
  align-items: center;
  gap: 16px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.user-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 16px;
  color: white;
}

.user-details {
  display: flex;
  flex-direction: column;
}

.user-name {
  font-weight: 600;
  font-size: 14px;
  color: #2c3e50;
}

.user-email {
  font-size: 12px;
  color: #7f8c8d;
}

.logout-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.3s ease;
}

.logout-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(235, 51, 73, 0.4);
}

.content-area {
  flex: 1;
  padding: 32px;
  overflow-y: auto;
  background: #f5f7fa;
}

/* Scrollbar */
.sidebar-nav::-webkit-scrollbar {
  width: 6px;
}

.sidebar-nav::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
}

.sidebar-nav::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 3px;
}
</style> -->

<template>
  <div class="dashboard-container">
    <!-- Global Loading Overlay -->
    <div v-if="globalDataStore.loading.all" class="global-loading-overlay">
      <div class="loading-content">
        <div class="loading-spinner"></div>
        <h3>Loading Airport Management System</h3>
        <p>Fetching all data...</p>
        <div class="loading-progress">
          <div class="progress-item" :class="{ loaded: globalDataStore.loaded.airports }">
            <span class="progress-icon">🏢</span>
            <span>Airports</span>
          </div>
          <div class="progress-item" :class="{ loaded: globalDataStore.loaded.airlines }">
            <span class="progress-icon">✈️</span>
            <span>Airlines</span>
          </div>
          <div class="progress-item" :class="{ loaded: globalDataStore.loaded.aircraft }">
            <span class="progress-icon">🛩️</span>
            <span>Aircraft</span>
          </div>
          <div class="progress-item" :class="{ loaded: globalDataStore.loaded.flights }">
            <span class="progress-icon">🛫</span>
            <span>Flights</span>
          </div>
          <div class="progress-item" :class="{ loaded: globalDataStore.loaded.bookings }">
            <span class="progress-icon">🎫</span>
            <span>Bookings</span>
          </div>
          <div class="progress-item" :class="{ loaded: globalDataStore.loaded.passengers }">
            <span class="progress-icon">👤</span>
            <span>Passengers</span>
          </div>
          <div class="progress-item" :class="{ loaded: globalDataStore.loaded.users }">
            <span class="progress-icon">👥</span>
            <span>Users</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <aside class="sidebar" :class="{ 'sidebar-collapsed': sidebarCollapsed }">
      <div class="sidebar-header">
        <div class="logo">
          <span class="logo-icon">✈️</span>
          <span class="logo-text" v-show="!sidebarCollapsed">Airport MS</span>
        </div>
        <button @click="toggleSidebar" class="toggle-btn">
          <span v-if="sidebarCollapsed">→</span>
          <span v-else>←</span>
        </button>
      </div>

      <nav class="sidebar-nav">
        <router-link to="/admin/dashboard" class="sidebar-link">
          <span class="link-icon">📊</span>
          <span class="link-text" v-show="!sidebarCollapsed">Dashboard</span>
        </router-link>
        
        <router-link to="/admin/airports" class="sidebar-link">
          <span class="link-icon">🏢</span>
          <span class="link-text" v-show="!sidebarCollapsed">Airports</span>
          <span class="badge-count" v-show="!sidebarCollapsed && globalDataStore.airports.length > 0">
            {{ globalDataStore.airports.length }}
          </span>
        </router-link>
        
        <router-link to="/admin/airlines" class="sidebar-link">
          <span class="link-icon">✈️</span>
          <span class="link-text" v-show="!sidebarCollapsed">Airlines</span>
          <span class="badge-count" v-show="!sidebarCollapsed && globalDataStore.airlines.length > 0">
            {{ globalDataStore.airlines.length }}
          </span>
        </router-link>
        
        <router-link to="/admin/aircraft" class="sidebar-link">
          <span class="link-icon">🛩️</span>
          <span class="link-text" v-show="!sidebarCollapsed">Aircraft</span>
          <span class="badge-count" v-show="!sidebarCollapsed && globalDataStore.aircraft.length > 0">
            {{ globalDataStore.aircraft.length }}
          </span>
        </router-link>
        
        <router-link to="/admin/flights" class="sidebar-link">
          <span class="link-icon">🛫</span>
          <span class="link-text" v-show="!sidebarCollapsed">Flights</span>
          <span class="badge-count" v-show="!sidebarCollapsed && globalDataStore.flights.length > 0">
            {{ globalDataStore.flights.length }}
          </span>
        </router-link>
        
        <router-link to="/admin/bookings" class="sidebar-link">
          <span class="link-icon">🎫</span>
          <span class="link-text" v-show="!sidebarCollapsed">Bookings</span>
          <span class="badge-count" v-show="!sidebarCollapsed && globalDataStore.bookings.length > 0">
            {{ globalDataStore.bookings.length }}
          </span>
        </router-link>
        
        <router-link to="/admin/users" class="sidebar-link">
          <span class="link-icon">👥</span>
          <span class="link-text" v-show="!sidebarCollapsed">Users</span>
          <span class="badge-count" v-show="!sidebarCollapsed && globalDataStore.users.length > 0">
            {{ globalDataStore.users.length }}
          </span>
        </router-link>
      </nav>

      <!-- Refresh Data Button -->
      <div class="sidebar-footer" v-show="!sidebarCollapsed">
        <button @click="refreshAllData" class="refresh-btn" :disabled="globalDataStore.loading.all">
          <span v-if="!globalDataStore.loading.all">🔄</span>
          <span v-else class="spinning">⟳</span>
          <span class="refresh-text">Refresh Data</span>
        </button>
        <div class="last-updated" v-if="lastUpdateTime">
          <small>Updated: {{ lastUpdateTime }}</small>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Navbar -->
      <header class="navbar">
        <div class="navbar-left">
          <h1 class="page-title">{{ pageTitle }}</h1>
        </div>
        
        <div class="navbar-right">
          <div class="user-menu">
            <div class="user-info">
              <span class="user-avatar">{{ userInitials }}</span>
              <div class="user-details">
                <span class="user-name">{{ authStore.userName }}</span>
                <span class="user-email">{{ authStore.user?.email }}</span>
              </div>
            </div>
            <button @click="handleLogout" class="logout-btn">
              <span>🚪</span>
              Logout
            </button>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="content-area">
        <router-view v-if="globalDataStore.isAllLoaded || !isFirstLoad" />
        <div v-else class="content-loading">
          <div class="spinner"></div>
          <p>Loading page data...</p>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useGlobalDataStore } from '../stores/globalData'

const authStore = useAuthStore()
const globalDataStore = useGlobalDataStore()
const route = useRoute()
const sidebarCollapsed = ref(false)
const isFirstLoad = ref(true)

// Fetch all data when layout mounts
onMounted(async () => {
  console.log('AdminLayout mounted - fetching all data...')
  await globalDataStore.fetchAllData()
  isFirstLoad.value = false
  console.log('All data loaded:', {
    airports: globalDataStore.airports.length,
    airlines: globalDataStore.airlines.length,
    aircraft: globalDataStore.aircraft.length,
    flights: globalDataStore.flights.length,
    bookings: globalDataStore.bookings.length,
    passengers: globalDataStore.passengers.length,
    users: globalDataStore.users.length
  })
})

const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value
}

const pageTitle = computed(() => {
  const titles = {
    'AdminDashboard': 'Dashboard',
    'AdminAirports': 'Airports Management',
    'AdminAirlines': 'Airlines Management',
    'AdminAircraft': 'Aircraft Management',
    'AdminFlights': 'Flights Management',
    'AdminBookings': 'Bookings Management',
    'AdminUsers': 'Users Management'
  }
  return titles[route.name] || 'Admin Panel'
})

const userInitials = computed(() => {
  const name = authStore.userName || 'User'
  return name.split(' ').map(n => n[0]).join('').toUpperCase()
})

const lastUpdateTime = computed(() => {
  const times = Object.values(globalDataStore.lastUpdated).filter(t => t !== null)
  if (times.length === 0) return null
  
  const latest = new Date(Math.max(...times.map(t => new Date(t))))
  return latest.toLocaleTimeString()
})

const refreshAllData = async () => {
  console.log('Refreshing all data...')
  await globalDataStore.fetchAllData(true) // Force refresh
  console.log('Data refreshed!')
}

const handleLogout = () => {
  if (confirm('Are you sure you want to logout?')) {
    authStore.logout()
  }
}
</script>

<style scoped>
/* Global Loading Overlay */
.global-loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.98);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  backdrop-filter: blur(10px);
}

.loading-content {
  text-align: center;
  max-width: 600px;
}

.loading-spinner {
  width: 60px;
  height: 60px;
  margin: 0 auto 24px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.loading-content h3 {
  font-size: 24px;
  color: #2c3e50;
  margin-bottom: 8px;
}

.loading-content p {
  color: #7f8c8d;
  margin-bottom: 32px;
}

.loading-progress {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 16px;
}

.progress-item {
  padding: 12px;
  background: #f8f9fa;
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  opacity: 0.5;
  transition: all 0.3s ease;
}

.progress-item.loaded {
  background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
  opacity: 1;
}

.progress-icon {
  font-size: 24px;
}

.progress-item span:last-child {
  font-size: 12px;
  font-weight: 600;
  color: #2c3e50;
}

/* Content Loading */
.content-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 400px;
}

.spinner {
  width: 50px;
  height: 50px;
  margin-bottom: 20px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

/* Dashboard Container */
.dashboard-container {
  display: flex;
  height: 100vh;
  background: #f5f7fa;
}

.sidebar {
  width: 260px;
  background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
  color: white;
  display: flex;
  flex-direction: column;
  transition: width 0.3s ease;
  position: relative;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.sidebar-collapsed {
  width: 80px;
}

.sidebar-header {
  padding: 24px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.logo {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 20px;
  font-weight: 700;
}

.logo-icon {
  font-size: 28px;
}

.toggle-btn {
  background: rgba(255, 255, 255, 0.1);
  border: none;
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.toggle-btn:hover {
  background: rgba(255, 255, 255, 0.2);
}

.sidebar-nav {
  flex: 1;
  padding: 20px 0;
  overflow-y: auto;
}

.sidebar-link {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 14px 20px;
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  transition: all 0.3s ease;
  margin: 4px 12px;
  border-radius: 10px;
  font-size: 15px;
  position: relative;
}

.sidebar-link:hover {
  background: rgba(255, 255, 255, 0.1);
  color: white;
  transform: translateX(5px);
}

.sidebar-link.router-link-active {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.link-icon {
  font-size: 22px;
  min-width: 22px;
}

.badge-count {
  margin-left: auto;
  background: rgba(255, 255, 255, 0.2);
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}

.sidebar-footer {
  padding: 20px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.refresh-btn {
  width: 100%;
  padding: 12px;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: white;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-size: 14px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.refresh-btn:hover:not(:disabled) {
  background: rgba(255, 255, 255, 0.2);
}

.refresh-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.spinning {
  display: inline-block;
  animation: spin 1s linear infinite;
}

.last-updated {
  text-align: center;
  margin-top: 8px;
  color: rgba(255, 255, 255, 0.6);
}

.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.navbar {
  background: white;
  padding: 20px 32px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  z-index: 10;
}

.page-title {
  font-size: 24px;
  font-weight: 700;
  color: #2c3e50;
}

.navbar-right {
  display: flex;
  align-items: center;
  gap: 24px;
}

.user-menu {
  display: flex;
  align-items: center;
  gap: 16px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.user-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 16px;
  color: white;
}

.user-details {
  display: flex;
  flex-direction: column;
}

.user-name {
  font-weight: 600;
  font-size: 14px;
  color: #2c3e50;
}

.user-email {
  font-size: 12px;
  color: #7f8c8d;
}

.logout-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.3s ease;
}

.logout-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(235, 51, 73, 0.4);
}

.content-area {
  flex: 1;
  padding: 32px;
  overflow-y: auto;
  background: #f5f7fa;
}

/* Scrollbar */
.sidebar-nav::-webkit-scrollbar,
.content-area::-webkit-scrollbar {
  width: 6px;
}

.sidebar-nav::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
}

.sidebar-nav::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 3px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>