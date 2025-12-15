<template>
  <div>
    <div class="page-header">
      <h2 class="page-title">Flight Management</h2>
      <button @click="openCreateModal" class="btn btn-primary">
        <span>➕</span>
        Add Flight
      </button>
    </div>

    <!-- Search and Filters -->
    <div class="card filter-card">
      <div class="filter-grid-4">
        <input 
          v-model="searchQuery"
          @input="handleSearch"
          type="text"
          placeholder="Search flight number..."
          class="form-control"
        />
        <select 
          v-model="statusFilter"
          @change="handleSearch"
          class="form-control"
        >
          <option value="">All Status</option>
          <option value="scheduled">Scheduled</option>
          <option value="delayed">Delayed</option>
          <option value="cancelled">Cancelled</option>
          <option value="completed">Completed</option>
        </select>
        <input 
          v-model="dateFrom"
          @change="handleSearch"
          type="date"
          class="form-control"
          placeholder="From date"
        />
        <input 
          v-model="dateTo"
          @change="handleSearch"
          type="date"
          class="form-control"
          placeholder="To date"
        />
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="globalDataStore.loading.flights" class="loading-state">
      <div class="spinner"></div>
      <p>Loading flights...</p>
    </div>

    <!-- Flights Table -->
    <div v-else class="table-container">
      <table>
        <thead>
          <tr>
            <th>Flight #</th>
            <th>Airline</th>
            <th>Route</th>
            <th>Departure</th>
            <th>Arrival</th>
            <th>Aircraft</th>
            <th>Price</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="flight in filteredFlights" :key="flight.id">
            <td>
              <span class="code-badge flight-badge">{{ flight.flight_number }}</span>
            </td>
            <td>
              <strong>{{ flight.airline?.name || 'N/A' }}</strong>
            </td>
            <td>
              <div class="route-info">
                <div class="route-airports">
                  <span class="airport-code">{{ flight.origin_airport?.code || flight.origin }}</span>
                  <span class="route-arrow">✈️</span>
                  <span class="airport-code">{{ flight.destination_airport?.code || flight.destination }}</span>
                </div>
                <small class="text-muted">
                  {{ flight.origin_airport?.city || flight.origin }} → {{ flight.destination_airport?.city || flight.destination }}
                </small>
              </div>
            </td>
            <td>
              <div class="datetime-info">
                <div>{{ formatDate(flight.departure_time) }}</div>
                <small class="text-muted">{{ formatTime(flight.departure_time) }}</small>
              </div>
            </td>
            <td>
              <div class="datetime-info">
                <div>{{ formatDate(flight.arrival_time) }}</div>
                <small class="text-muted">{{ formatTime(flight.arrival_time) }}</small>
              </div>
            </td>
            <td>
              <small class="text-muted">{{ flight.aircraft?.registration_number || 'Not assigned' }}</small>
            </td>
            <td>
              <strong class="price-text">${{ parseFloat(flight.base_price).toFixed(2) }}</strong>
            </td>
            <td>
              <span class="badge" :class="getStatusClass(flight.status)">
                {{ flight.status }}
              </span>
            </td>
            <td>
              <div class="action-buttons">
                <button @click="openEditModal(flight)" class="btn-icon btn-edit" title="Edit">
                  ✏️
                </button>
                <button @click="handleDeleteFlight(flight.id)" class="btn-icon btn-delete" title="Delete">
                  🗑️
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Empty State -->
      <div v-if="filteredFlights.length === 0" class="empty-state">
        <div class="empty-icon">✈️</div>
        <p>No flights found</p>
        <button @click="openCreateModal" class="btn btn-primary">Add First Flight</button>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content modal-xlarge">
        <div class="modal-header">
          <h2>{{ isEditing ? 'Edit Flight' : 'Create New Flight' }}</h2>
          <button @click="closeModal" class="close-btn">✕</button>
        </div>
        
        <form @submit.prevent="handleSubmit">
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Flight Number *</label>
              <input 
                v-model="form.flight_number"
                type="text"
                class="form-control"
                placeholder="e.g., AA123"
                required
                @input="form.flight_number = form.flight_number.toUpperCase()"
              />
            </div>

            <div class="form-group">
              <label class="form-label">Airline *</label>
              <select v-model="form.airline_id" class="form-control" required>
                <option value="">Select Airline</option>
                <option v-for="airline in globalDataStore.airlines" :key="airline.id" :value="airline.id">
                  {{ airline.name }}
                </option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Origin Airport *</label>
              <select v-model="form.origin_airport_id" class="form-control" required>
                <option value="">Select Origin</option>
                <option v-for="airport in globalDataStore.airports" :key="airport.id" :value="airport.id">
                  {{ airport.code }} - {{ airport.name }}
                </option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Destination Airport *</label>
              <select v-model="form.destination_airport_id" class="form-control" required>
                <option value="">Select Destination</option>
                <option v-for="airport in globalDataStore.airports" :key="airport.id" :value="airport.id">
                  {{ airport.code }} - {{ airport.name }}
                </option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Departure Time *</label>
              <input 
                v-model="form.departure_time"
                type="datetime-local"
                class="form-control"
                required
              />
            </div>

            <div class="form-group">
              <label class="form-label">Arrival Time *</label>
              <input 
                v-model="form.arrival_time"
                type="datetime-local"
                class="form-control"
                required
              />
            </div>

            <div class="form-group">
              <label class="form-label">Aircraft</label>
              <select v-model="form.aircraft_id" class="form-control">
                <option value="">Select Aircraft (Optional)</option>
                <option v-for="aircraft in globalDataStore.aircraft" :key="aircraft.id" :value="aircraft.id">
                  {{ aircraft.registration_number }} - {{ aircraft.model }}
                </option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Base Price *</label>
              <input 
                v-model.number="form.base_price"
                type="number"
                step="0.01"
                min="0"
                class="form-control"
                required
              />
            </div>

            <div class="form-group">
              <label class="form-label">Status *</label>
              <select v-model="form.status" class="form-control" required>
                <option value="scheduled">Scheduled</option>
                <option value="delayed">Delayed</option>
                <option value="cancelled">Cancelled</option>
                <option value="completed">Completed</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Available Seats</label>
              <input 
                v-model.number="form.available_seats"
                type="number"
                min="0"
                class="form-control"
                placeholder="Auto-calculated from aircraft"
              />
            </div>
          </div>

          <div v-if="error" class="alert alert-error">{{ error }}</div>

          <div class="modal-footer">
            <button type="button" @click="closeModal" class="btn btn-secondary">Cancel</button>
            <button type="submit" :disabled="submitting" class="btn btn-primary">
              <span v-if="submitting" class="spinner-small"></span>
              <span v-else>{{ isEditing ? 'Update' : 'Create' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useGlobalDataStore } from '../../stores/globalData'
import axios from '../../api/axios'

const globalDataStore = useGlobalDataStore()

const showModal = ref(false)
const isEditing = ref(false)
const currentFlightId = ref(null)
const searchQuery = ref('')
const statusFilter = ref('')
const dateFrom = ref('')
const dateTo = ref('')
const submitting = ref(false)
const error = ref(null)

const form = ref({
  flight_number: '',
  airline_id: '',
  origin_airport_id: '',
  destination_airport_id: '',
  departure_time: '',
  arrival_time: '',
  aircraft_id: '',
  base_price: 0,
  status: 'scheduled',
  available_seats: null
})

const filteredFlights = computed(() => {
  let flights = [...globalDataStore.flights]
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    flights = flights.filter(f => f.flight_number.toLowerCase().includes(query))
  }
  
  if (statusFilter.value) {
    flights = flights.filter(f => f.status === statusFilter.value)
  }
  
  if (dateFrom.value) {
    flights = flights.filter(f => new Date(f.departure_time) >= new Date(dateFrom.value))
  }
  
  if (dateTo.value) {
    flights = flights.filter(f => new Date(f.departure_time) <= new Date(dateTo.value))
  }
  
  return flights
})

