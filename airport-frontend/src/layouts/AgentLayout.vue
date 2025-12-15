<template>
  <div class="dashboard-container">
    <aside class="sidebar sidebar-agent" :class="{ 'sidebar-collapsed': sidebarCollapsed }">
      <div class="sidebar-header">
        <div class="logo">
          <span class="logo-icon">✈️</span>
          <span class="logo-text" v-show="!sidebarCollapsed">Agent Portal</span>
        </div>
        <button @click="toggleSidebar" class="toggle-btn">
          <span v-if="sidebarCollapsed">→</span>
          <span v-else>←</span>
        </button>
      </div>

      <nav class="sidebar-nav">
        <router-link to="/agent/dashboard" class="sidebar-link">
          <span class="link-icon">📊</span>
          <span class="link-text" v-show="!sidebarCollapsed">Dashboard</span>
        </router-link>
        
        <router-link to="/agent/search-flights" class="sidebar-link">
          <span class="link-icon">🔍</span>
          <span class="link-text" v-show="!sidebarCollapsed">Search Flights</span>
        </router-link>
        
        <router-link to="/agent/create-booking" class="sidebar-link">
          <span class="link-icon">➕</span>
          <span class="link-text" v-show="!sidebarCollapsed">New Booking</span>
        </router-link>
        
        <router-link to="/agent/my-bookings" class="sidebar-link">
          <span class="link-icon">🎫</span>
          <span class="link-text" v-show="!sidebarCollapsed">My Bookings</span>
        </router-link>
        
        <router-link to="/agent/passengers" class="sidebar-link">
          <span class="link-icon">👥</span>
          <span class="link-text" v-show="!sidebarCollapsed">Passengers</span>
        </router-link>
      </nav>
    </aside>

    <div class="main-content">
      <header class="navbar navbar-agent">
        <div class="navbar-left">
          <h1 class="page-title">{{ pageTitle }}</h1>
        </div>
        
        <div class="navbar-right">
          <div class="user-menu">
            <div class="user-info">
              <span class="user-avatar agent-avatar">{{ userInitials }}</span>
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
    'AgentDashboard': 'Dashboard',
    'AgentSearchFlights': 'Search Flights',
    'AgentCreateBooking': 'Create Booking',
    'AgentMyBookings': 'My Bookings',
    'AgentPassengers': 'Passengers Management'
  }
  return titles[route.name] || 'Agent Portal'
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
.sidebar-agent {
  background: linear-gradient(180deg, #8e44ad 0%, #9b59b6 100%);
}

.agent-avatar {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.navbar-agent {
  border-bottom: 3px solid #9b59b6;
}

/* Copy all other styles from AdminLayout */
.dashboard-container {
  display: flex;
  height: 100vh;
  background: #f5f7fa;
}

.sidebar {
  width: 260px;
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
</style>