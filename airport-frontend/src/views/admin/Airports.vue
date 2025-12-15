<template>
  <div>
    <div class="page-header">
      <h2 class="page-title">Airports Management</h2>
      <button @click="openCreateModal" class="btn btn-primary">
        <span>+</span>
        Add Airport
      </button>
    </div>

    <!-- Search and Filters -->
    <div class="card filter-card">
      <div class="filter-grid">
        <input 
          v-model="searchQuery"
          @input="handleSearch"
          type="text"
          placeholder="Search airports by name, code, city..."
          class="form-control"
        />
        <select 
          v-model="statusFilter"
          @change="handleSearch"
          class="form-control"
        >
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="globalDataStore.loading.airports" class="loading-state">
      <div class="spinner"></div>
      <p>Loading airports...</p>
    </div>

    <!-- Airports Table -->
    <div v-else class="table-container">
      <table>
        <thead>
          <tr>
            <th>Code</th>
            <th>Airport Name</th>
            <th>City</th>
            <th>Country</th>
            <th>Timezone</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="airport in filteredAirports" :key="airport.id">
            <td>
              <span class="code-badge">{{ airport.code }}</span>
            </td>
            <td>
              <strong>{{ airport.name }}</strong>
            </td>
            <td>{{ airport.city }}</td>
            <td>{{ airport.country }}</td>
            <td>
              <small class="text-muted">{{ airport.timezone }}</small>
            </td>
            <td>
              <span class="badge" :class="airport.status === 'active' ? 'badge-success' : 'badge-danger'">
                {{ airport.status }}
              </span>
            </td>
            <td>
              <div class="action-buttons">
                <button @click="openEditModal(airport)" class="btn-icon btn-edit" title="Edit">
                  ✏️
                </button>
                <button @click="handleDeleteAirport(airport.id)" class="btn-icon btn-delete" title="Delete">
                  🗑️
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Empty State -->
      <div v-if="filteredAirports.length === 0" class="empty-state">
        <div class="empty-icon">🏢</div>
        <p>No airports found</p>
        <button @click="openCreateModal" class="btn btn-primary">Add First Airport</button>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <div class="modal-header">
          <h2>{{ isEditing ? 'Edit Airport' : 'Add New Airport' }}</h2>
          <button @click="closeModal" class="close-btn">✕</button>
        </div>
        
        <form @submit.prevent="handleSubmit">
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Airport Code (IATA) *</label>
              <input 
                v-model="form.code"
                type="text"
                maxlength="3"
                class="form-control"
                placeholder="e.g., JFK"
                required
                @input="form.code = form.code.toUpperCase()"
              />
              <small class="form-hint">3-letter airport code</small>
            </div>

            <div class="form-group full-width">
              <label class="form-label">Airport Name *</label>
              <input 
                v-model="form.name"
                type="text"
                class="form-control"
                placeholder="e.g., John F. Kennedy International Airport"
                required
              />
            </div>

            <div class="form-group">
              <label class="form-label">City *</label>
              <input 
                v-model="form.city"
                type="text"
                class="form-control"
                placeholder="e.g., New York"
                required
              />
            </div>

            <div class="form-group">
              <label class="form-label">Country *</label>
              <input 
                v-model="form.country"
                type="text"
                class="form-control"
                placeholder="e.g., USA"
                required
              />
            </div>

            <div class="form-group">
              <label class="form-label">Timezone</label>
              <input 
                v-model="form.timezone"
                type="text"
                class="form-control"
                placeholder="e.g., America/New_York"
              />
            </div>

            <div class="form-group">
              <label class="form-label">Status *</label>
              <select v-model="form.status" class="form-control" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>

          <!-- Error Message -->
          <div v-if="error" class="alert alert-error">
            {{ error }}
          </div>

          <!-- Buttons -->
          <div class="modal-footer">
            <button type="button" @click="closeModal" class="btn btn-secondary">
              Cancel
            </button>
            <button type="submit" :disabled="submitting" class="btn btn-primary">
              <span v-if="submitting" class="spinner-small"></span>
              <span v-else>{{ isEditing ? 'Update Airport' : 'Create Airport' }}</span>
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
const currentAirportId = ref(null)
const searchQuery = ref('')
const statusFilter = ref('')
const submitting = ref(false)
const error = ref(null)

const form = ref({
  code: '',
  name: '',
  city: '',
  country: '',
  timezone: 'UTC',
  status: 'active'
})

// Computed filtered airports based on search and filters
const filteredAirports = computed(() => {
  let airports = [...globalDataStore.airports]
  
  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    airports = airports.filter(airport => 
      airport.code.toLowerCase().includes(query) ||
      airport.name.toLowerCase().includes(query) ||
      airport.city.toLowerCase().includes(query) ||
      airport.country.toLowerCase().includes(query)
    )
  }
  
  // Apply status filter
  if (statusFilter.value) {
    airports = airports.filter(airport => airport.status === statusFilter.value)
  }
  
  return airports
})

const handleSearch = () => {
  // Filtering is handled by computed property
  console.log('Filtered airports:', filteredAirports.value.length)
}

const openCreateModal = () => {
  isEditing.value = false
  currentAirportId.value = null
  form.value = {
    code: '',
    name: '',
    city: '',
    country: '',
    timezone: 'UTC',
    status: 'active'
  }
  error.value = null
  showModal.value = true
}

const openEditModal = (airport) => {
  isEditing.value = true
  currentAirportId.value = airport.id
  form.value = { ...airport }
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
      // Update existing airport
      const response = await axios.put(`/admin/airports/${currentAirportId.value}`, form.value)
      
      // Update in global store
      globalDataStore.updateItem('airports', currentAirportId.value, response.data.data || form.value)
      
      console.log('Airport updated:', response.data)
    } else {
      // Create new airport
      const response = await axios.post('/admin/airports', form.value)
      
      // Add to global store
      globalDataStore.addItem('airports', response.data.data || { ...form.value, id: Date.now() })
      
      console.log('Airport created:', response.data)
    }
    
    closeModal()
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to save airport'
    if (err.response?.data?.errors) {
      const errors = Object.values(err.response.data.errors).flat()
      error.value = errors.join(', ')
    }
    console.error('Submit error:', err)
  } finally {
    submitting.value = false
  }
}

const handleDeleteAirport = async (id) => {
  if (!confirm('Are you sure you want to delete this airport? This action cannot be undone.')) return
  
  try {
    await axios.delete(`/admin/airports/${id}`)
    
    // Remove from global store
    globalDataStore.removeItem('airports', id)
    
    console.log('Airport deleted:', id)
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to delete airport')
    console.error('Delete error:', err)
  }
}
</script>

<style scoped>
/* All the same styles as before */
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

.filter-grid {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 16px;
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
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 6px;
  font-weight: 700;
  font-size: 14px;
}

.text-muted {
  color: #7f8c8d;
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
  max-width: 600px;
  width: 90%;
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

.form-hint {
  display: block;
  margin-top: 6px;
  font-size: 12px;
  color: #7f8c8d;
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

.badge-danger {
  background: #f8d7da;
  color: #721c24;
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
</style>