const handleSearch = () => {
  console.log('Filtered flights:', filteredFlights.value.length)
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString()
}

const formatTime = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

const getStatusClass = (status) => {
  const classes = {
    'scheduled': 'badge-success',
    'delayed': 'badge-warning',
    'cancelled': 'badge-danger',
    'completed': 'badge-info'
  }
  return classes[status] || 'badge-info'
}

const openCreateModal = () => {
  isEditing.value = false
  currentFlightId.value = null
  const now = new Date()
  const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16)
  
  form.value = {
    flight_number: '',
    airline_id: '',
    origin_airport_id: '',
    destination_airport_id: '',
    departure_time: localDateTime,
    arrival_time: localDateTime,
    aircraft_id: '',
    base_price: 0,
    status: 'scheduled',
    available_seats: null
  }
  error.value = null
  showModal.value = true
}

const openEditModal = (flight) => {
  isEditing.value = true
  currentFlightId.value = flight.id
  
  const departureDate = new Date(flight.departure_time)
  const arrivalDate = new Date(flight.arrival_time)
  const localDepartureDateTime = new Date(departureDate.getTime() - departureDate.getTimezoneOffset() * 60000).toISOString().slice(0, 16)
  const localArrivalDateTime = new Date(arrivalDate.getTime() - arrivalDate.getTimezoneOffset() * 60000).toISOString().slice(0, 16)
  
  form.value = {
    flight_number: flight.flight_number,
    airline_id: flight.airline_id,
    origin_airport_id: flight.origin_airport_id,
    destination_airport_id: flight.destination_airport_id,
    departure_time: localDepartureDateTime,
    arrival_time: localArrivalDateTime,
    aircraft_id: flight.aircraft_id || '',
    base_price: parseFloat(flight.base_price),
    status: flight.status,
    available_seats: flight.available_seats
  }
  error.value = null
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  error.value = null
}

