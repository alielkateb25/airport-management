<!-- <template>
  <div>
    <div class="page-header">
      <h2 class="page-title">Booking Management</h2>
      <button @click="openCreateModal" class="btn btn-primary">
        <span>➕</span>
        Add Booking
      </button>
    </div>

    <div class="card filter-card">
      <div class="filter-grid-4">
        <input 
          v-model="searchQuery"
          @input="handleSearch"
          type="text"
          placeholder="Search by reference or passenger..."
          class="form-control"
        />
        <select 
          v-model="classFilter"
          @change="handleSearch"
          class="form-control"
        >
          <option value="">All Classes</option>
          <option value="economy">Economy</option>
          <option value="business">Business</option>
          <option value="first">First Class</option>
        </select>
        <select 
          v-model="statusFilter"
          @change="handleSearch"
          class="form-control"
        >
          <option value="">All Status</option>
          <option value="confirmed">Confirmed</option>
          <option value="pending">Pending</option>
          <option value="cancelled">Cancelled</option>
        </select>
        <input 
          v-model="dateFilter"
          @change="handleSearch"
          type="date"
          class="form-control"
        />
      </div>
    </div>

    <div v-if="globalDataStore.loading.bookings" class="loading-state">
      <div class="spinner"></div>
      <p>Loading bookings...</p>
    </div>

    <div v-else class="table-container">
      <table>
        <thead>
          <tr>
            <th>Reference</th>
            <th>Passenger</th>
            <th>Flight</th>
            <th>Class</th>
            <th>Seat</th>
            <th>Booking Date</th>
            <th>Price</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="booking in filteredBookings" :key="booking.id">
            <td>
              <span class="code-badge booking-badge">{{ booking.booking_reference }}</span>
            </td>
            <td>
              <div class="passenger-info">
                <strong>{{ booking.passenger?.name }}</strong>
                <small class="text-muted">{{ booking.passenger?.email }}</small>
              </div>
            </td>
            <td>
              <div class="flight-info">
                <strong>{{ booking.flight?.flight_number }}</strong>
                <small class="text-muted">
                  {{ booking.flight?.origin }} → {{ booking.flight?.destination }}
                </small>
              </div>
            </td>
            <td>
              <span class="badge" :class="getClassBadge(booking.class)">
                {{ formatClass(booking.class) }}
              </span>
            </td>
            <td>
              <span class="seat-badge">{{ booking.seat_number || 'Not assigned' }}</span>
            </td>
            <td>
              <div class="date-info">
                <div>{{ formatDate(booking.booking_date) }}</div>
                <small class="text-muted">{{ formatTime(booking.booking_date) }}</small>
              </div>
            </td>
            <td>
              <strong class="price-text">${{ parseFloat(booking.total_price).toFixed(2) }}</strong>
            </td>
            <td>
              <span class="badge" :class="getStatusClass(booking.status)">
                {{ booking.status }}
              </span>
            </td>
            <td>
              <div class="action-buttons">
                <button @click="openEditModal(booking)" class="btn-icon btn-edit" title="Edit">
                  ✏️
                </button>
                <button @click="cancelBooking(booking.id)" v-if="booking.status !== 'cancelled'" class="btn-icon btn-warning" title="Cancel">
                  ❌
                </button>
                <button @click="deleteBooking(booking.id)" class="btn-icon btn-delete" title="Delete">
                  🗑️
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="filteredBookings.length === 0" class="empty-state">
        <div class="empty-icon">🎫</div>
        <p>No bookings found</p>
        <button @click="openCreateModal" class="btn btn-primary">Add First Booking</button>
      </div>
    </div>

    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content modal-large">
        <div class="modal-header">
          <h2>{{ isEditing ? 'Edit Booking' : 'Create New Booking' }}</h2>
          <button @click="closeModal" class="close-btn">✕</button>
        </div>
        
        <form @submit.prevent="handleSubmit">
          <div class="form-grid">
            <div class="form-group full-width">
              <label class="form-label">Passenger *</label>
              <select v-model="form.passenger_id" class="form-control" required>
                <option value="">Select Passenger</option>
                <option v-for="passenger in globalDataStore.passengers" :key="passenger.id" :value="passenger.id">
                  {{ passenger.name }} ({{ passenger.email }})
                </option>
              </select>
            </div>

            <div class="form-group full-width">
              <label class="form-label">Flight *</label>
              <select v-model="form.flight_id" class="form-control" required>
                <option value="">Select Flight</option>
                <option v-for="flight in globalDataStore.flights" :key="flight.id" :value="flight.id">
                  {{ flight.flight_number }} - {{ flight.origin }} → {{ flight.destination }}
                </option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Class *</label>
              <select v-model="form.class" class="form-control" required>
                <option value="economy">Economy</option>
                <option value="business">Business</option>
                <option value="first">First Class</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Seat Number</label>
              <input 
                v-model="form.seat_number"
                type="text"
                class="form-control"
                placeholder="e.g., 12A"
                @input="form.seat_number = form.seat_number ? form.seat_number.toUpperCase() : ''"
              />
            </div>

            <div class="form-group">
              <label class="form-label">Booking Date *</label>
              <input 
                v-model="form.booking_date"
                type="datetime-local"
                class="form-control"
                required
              />
            </div>

            <div class="form-group">
              <label class="form-label">Total Price *</label>
              <input 
                v-model.number="form.total_price"
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
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>

            <div class="form-group" v-if="!isEditing">
              <label class="form-label">Booking Reference</label>
              <input 
                v-model="form.booking_reference"
                type="text"
                class="form-control"
                placeholder="Auto-generated if empty"
                @input="form.booking_reference = form.booking_reference ? form.booking_reference.toUpperCase() : ''"
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
const currentBookingId = ref(null)
const searchQuery = ref('')
const classFilter = ref('')
const statusFilter = ref('')
const dateFilter = ref('')
const submitting = ref(false)
const error = ref(null)

