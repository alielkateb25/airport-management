<!-- <template>
  <div>
    <div class="page-header">
      <h2 class="page-title">Admin Dashboard</h2>
      <div class="welcome-text">Welcome back, {{ userName }}</div>
    </div>

    <div class="stats-grid">
      <div class="stat-card card-airports">
        <div class="stat-icon">🏢</div>
        <div class="stat-content">
          <div class="stat-label">Total Airports</div>
          <div v-if="loadingStats" class="stat-skeleton"></div>
          <div v-else class="stat-value">{{ stats.airports }}</div>
          <div class="stat-subtitle">Active locations</div>
        </div>
      </div>

      <div class="stat-card card-airlines">
        <div class="stat-icon">✈️</div>
        <div class="stat-content">
          <div class="stat-label">Total Airlines</div>
          <div v-if="loadingStats" class="stat-skeleton"></div>
          <div v-else class="stat-value">{{ stats.airlines }}</div>
          <div class="stat-subtitle">Registered carriers</div>
        </div>
      </div>

      <div class="stat-card card-flights">
        <div class="stat-icon">🛫</div>
        <div class="stat-content">
          <div class="stat-label">Total Flights</div>
          <div v-if="loadingStats" class="stat-skeleton"></div>
          <div v-else class="stat-value">{{ stats.flights }}</div>
          <div class="stat-subtitle">Scheduled & active</div>
        </div>
      </div>

      <div class="stat-card card-bookings">
        <div class="stat-icon">🎫</div>
        <div class="stat-content">
          <div class="stat-label">Total Bookings</div>
          <div v-if="loadingStats" class="stat-skeleton"></div>
          <div v-else class="stat-value">{{ stats.bookings }}</div>
          <div class="stat-subtitle">All time bookings</div>
        </div>
      </div>
      <div class="stat-card card-aircraft">
        <div class="stat-icon">🛩️</div>
        <div class="stat-content">
          <div class="stat-label">Total Aircraft</div>
          <div v-if="loadingStats" class="stat-skeleton"></div>
          <div v-else class="stat-value">{{ stats.aircraft }}</div>
          <div class="stat-subtitle">Fleet size</div>
        </div>
      </div>

       <div class="stat-card card-passengers">
        <div class="stat-icon">👥</div>
        <div class="stat-content">
          <div class="stat-label">Total Passengers</div>
          <div v-if="loadingStats" class="stat-skeleton"></div>
          <div v-else class="stat-value">{{ stats.passengers }}</div>
          <div class="stat-subtitle">Registered users</div>
        </div>
      </div>

       <div class="stat-card card-users">
        <div class="stat-icon">👨‍💼</div>
        <div class="stat-content">
          <div class="stat-label">Staff Members</div>
          <div v-if="loadingStats" class="stat-skeleton"></div>
          <div v-else class="stat-value">{{ stats.users }}</div>
          <div class="stat-subtitle">Active staff</div>
        </div>
      </div>

       <div class="stat-card card-revenue">
        <div class="stat-icon">💰</div>
        <div class="stat-content">
          <div class="stat-label">Total Revenue</div>
          <div v-if="loadingStats" class="stat-skeleton"></div>
          <div v-else class="stat-value">${{ formatRevenue(stats.revenue) }}</div>
          <div class="stat-subtitle">All time earnings</div>
        </div>
      </div>
    </div>

    <div class="card quick-actions-card">
      <h3 class="section-title">Quick Actions</h3>
      <div class="quick-actions-grid">
        <router-link to="/admin/airports" class="quick-action-btn action-airports">
          <div class="action-icon">🏢</div>
          <div class="action-label">Manage Airports</div>
        </router-link>

        <router-link to="/admin/airlines" class="quick-action-btn action-airlines">
          <div class="action-icon">✈️</div>
          <div class="action-label">Manage Airlines</div>
        </router-link>

        <router-link to="/admin/aircraft" class="quick-action-btn action-aircraft">
          <div class="action-icon">🛩️</div>
          <div class="action-label">Manage Aircraft</div>
        </router-link>

        <router-link to="/admin/flights" class="quick-action-btn action-flights">
          <div class="action-icon">🛫</div>
          <div class="action-label">Manage Flights</div>
        </router-link>

        <router-link to="/admin/bookings" class="quick-action-btn action-bookings">
          <div class="action-icon">🎫</div>
          <div class="action-label">View Bookings</div>
        </router-link>

        <router-link to="/admin/users" class="quick-action-btn action-users">
          <div class="action-icon">👥</div>
          <div class="action-label">Manage Users</div>
        </router-link>
      </div>
    </div>

    <div class="card overview-card">
      <h3 class="section-title">System Overview</h3>
      <div class="overview-content">
        <p class="overview-text">
          Welcome to the <strong>Airport Management System</strong> Admin Dashboard. 
          This centralized hub provides you with real-time statistics and quick access 
          to all system management functions.
        </p>
        <div class="overview-features">
          <div class="feature-item">
            <span class="feature-icon">✅</span>
            <span>Manage airports, airlines, and aircraft fleet</span>
          </div>
          <div class="feature-item">
            <span class="feature-icon">✅</span>
            <span>Monitor flights and booking operations</span>
          </div>
          <div class="feature-item">
            <span class="feature-icon">✅</span>
            <span>Track system performance and revenue</span>
          </div>
          <div class="feature-item">
            <span class="feature-icon">✅</span>
            <span>Manage staff and user permissions</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from '../../api/axios'