const handleSubmit = async () => {
  submitting.value = true
  error.value = null
  
  try {
    const submitData = { ...form.value }
    if (!submitData.aircraft_id) {
      submitData.aircraft_id = null
    }
    if (!submitData.available_seats) {
      submitData.available_seats = null
    }
    
    if (isEditing.value) {
      const response = await axios.put(`/staff/flights/${currentFlightId.value}`, submitData)
      globalDataStore.updateItem('flights', currentFlightId.value, response.data.data || submitData)
      console.log('Flight updated:', response.data)
    } else {
      const response = await axios.post('/staff/flights', submitData)
      globalDataStore.addItem('flights', response.data.data || { ...submitData, id: Date.now() })
      console.log('Flight created:', response.data)
    }
    closeModal()
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to save flight'
    console.error('Submit error:', err.response?.data)
  } finally {
    submitting.value = false
  }
}

const handleDeleteFlight = async (id) => {
  if (!confirm('Are you sure you want to delete this flight?')) return
  
  try {
    await axios.delete(`/staff/flights/${id}`)
    globalDataStore.removeItem('flights', id)
    console.log('Flight deleted:', id)
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to delete flight')
  }
}
</script>

<style scoped>
.flight-badge {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.filter-grid-4 {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: 16px;
}

.route-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.route-airports {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
}

.airport-code {
  color: #2c3e50;
  font-size: 14px;
}

.route-arrow {
  font-size: 12px;
  opacity: 0.6;
}

.datetime-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.text-muted {
  color: #6c757d;
  font-size: 12px;
}

.price-text {
  color: #28a745;
  font-size: 14px;
}

.modal-xlarge {
  max-width: 900px;
  width: 90%;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.page-title {
  font-size: 28px;
  font-weight: 700;
  color: #2c3e50;
  margin: 0;
}

.filter-card {
  margin-bottom: 24px;
}

.loading-state {
  text-align: center;
  padding: 60px 20px;
}

.spinner {
  width: 50px;
  height: 50px;
  margin: 0 auto 20px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.code-badge {
  display: inline-block;
  padding: 6px 12px;
  color: white;
  border-radius: 6px;
  font-weight: 700;
  font-size: 12px;
}

.action-buttons {
  display: flex;
  gap: 8px;
}

.btn-icon {
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 16px;
  transition: all 0.3s ease;
}

.btn-edit {
  background: #e3f2fd;
}

.btn-edit:hover {
  background: #2196f3;
  transform: scale(1.1);
}

.btn-delete {
  background: #ffebee;
}

.btn-delete:hover {
  background: #f44336;
  transform: scale(1.1);
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-icon {
  font-size: 64px;
  margin-bottom: 16px;
  opacity: 0.5;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  padding: 24px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.modal-header h2 {
  margin: 0;
  font-size: 24px;
  color: #2c3e50;
}

.close-btn {
  width: 32px;
  height: 32px;
  border: none;
  background: #f5f5f5;
  border-radius: 8px;
  cursor: pointer;
  font-size: 20px;
  transition: all 0.3s ease;
}

.close-btn:hover {
  background: #e0e0e0;
  transform: rotate(90deg);
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-label {
  font-weight: 600;
  margin-bottom: 8px;
  color: #2c3e50;
}

.form-control {
  padding: 10px 12px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.3s ease;
}

.form-control:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.alert {
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.alert-error {
  background: #ffebee;
  color: #c62828;
  border: 1px solid #ef5350;
}

.modal-footer {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  margin-top: 24px;
}

.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
  background: #f5f5f5;
  color: #333;
}

.btn-secondary:hover {
  background: #e0e0e0;
}

.badge {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: capitalize;
}

.badge-success {
  background: #d4edda;
  color: #155724;
}

.badge-warning {
  background: #fff3cd;
  color: #856404;
}

.badge-danger {
  background: #f8d7da;
  color: #721c24;
}

.badge-info {
  background: #d1ecf1;
  color: #0c5460;
}

.table-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: #f8f9fa;
}

th {
  padding: 16px;
  text-align: left;
  font-weight: 600;
  color: #495057;
  font-size: 14px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

td {
  padding: 16px;
  border-top: 1px solid #e9ecef;
}

tbody tr:hover {
  background: #f8f9fa;
}

.card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.spinner-small {
  display: inline-block;
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
  .filter-grid-4 {
    grid-template-columns: 1fr;
  }
  
  .form-grid {
    grid-template-columns: 1fr;
  }
}
</style>