const form = ref({
  passenger_id: '',
  flight_id: '',
  booking_reference: '',
  seat_number: '',
  class: 'economy',
  booking_date: '',
  total_price: 0,
  status: 'pending'
})

const filteredBookings = computed(() => {
  let bookings = [...globalDataStore.bookings]
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    bookings = bookings.filter(b => 
      b.booking_reference.toLowerCase().includes(query) ||
      b.passenger?.name.toLowerCase().includes(query)
    )
  }
  
  if (classFilter.value) {
    bookings = bookings.filter(b => b.class === classFilter.value)
  }
  
  if (statusFilter.value) {
    bookings = bookings.filter(b => b.status === statusFilter.value)
  }
  
  if (dateFilter.value) {
    bookings = bookings.filter(b => {
      const bookingDate = new Date(b.booking_date).toISOString().split('T')[0]
      return bookingDate === dateFilter.value
    })
  }
  
  return bookings
})

const handleSearch = () => {
  console.log('Filtered bookings:', filteredBookings.value.length)
}

const formatClass = (className) => {
  const classes = {
    'economy': 'Economy',
    'business': 'Business',
    'first': 'First Class'
  }
  return classes[className] || className
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

const getClassBadge = (className) => {
  const badges = {
    'economy': 'badge-info',
    'business': 'badge-warning',
    'first': 'badge-premium'
  }
  return badges[className] || 'badge-info'
}

const getStatusClass = (status) => {
  const classes = {
    'confirmed': 'badge-success',
    'pending': 'badge-warning',
    'cancelled': 'badge-danger'
  }
  return classes[status] || 'badge-info'
}

const openCreateModal = () => {
  isEditing.value = false
  currentBookingId.value = null
  const now = new Date()
  const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16)
  
  form.value = {
    passenger_id: '',
    flight_id: '',
    booking_reference: '',
    seat_number: '',
    class: 'economy',
    booking_date: localDateTime,
    total_price: 0,
    status: 'pending'
  }
  error.value = null
  showModal.value = true
}