const loadingStats = ref(true)
const stats = ref({
  airports: 0,
  airlines: 0,
  flights: 0,
  bookings: 0,
  aircraft: 0,
  passengers: 0,
  users: 0,
  revenue: 0
})

const userName = computed(() => {
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  return user.name || 'Admin'
})

onMounted(async () => {
  await fetchStats()
})

const fetchStats = async () => {
  loadingStats.value = true
  try {
    // Fetch all stats in parallel
    const [
      airportsRes,
      airlinesRes,
      flightsRes,
      bookingsRes,
      aircraftRes,
      passengersRes,
      usersRes
    ] = await Promise.all([
      axios.get('/airports').catch(() => ({ data: { data: [] } })),
      axios.get('/airlines').catch(() => ({ data: { data: [] } })),
      axios.get('/flights').catch(() => ({ data: { data: [] } })),
      //axios.get('/bookings').catch(() => ({ data: { data: [] } })),
      axios.get('/aircraft').catch(() => ({ data: { data: [] } })),
      //axios.get('/passengers').catch(() => ({ data: { data: [] } })),
      axios.get('/admin/users').catch(() => ({ data: { data: [] } }))
    ])

    stats.value.airports = airportsRes.data.data?.length || 0
    stats.value.airlines = airlinesRes.data.data?.length || 0
    stats.value.flights = flightsRes.data.data?.length || 0
    //stats.value.bookings = bookingsRes.data.data?.length || 0
    stats.value.aircraft = aircraftRes.data.data?.length || 0
    //stats.value.passengers = passengersRes.data.data?.length || 0
    //stats.value.users = usersRes.data.data?.length || 0

    // Calculate revenue from bookings
    /* if (bookingsRes.data.data) {
      stats.value.revenue = bookingsRes.data.data.reduce((sum, booking) => {
        return sum + (parseFloat(booking.total_price) || 0)
      }, 0)
    } */

    console.log('Dashboard stats loaded:', stats.value)
  } catch (err) {
    console.error('Failed to fetch dashboard stats:', err)
  } finally {
    loadingStats.value = false
  }
}

const formatRevenue = (value) => {
  if (value >= 1000000) {
    return (value / 1000000).toFixed(2) + 'M'
  } else if (value >= 1000) {
    return (value / 1000).toFixed(1) + 'K'
  }
  return value.toFixed(2)
}
</script>

<style scoped>
.page-header {
  margin-bottom: 32px;
}

