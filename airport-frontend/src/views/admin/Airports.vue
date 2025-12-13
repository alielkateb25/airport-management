<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Manage Airports</h1>
      <button 
        @click="openCreateModal"
        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition"
      >
        + Add Airport
      </button>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
      <div class="flex gap-4">
        <input 
          v-model="searchQuery"
          @input="handleSearch"
          type="text"
          placeholder="Search airports..."
          class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <select 
          v-model="statusFilter"
          @change="handleSearch"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="airportStore.loading" class="text-center py-12">
      <div class="text-gray-600">Loading airports...</div>
    </div>

    <!-- Airports Table -->
    <div v-else class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">City</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Country</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="airport in airportStore.airports" :key="airport.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
              {{ airport.code }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ airport.name }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ airport.city }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ airport.country }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                :class="airport.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                class="px-2 py-1 text-xs font-semibold rounded-full"
              >
                {{ airport.status }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
              <button 
                @click="openEditModal(airport)"
                class="text-blue-600 hover:text-blue-900"
              >
                Edit
              </button>
              <button 
                @click="deleteAirport(airport.id)"
                class="text-red-600 hover:text-red-900"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Empty State -->
      <div v-if="airportStore.airports.length === 0" class="text-center py-12">
        <p class="text-gray-600">No airports found.</p>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="airportStore.pagination.last_page > 1" class="mt-6 flex justify-center">
      <div class="flex gap-2">
        <button 
          v-for="page in airportStore.pagination.last_page" 
          :key="page"
          @click="changePage(page)"
          :class="page === airportStore.pagination.current_page ? 'bg-blue-600 text-white' : 'bg-white text-gray-700'"
          class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50"
        >
          {{ page }}
        </button>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
        <h2 class="text-2xl font-bold mb-6">{{ isEditing ? 'Edit Airport' : 'Add New Airport' }}</h2>
        
        <form @submit.prevent="handleSubmit">
          <div class="space-y-4">
            <!-- Code -->
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Airport Code (IATA)</label>
              <input 
                v-model="form.code"
                type="text"
                maxlength="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase"
                placeholder="JFK"
                required
              />
            </div>

            <!-- Name -->
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Airport Name</label>
              <input 
                v-model="form.name"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="John F. Kennedy International Airport"
                required
              />
            </div>

            <!-- City -->
            <div>
              <label class="block text-gray-700 font-semibold mb-2">City</label>
              <input 
                v-model="form.city"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="New York"
                required
              />
            </div>

            <!-- Country -->
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Country</label>
              <input 
                v-model="form.country"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="USA"
                required
              />
            </div>

            <!-- Timezone -->
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Timezone</label>
              <input 
                v-model="form.timezone"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="America/New_York"
              />
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
                <option value="inactive">Inactive</option>
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
import { useAirportStore } from '../../stores/airport'

const airportStore = useAirportStore()

const showModal = ref(false)
const isEditing = ref(false)
const currentAirportId = ref(null)
const searchQuery = ref('')
const statusFilter = ref('')
const error = ref(null)
const submitting = ref(false)

const form = ref({
  code: '',
  name: '',
  city: '',
  country: '',
  timezone: 'UTC',
  status: 'active'
})

onMounted(() => {
  fetchAirports()
})

const fetchAirports = async () => {
  try {
    await airportStore.fetchAirports({
      search: searchQuery.value,
      status: statusFilter.value
    })
  } catch (err) {
    console.error('Failed to fetch airports:', err)
  }
}

const handleSearch = () => {
  fetchAirports()
}

const changePage = (page) => {
  airportStore.fetchAirports({
    page,
    search: searchQuery.value,
    status: statusFilter.value
  })
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
      await airportStore.updateAirport(currentAirportId.value, form.value)
    } else {
      await airportStore.createAirport(form.value)
    }
    closeModal()
    fetchAirports()
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to save airport'
  } finally {
    submitting.value = false
  }
}

const deleteAirport = async (id) => {
  if (!confirm('Are you sure you want to delete this airport?')) return
  
  try {
    await airportStore.deleteAirport(id)
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to delete airport')
  }
}
</script>