const openEditModal = (booking) => {
  isEditing.value = true
  currentBookingId.value = booking.id
  
  const bookingDate = new Date(booking.booking_date)
  const localDateTime = new Date(bookingDate.getTime() - bookingDate.getTimezoneOffset() * 60000).toISOString().slice(0, 16)
  
  form.value = {
    passenger_id: booking.passenger_id,
    flight_id: booking.flight_id,
    booking_reference: booking.booking_reference,
    seat_number: booking.seat_number || '',
    class: booking.class,
    booking_date: localDateTime,
    total_price: parseFloat(booking.total_price),
    status: booking.status
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
    if (isEditing.value) {
      const response = await axios.put(`/admin/bookings/${currentBookingId.value}`, form.value)
      globalDataStore.updateItem('bookings', currentBookingId.value, response.data.data || form.value)
      console.log('Booking updated:', response.data)
    } else {
      const response = await axios.post('/admin/bookings', form.value)
      globalDataStore.addItem('bookings', response.data.data || { ...form.value, id: Date.now() })
      console.log('Booking created:', response.data)
    }
    closeModal()
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to save booking'
    console.error('Submit error:', err)
  } finally {
    submitting.value = false
  }
}

const cancelBooking = async (id) => {
  if (!confirm('Are you sure you want to cancel this booking?')) return
  
  try {
    const response = await axios.put(`/admin/bookings/${id}`, { status: 'cancelled' })
    globalDataStore.updateItem('bookings', id, response.data.data || { status: 'cancelled' })
    console.log('Booking cancelled:', id)
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to cancel booking')
  }
}

const deleteBooking = async (id) => {
  if (!confirm('Are you sure you want to delete this booking?')) return
  
  try {
    await axios.delete(`/admin/bookings/${id}`)
    globalDataStore.removeItem('bookings', id)
    console.log('Booking deleted:', id)
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to delete booking')
  }
}
</script>

<style scoped>