.page-title {
  font-size: 32px;
  font-weight: 700;
  color: #2c3e50;
  margin: 0 0 8px 0;
}

.welcome-text {
  font-size: 16px;
  color: #7f8c8d;
  font-weight: 500;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 24px;
  margin-bottom: 32px;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 20px;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.stat-card:hover::before {
  opacity: 1;
}

.stat-icon {
  font-size: 48px;
  line-height: 1;
  opacity: 0.9;
}

.stat-content {
  flex: 1;
}

.stat-label {
  font-size: 13px;
  color: #7f8c8d;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
}

.stat-value {
  font-size: 36px;
  font-weight: 700;
  color: #2c3e50;
  line-height: 1;
  margin-bottom: 4px;
}

.stat-subtitle {
  font-size: 12px;
  color: #95a5a6;
}

.stat-skeleton {
  height: 36px;
  width: 80px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
  border-radius: 6px;
  margin-bottom: 4px;
}

/* Card Color Accents */
.card-airports .stat-icon { filter: hue-rotate(200deg); }
.card-airlines .stat-icon { filter: hue-rotate(100deg); }
.card-flights .stat-icon { filter: hue-rotate(270deg); }
.card-bookings .stat-icon { filter: hue-rotate(30deg); }
.card-aircraft .stat-icon { filter: hue-rotate(280deg); }
.card-passengers .stat-icon { filter: hue-rotate(180deg); }
.card-users .stat-icon { filter: hue-rotate(320deg); }
.card-revenue .stat-icon { filter: hue-rotate(50deg); }

/* Quick Actions */
.quick-actions-card {
  margin-bottom: 32px;
}

.section-title {
  font-size: 20px;
  font-weight: 700;
  color: #2c3e50;
  margin: 0 0 24px 0;
}

.quick-actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 16px;
}

.quick-action-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 24px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 12px;
  text-decoration: none;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.quick-action-btn:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(102, 126, 234, 0.4);
}

.action-icon {
  font-size: 42px;
  margin-bottom: 12px;
}

.action-label {
  font-size: 14px;
  font-weight: 600;
  text-align: center;
}

