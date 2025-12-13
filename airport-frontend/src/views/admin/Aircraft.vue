<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Manage Aircraft</h1>
      <button 
        @click="openCreateModal"
        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition"
      >
        + Add Aircraft
      </button>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input 
          v-model="searchQuery"
          @input="handleSearch"
          type="text"
          placeholder="Search aircraft..."
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <select 
          v-model="airlineFilter"
          @change="handleSearch"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">All Airlines</option>
          <option v-for="airline in airlines" :key="airline.id" :value="airline.id">
            {{ airline.name }}
          </option>
        </select>
        <select 
          v-model="statusFilter"
          @change="handleSearch"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="maintenance">Maintenance</option>
          <option value="retired">Retired</option>
        </select>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="text-gray-600">Loading aircraft...</div>
    </div>

    <!-- Aircraft Table -->
    <div v-else class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registration</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Airline</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="aircraft in aircraftList" :key="aircraft.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
              {{ aircraft.registration_number }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ aircraft.model }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ aircraft.airline?.name }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              <div class="text-xs">
                <div>Economy: {{ aircraft.capacity_economy }}</div>
                <div>Business: {{ aircraft.capacity_business }}</div>
                <div>First: {{ aircraft.capacity_first }}</div>
                <div class="font-semibold mt-1">Total: {{ getTotalCapacity(aircraft) }}</div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                :class="{
                  'bg-green-100 text-green-800': aircraft.status === 'active',
                  'bg-yellow-100 text-yellow-800': aircraft.status === 'maintenance',
                  'bg-red-100 text-red-800': aircraft.status === 'retired'
                }"
                class="px-2 py-1 text-xs font-semibold rounded-full"
              >
                {{ aircraft.status }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
              <button 
                @click="openEditModal(aircraft)"
                class="text-blue-600 hover:text-blue-900"
              >
                Edit
              </button>
              <button 
                @click="deleteAircraft(aircraft.id)"
                class="text-red-600 hover:text-red-900"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Empty State -->
      <div v-if="aircraftList.length === 0" class="text-center py-12">
        <p class="text-gray-600">No aircraft found.</p>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
        <h2 class="text-2xl font-bold mb-6">{{ isEditing ? 'Edit Aircraft' : 'Add New Aircraft' }}</h2>
        
        <form @submit.prevent="handleSubmit">
          <div class="space-y-4">
            <!-- Airline -->
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Airline</label>
              <select 
                v-model="form.airline_id"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              >
                <option value="">Select Airline</option>
                <option v-for="airline in airlines" :key="airline.id" :value="airline.id">
                  {{ airline.name }}
                </option>
              </select>
            </div>

            <!-- Model -->
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Aircraft Model</label>
              <input 
                v-model="form.model"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Boeing 737-800"
                required
              />
            </div>

            <!-- Registration Number -->
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Registration Number</label>
              <input 
                v-model="form.registration_number"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase"
                placeholder="N12345"
                required
              />
            </div>

            <!-- Capacities -->
            <div class="grid grid-cols-3 gap-4">
              <div>
                <label class="block text-gray-700 font-semibold mb-2">Economy Seats</label>
                <input 
                  v-model.number="form.capacity_economy"
                  type="number"
                  min="0"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required
                />
              </div>
              <div>
                <label class="block text-gray-700 font-semibold mb-2">Business Seats</label>
                <input 
                  v-model.number="form.capacity_business"
                  type="number"
                  min="0"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required
                />
              </div>
              <div>
                <label class="block text-gray-700 font-semibold mb-2">First Class Seats</label>
                <input 
                  v-model.number="form.capacity_first"
                  type="number"
                  min="0"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required
                />
              </div>
            </div>

            <!-- Total Capacity Display -->
            <div class="bg-blue-50 p-4 rounded-lg">
              <p class="text-sm text-gray-700">
                Total Capacity: <strong>{{ getTotalFormCapacity() }}</strong> seats
              </p>
            </div>

            <!-- Status -->
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Status</label>
              <select 
                v-model="form.status"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              >
                <option value="active">Active</option>
                <option value="maintenance">Maintenance</option>
                <option value="retired">Retired</option>
              </select>
            </div>
          </div>

          <!-- Error Message -->
          <div v-if="error" class="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
            {{ error }}
          </div>

          <!-- Buttons -->
          <div class="flex gap-4 mt-6">
            <button 
              type="submit"
              :disabled="submitting"
              class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition disabled:bg-gray-400"
            >
              {{ submitting ? 'Saving...' : (isEditing ? 'Update' : 'Create') }}
            </button>
            <button 
              type="button"
              @click="closeModal"
              class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 rounded-lg font-semibold transition"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../api/axios'

const aircraftList = ref([])
const airlines = ref([])
const loading = ref(false)
const showModal = ref(false)
const isEditing = ref(false)
const currentAircraftId = ref(null)
const searchQuery = ref('')
const airlineFilter = ref('')
const statusFilter = ref('')
const error = ref(null)
const submitting = ref(false)

const form = ref({
  airline_id: '',
  model: '',
  registration_number: '',
  capacity_economy: 0,
  capacity_business: 0,
  capacity_first: 0,
  status: 'active'
})

onMounted(() => {
  fetchAirlines()
  fetchAircraft()
})

const fetchAirlines = async () => {
  try {
    const response = await axios.get('/airlines')
    airlines.value = response.data.data
  } catch (err) {
    console.error('Failed to fetch airlines:', err)
  }
}

const fetchAircraft = async () => {
  loading.value = true
  try {
    const response = await axios.get('/aircraft', {
      params: {
        search: searchQuery.value,
        airline_id: airlineFilter.value,
        status: statusFilter.value
      }
    })
    aircraftList.value = response.data.data
  } catch (err) {
    console.error('Failed to fetch aircraft:', err)
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  fetchAircraft()
}

const getTotalCapacity = (aircraft) => {
  return aircraft.capacity_economy + aircraft.capacity_business + aircraft.capacity_first
}

const getTotalFormCapacity = () => {
  return (form.value.capacity_economy || 0) + 
         (form.value.capacity_business || 0) + 
         (form.value.capacity_first || 0)
}

const openCreateModal = () => {
  isEditing.value = false
  currentAircraftId.value = null
  form.value = {
    airline_id: '',
    model: '',
    registration_number: '',
    capacity_economy: 0,
    capacity_business: 0,
    capacity_first: 0,
    status: 'active'
  }
  error.value = null
  showModal.value = true
}

const openEditModal = (aircraft) => {
  isEditing.value = true
  currentAircraftId.value = aircraft.id
  form.value = {
    airline_id: aircraft.airline_id,
    model: aircraft.model,
    registration_number: aircraft.registration_number,
    capacity_economy: aircraft.capacity_economy,
    capacity_business: aircraft.capacity_business,
    capacity_first: aircraft.capacity_first,
    status: aircraft.status
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
      await axios.put(`/admin/aircraft/${currentAircraftId.value}`, form.value)
    } else {
      await axios.post('/admin/aircraft', form.value)
    }
    closeModal()
    fetchAircraft()
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to save aircraft'
  } finally {
    submitting.value = false
  }
}

const deleteAircraft = async (id) => {
  if (!confirm('Are you sure you want to delete this aircraft?')) return
  
  try {
    await axios.delete(`/admin/aircraft/${id}`)
    fetchAircraft()
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to delete aircraft')
  }
}
</script>