.booking-badge {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.filter-grid-4 {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: 16px;
}

.passenger-info,
.flight-info,
.date-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.text-muted {
  color: #6c757d;
  font-size: 12px;
}

.seat-badge {
  display: inline-block;
  padding: 4px 8px;
  background: #f0f0f0;
  border-radius: 4px;
  font-weight: 600;
  font-size: 12px;
}

.price-text {
  color: #28a745;
  font-size: 14px;
}

.badge-premium {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  color: white;
}

.btn-warning {
  background: #fff3cd;
}

.btn-warning:hover {
  background: #ffc107;
  transform: scale(1.1);
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

.modal-large {
  max-width: 700px;
  width: 90%;
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

.full-width {
  grid-column: 1 / -1;
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
</style> -->

<template>
  <div>
    <div class="page-header">
      <h2 class="page-title">Booking Management</h2>
      <button @click="openCreateModal" class="btn btn-primary">
        <span>➕</span>
        Add Booking
      </button>
    </div>

    <!-- Search and Filters -->
    <div class="card filter-card">
      <div class="filter-grid-4">
        <input 
          v-model="searchQuery"
          @input="handleSearch"
          type="text"
          placeholder="Search by reference or passenger..."
          class="form-control"
        />
        <select 
          v-model="classFilter"
          @change="handleSearch"
          class="form-control"
        >
          <option value="">All Classes</option>
          <option value="economy">Economy</option>
          <option value="business">Business</option>
          <option value="first">First Class</option>
        </select>
        <select 
          v-model="statusFilter"
          @change="handleSearch"
          class="form-control"
        >
          <option value="">All Status</option>
          <option value="confirmed">Confirmed</option>
          <option value="pending">Pending</option>
          <option value="cancelled">Cancelled</option>
        </select>
        <input 
          v-model="dateFilter"
          @change="handleSearch"
          type="date"
          class="form-control"
        />
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="globalDataStore.loading.bookings" class="loading-state">
      <div class="spinner"></div>
      <p>Loading bookings...</p>
    </div>

    <!-- Bookings Table -->
    <div v-else class="table-container">
      <table>
        <thead>
          <tr>
            <th>Reference</th>
            <th>Passenger</th>
            <th>Flight</th>
            <th>Class</th>
            <th>Seat</th>
            <th>Booking Date</th>
            <th>Price</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="booking in filteredBookings" :key="booking.id">
            <td>
              <span class="code-badge booking-badge">{{ booking.booking_reference }}</span>
            </td>
            <td>
              <div class="passenger-info">
                <strong>{{ getPassengerName(booking.passenger_id) }}</strong>
                <small class="text-muted">{{ getPassengerEmail(booking.passenger_id) }}</small>
              </div>
            </td>
            <td>
              <div class="flight-info">
                <strong>{{ getFlightNumber(booking.flight_id) }}</strong>
                <small class="text-muted">{{ getFlightRoute(booking.flight_id) }}</small>
              </div>
            </td>
            <td>
              <span class="badge" :class="getClassBadge(booking.class)">
                {{ formatClass(booking.class) }}
              </span>
            </td>
            <td>
              <span class="seat-badge">{{ booking.seat_number || 'Not assigned' }}</span>
            </td>
            <td>
              <div class="date-info">
                <div>{{ formatDate(booking.booking_date) }}</div>
                <small class="text-muted">{{ formatTime(booking.booking_date) }}</small>
              </div>
            </td>
            <td>
              <strong class="price-text">${{ parseFloat(booking.total_price).toFixed(2) }}</strong>
            </td>
            <td>
              <span class="badge" :class="getStatusClass(booking.status)">
                {{ booking.status }}
              </span>
            </td>
            <td>
              <div class="action-buttons">
                <button @click="openEditModal(booking)" class="btn-icon btn-edit" title="Edit">
                  ✏️
                </button>
                <button 
                  @click="handleCancelBooking(booking.id)" 
                  v-if="booking.status !== 'cancelled'" 
                  class="btn-icon btn-warning" 
                  title="Cancel Booking"
                >
                  ❌
                </button>
                <button @click="handleDeleteBooking(booking.id)" class="btn-icon btn-delete" title="Delete">
                  🗑️
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Empty State -->
      <div v-if="filteredBookings.length === 0" class="empty-state">
        <div class="empty-icon">🎫</div>
        <p>No bookings found</p>
        <button @click="openCreateModal" class="btn btn-primary">Add First Booking</button>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content modal-large">
        <div class="modal-header">
          <h2>{{ isEditing ? 'Edit Booking' : 'Create New Booking' }}</h2>
          <button @click="closeModal" class="close-btn">✕</button>
        </div>
        
        <form @submit.prevent="handleSubmit">
          <div class="form-grid">
            <div class="form-group full-width">
              <label class="form-label">Passenger *</label>
              <div v-if="isEditing" class="passenger-display">
                <div class="passenger-avatar">{{ getPassengerInitials(form.passenger_id) }}</div>
                <div class="passenger-details">
                  <strong>{{ getPassengerName(form.passenger_id) }}</strong>
                  <small>{{ getPassengerEmail(form.passenger_id) }}</small>
                </div>
              </div>
              <select v-else v-model="form.passenger_id" class="form-control" required>
                <option value="">Select Passenger</option>
                <option v-for="passenger in globalDataStore.passengers" :key="passenger.id" :value="passenger.id">
                  {{ passenger.first_name }} {{ passenger.last_name }} ({{ passenger.email }})
                </option>
              </select>
            </div>

            <div class="form-group full-width">
              <label class="form-label">Flight *</label>
              <select v-model="form.flight_id" class="form-control" required>
                <option value="">Select Flight</option>
                <option v-for="flight in globalDataStore.flights" :key="flight.id" :value="flight.id">
                  {{ flight.flight_number }} - {{ getFlightRouteFromId(flight.id) }}
                </option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Class *</label>
              <select v-model="form.class" class="form-control" required>
                <option value="economy">Economy</option>
                <option value="business">Business</option>
                <option value="first">First Class</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Seat Number</label>
              <input 
                v-model="form.seat_number"
                type="text"
                class="form-control"
                placeholder="e.g., 12A"
                @input="form.seat_number = form.seat_number ? form.seat_number.toUpperCase() : ''"
              />
            </div>

            <div class="form-group">
              <label class="form-label">Booking Date *</label>
              <input 
                v-model="form.booking_date"
                type="datetime-local"
                class="form-control"
                required
              />
            </div>

            <div class="form-group">
              <label class="form-label">Total Price *</label>
              <input 
                v-model.number="form.total_price"
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
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>

            <div class="form-group" v-if="!isEditing">
              <label class="form-label">Booking Reference</label>
              <input 
                v-model="form.booking_reference"
                type="text"
                class="form-control"
                placeholder="Auto-generated if empty"
                @input="form.booking_reference = form.booking_reference ? form.booking_reference.toUpperCase() : ''"
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
const currentBookingId = ref(null)
const searchQuery = ref('')
const classFilter = ref('')
const statusFilter = ref('')
const dateFilter = ref('')
const submitting = ref(false)
const error = ref(null)

const form = ref({
  passenger_id: '',
  flight_id: '',
  booking_reference: '',
  seat_number: '',
  class: 'economy',
  booking_date: '',
  total_price: 0,
  status: 'pending'
})

// Computed filtered bookings
const filteredBookings = computed(() => {
  let bookings = [...globalDataStore.bookings]
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    bookings = bookings.filter(b => {
      const passengerName = getPassengerName(b.passenger_id).toLowerCase()
      return b.booking_reference.toLowerCase().includes(query) || passengerName.includes(query)
    })
  }
  
  if (classFilter.value) {
    bookings = bookings.filter(b => b.class === classFilter.value)
  }
  
  if (statusFilter.value) {
    bookings = bookings.filter(b => b.status === statusFilter.value)
  }
  
  if (dateFilter.value) {
    bookings = bookings.filter(b => {
      const bookingDate = new Date(b.booking_date).toISOString().split('T')[0]
      return bookingDate === dateFilter.value
    })
  }
  
  return bookings
})

// Helper functions to get related data
const getPassengerName = (passengerId) => {
  // First try to find in global store
  const passenger = globalDataStore.passengers.find(p => p.id === passengerId)
  if (passenger) {
    return `${passenger.first_name || ''} ${passenger.last_name || ''}`.trim() || passenger.name || 'Unknown'
  }
  
  // Fallback: check if booking has passenger data embedded
  const booking = globalDataStore.bookings.find(b => b.passenger_id === passengerId)
  if (booking?.passenger) {
    return `${booking.passenger.first_name || ''} ${booking.passenger.last_name || ''}`.trim() || booking.passenger.name || 'Unknown'
  }
  
  return 'Unknown'
}

const getPassengerEmail = (passengerId) => {
  // First try global store
  const passenger = globalDataStore.passengers.find(p => p.id === passengerId)
  if (passenger) return passenger.email || ''
  
  // Fallback to booking's embedded passenger data
  const booking = globalDataStore.bookings.find(b => b.passenger_id === passengerId)
  return booking?.passenger?.email || ''
}

const getPassengerInitials = (passengerId) => {
  const name = getPassengerName(passengerId)
  if (name === 'Unknown') return '?'
  
  const parts = name.split(' ')
  if (parts.length >= 2) {
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase()
  }
  return name.substring(0, 2).toUpperCase()
}

const getFlightNumber = (flightId) => {
  const flight = globalDataStore.flights.find(f => f.id === flightId)
  return flight?.flight_number || 'N/A'
}

const getFlightRoute = (flightId) => {
  const flight = globalDataStore.flights.find(f => f.id === flightId)
  if (!flight) return ''
  
  const origin = flight.origin_airport?.code || flight.origin || 'Unknown'
  const destination = flight.destination_airport?.code || flight.destination || 'Unknown'
  return `${origin} → ${destination}`
}

const getFlightRouteFromId = (flightId) => {
  const flight = globalDataStore.flights.find(f => f.id === flightId)
  if (!flight) return ''
  
  const origin = flight.origin_airport?.code || flight.origin || 'Unknown'
  const destination = flight.destination_airport?.code || flight.destination || 'Unknown'
  return `${origin} → ${destination}`
}

const handleSearch = () => {
  console.log('Filtered bookings:', filteredBookings.value.length)
}

const formatClass = (className) => {
  const classes = {
    'economy': 'Economy',
    'business': 'Business',
    'first': 'First Class'
  }
  return classes[className] || className
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

const getClassBadge = (className) => {
  const badges = {
    'economy': 'badge-info',
    'business': 'badge-warning',
    'first': 'badge-premium'
  }
  return badges[className] || 'badge-info'
}

const getStatusClass = (status) => {
  const classes = {
    'confirmed': 'badge-success',
    'pending': 'badge-warning',
    'cancelled': 'badge-danger'
  }
  return classes[status] || 'badge-info'
}

const openCreateModal = () => {
  isEditing.value = false
  currentBookingId.value = null
  const now = new Date()
  const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16)
  
  form.value = {
    passenger_id: '',
    flight_id: '',
    booking_reference: '',
    seat_number: '',
    class: 'economy',
    booking_date: localDateTime,
    total_price: 0,
    status: 'pending'
  }
  error.value = null
  showModal.value = true
}

const openEditModal = (booking) => {
  isEditing.value = true
  currentBookingId.value = booking.id
  
  const bookingDate = new Date(booking.booking_date)
  const localDateTime = new Date(bookingDate.getTime() - bookingDate.getTimezoneOffset() * 60000).toISOString().slice(0, 16)
  
  form.value = {
    passenger_id: booking.passenger_id,
    flight_id: booking.flight_id,
    booking_reference: booking.booking_reference,
    seat_number: booking.seat_number || '',
    class: booking.class,
    booking_date: localDateTime,
    total_price: parseFloat(booking.total_price),
    status: booking.status
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
    let response
    if (isEditing.value) {
      response = await axios.patch(`/staff/bookings/${currentBookingId.value}`, form.value)
      globalDataStore.updateItem('bookings', currentBookingId.value, response.data.data || form.value)
      console.log('Booking updated:', response.data)
    } else {
      response = await axios.post('/admin/bookings', form.value)
      globalDataStore.addItem('bookings', response.data.data || { ...form.value, id: Date.now() })
      console.log('Booking created:', response.data)
    }
    closeModal()
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to save booking'
    console.error('Submit error:', err)
  } finally {
    submitting.value = false
  }
}

const handleCancelBooking = async (id) => {
  if (!confirm('Are you sure you want to cancel this booking?')) return
  
  try {
    const response = await axios.put(`/staff/bookings/${id}`, { status: 'cancelled' })
    globalDataStore.updateItem('bookings', id, { status: 'cancelled' })
    console.log('Booking cancelled:', id)
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to cancel booking')
    console.error('Cancel error:', err)
  }
}

const handleDeleteBooking = async (id) => {
  if (!confirm('Are you sure you want to delete this booking? This action cannot be undone.')) return
  
  try {
    await axios.delete(`/staff/bookings/${id}`)
    globalDataStore.removeItem('bookings', id)
    console.log('Booking deleted:', id)
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to delete booking')
    console.error('Delete error:', err)
  }
}
</script>

<style scoped>
.booking-badge {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.filter-grid-4 {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: 16px;
}

.passenger-info,
.flight-info,
.date-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.text-muted {
  color: #6c757d;
  font-size: 12px;
}

.seat-badge {
  display: inline-block;
  padding: 4px 8px;
  background: #f0f0f0;
  border-radius: 4px;
  font-weight: 600;
  font-size: 12px;
}

.price-text {
  color: #28a745;
  font-size: 14px;
}

.badge-premium {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  color: white;
}

.btn-warning {
  background: #fff3cd;
}

.btn-warning:hover {
  background: #ffc107;
  transform: scale(1.1);
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

.modal-large {
  max-width: 700px;
  width: 90%;
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

.full-width {
  grid-column: 1 / -1;
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