/* Action Button Colors */
.action-airports { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.action-airlines { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); }
.action-aircraft { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
.action-flights { background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%); }
.action-bookings { background: linear-gradient(135deg, #fdcbf1 0%, #e6dee9 100%); }
.action-users { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }

/* Overview Card */
.overview-card {
  margin-bottom: 32px;
}

.overview-content {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.overview-text {
  color: #5a6c7d;
  line-height: 1.7;
  margin: 0;
}

.overview-features {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 12px;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #f8f9fa;
  border-radius: 8px;
  font-size: 14px;
  color: #2c3e50;
}

.feature-icon {
  font-size: 20px;
}

/* Shared Styles */
.card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

@keyframes loading {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }

  .quick-actions-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .overview-features {
    grid-template-columns: 1fr;
  }
}
</style> -->

<template>
  <div>
    <div class="page-header">
      <h2 class="page-title">Admin Dashboard</h2>
      <div class="welcome-text">Welcome back, {{ userName }}</div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card card-airports">
        <div class="stat-icon">🏢</div>
        <div class="stat-content">
          <div class="stat-label">Total Airports</div>
          <div v-if="globalDataStore.loading.airports" class="stat-skeleton"></div>
          <div v-else class="stat-value">{{ globalDataStore.stats.airports }}</div>
          <div class="stat-subtitle">Active locations</div>
        </div>
      </div>

      <div class="stat-card card-airlines">
        <div class="stat-icon">✈️</div>
        <div class="stat-content">
          <div class="stat-label">Total Airlines</div>
          <div v-if="globalDataStore.loading.airlines" class="stat-skeleton"></div>
          <div v-else class="stat-value">{{ globalDataStore.stats.airlines }}</div>
          <div class="stat-subtitle">Registered carriers</div>
        </div>
      </div>

      <div class="stat-card card-flights">
        <div class="stat-icon">🛫</div>
        <div class="stat-content">
          <div class="stat-label">Total Flights</div>
          <div v-if="globalDataStore.loading.flights" class="stat-skeleton"></div>
          <div v-else class="stat-value">{{ globalDataStore.stats.flights }}</div>
          <div class="stat-subtitle">Scheduled & active</div>
        </div>
      </div>

      <div class="stat-card card-bookings">
        <div class="stat-icon">🎫</div>
        <div class="stat-content">
          <div class="stat-label">Total Bookings</div>
          <div v-if="globalDataStore.loading.bookings" class="stat-skeleton"></div>
          <div v-else class="stat-value">{{ globalDataStore.stats.bookings }}</div>
          <div class="stat-subtitle">All time bookings</div>
        </div>
      </div>

      <div class="stat-card card-aircraft">
        <div class="stat-icon">🛩️</div>
        <div class="stat-content">
          <div class="stat-label">Total Aircraft</div>
          <div v-if="globalDataStore.loading.aircraft" class="stat-skeleton"></div>
          <div v-else class="stat-value">{{ globalDataStore.stats.aircraft }}</div>
          <div class="stat-subtitle">Fleet size</div>
        </div>
      </div>

      <div class="stat-card card-passengers">
        <div class="stat-icon">👥</div>
        <div class="stat-content">
          <div class="stat-label">Total Passengers</div>
          <div v-if="globalDataStore.loading.passengers" class="stat-skeleton"></div>
          <div v-else class="stat-value">{{ globalDataStore.stats.passengers }}</div>
          <div class="stat-subtitle">Registered users</div>
        </div>
      </div>

      <div class="stat-card card-users">
        <div class="stat-icon">👨‍💼</div>
        <div class="stat-content">
          <div class="stat-label">Staff Members</div>
          <div v-if="globalDataStore.loading.users" class="stat-skeleton"></div>
          <div v-else class="stat-value">{{ globalDataStore.stats.users }}</div>
          <div class="stat-subtitle">Active staff</div>
        </div>
      </div>

      <div class="stat-card card-revenue">
        <div class="stat-icon">💰</div>
        <div class="stat-content">
          <div class="stat-label">Total Revenue</div>
          <div v-if="globalDataStore.loading.bookings" class="stat-skeleton"></div>
          <div v-else class="stat-value">${{ formatRevenue(globalDataStore.stats.revenue) }}</div>
          <div class="stat-subtitle">All time earnings</div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="card quick-actions-card">
      <h3 class="section-title">Quick Actions</h3>
      <div class="quick-actions-grid">
        <router-link to="/admin/airports" class="quick-action-btn action-airports">
          <div class="action-icon">🏢</div>
          <div class="action-label">Manage Airports</div>
        </router-link>

        <router-link to="/admin/airlines" class="quick-action-btn action-airlines">
          <div class="action-icon">✈️</div>
          <div class="action-label">Manage Airlines</div>
        </router-link>

        <router-link to="/admin/aircraft" class="quick-action-btn action-aircraft">
          <div class="action-icon">🛩️</div>
          <div class="action-label">Manage Aircraft</div>
        </router-link>

        <router-link to="/admin/flights" class="quick-action-btn action-flights">
          <div class="action-icon">🛫</div>
          <div class="action-label">Manage Flights</div>
        </router-link>

        <router-link to="/admin/bookings" class="quick-action-btn action-bookings">
          <div class="action-icon">🎫</div>
          <div class="action-label">View Bookings</div>
        </router-link>

        <router-link to="/admin/users" class="quick-action-btn action-users">
          <div class="action-icon">👥</div>
          <div class="action-label">Manage Users</div>
        </router-link>
      </div>
    </div>

    <!-- System Overview -->
    <div class="card overview-card">
      <h3 class="section-title">System Overview</h3>
      <div class="overview-content">
        <p class="overview-text">
          Welcome to the <strong>Airport Management System</strong> Admin Dashboard. 
          This centralized hub provides you with real-time statistics and quick access 
          to all system management functions.
        </p>
        <div class="overview-features">
          <div class="feature-item">
            <span class="feature-icon">✅</span>
            <span>Manage airports, airlines, and aircraft fleet</span>
          </div>
          <div class="feature-item">
            <span class="feature-icon">✅</span>
            <span>Monitor flights and booking operations</span>
          </div>
          <div class="feature-item">
            <span class="feature-icon">✅</span>
            <span>Track system performance and revenue</span>
          </div>
          <div class="feature-item">
            <span class="feature-icon">✅</span>
            <span>Manage staff and user permissions</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useGlobalDataStore } from '../../stores/globalData'

const globalDataStore = useGlobalDataStore()

const userName = computed(() => {
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  return user.name || 'Admin'
})

const formatRevenue = (value) => {
  if (value >= 1000000) {
    return (value / 1000000).toFixed(2) + 'M'
  } else if (value >= 1000) {
    return (value / 1000).toFixed(1) + 'K'
  }
  return value.toFixed(2)
}
</script>

<style scoped>
.page-header {
  margin-bottom: 32px;
}

.page-title {
  font-size: 32px;
  font-weight: 700;
  color: #2c3e50;
  margin: 0 0 8px 0;
}

.welcome-text {
  font-size: 16px;
  color: #7f8c8d;
  font-weight: 500;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 24px;
  margin-bottom: 32px;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 20px;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.stat-card:hover::before {
  opacity: 1;
}

.stat-icon {
  font-size: 48px;
  line-height: 1;
  opacity: 0.9;
}

.stat-content {
  flex: 1;
}

.stat-label {
  font-size: 13px;
  color: #7f8c8d;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
}

.stat-value {
  font-size: 36px;
  font-weight: 700;
  color: #2c3e50;
  line-height: 1;
  margin-bottom: 4px;
}

.stat-subtitle {
  font-size: 12px;
  color: #95a5a6;
}

.stat-skeleton {
  height: 36px;
  width: 80px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
  border-radius: 6px;
  margin-bottom: 4px;
}

/* Card Color Accents */
.card-airports .stat-icon { filter: hue-rotate(200deg); }
.card-airlines .stat-icon { filter: hue-rotate(100deg); }
.card-flights .stat-icon { filter: hue-rotate(270deg); }
.card-bookings .stat-icon { filter: hue-rotate(30deg); }
.card-aircraft .stat-icon { filter: hue-rotate(280deg); }
.card-passengers .stat-icon { filter: hue-rotate(180deg); }
.card-users .stat-icon { filter: hue-rotate(320deg); }
.card-revenue .stat-icon { filter: hue-rotate(50deg); }

/* Quick Actions */
.quick-actions-card {
  margin-bottom: 32px;
}

.section-title {
  font-size: 20px;
  font-weight: 700;
  color: #2c3e50;
  margin: 0 0 24px 0;
}

.quick-actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 16px;
}

.quick-action-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 24px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 12px;
  text-decoration: none;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.quick-action-btn:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(102, 126, 234, 0.4);
}

.action-icon {
  font-size: 42px;
  margin-bottom: 12px;
}

.action-label {
  font-size: 14px;
  font-weight: 600;
  text-align: center;
}

/* Action Button Colors */
.action-airports { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.action-airlines { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); }
.action-aircraft { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
.action-flights { background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%); }
.action-bookings { background: linear-gradient(135deg, #fdcbf1 0%, #e6dee9 100%); }
.action-users { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }

/* Overview Card */
.overview-card {
  margin-bottom: 32px;
}

.overview-content {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.overview-text {
  color: #5a6c7d;
  line-height: 1.7;
  margin: 0;
}

.overview-features {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 12px;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #f8f9fa;
  border-radius: 8px;
  font-size: 14px;
  color: #2c3e50;
}

.feature-icon {
  font-size: 20px;
}

/* Shared Styles */
.card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

@keyframes loading {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }

  .quick-actions-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .overview-features {
    grid-template-columns: 1fr;
  }
}
</style>