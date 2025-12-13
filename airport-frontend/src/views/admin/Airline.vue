<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Manage Airlines</h1>
      <button 
        @click="openCreateModal"
        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition"
      >
        + Add Airline
      </button>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
      <div class="flex gap-4">
        <input 
          v-model="searchQuery"
          @input="handleSearch"
          type="text"
          placeholder="Search airlines..."
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
    <div v-if="loading" class="text-center py-12">
      <div class="text-gray-600">Loading airlines...</div>
    </div>

    <!-- Airlines Table -->
    <div v-else class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Country</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="airline in airlines" :key="airline.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
              {{ airline.code }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ airline.name }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ airline.country }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ airline.contact_email || 'N/A' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                :class="airline.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                class="px-2 py-1 text-xs font-semibold rounded-full"
              >
                {{ airline.status }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
              <button 
                @click="openEditModal(airline)"
                class="text-blue-600 hover:text-blue-900"
              >
                Edit
              </button>
              <button 
                @click="deleteAirline(airline.id)"
                class="text-red-600 hover:text-red-900"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Empty State -->
      <div v-if="airlines.length === 0" class="text-center py-12">
        <p class="text-gray-600">No airlines found.</p>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
        <h2 class="text-2xl font-bold mb-6">{{ isEditing ? 'Edit Airline' : 'Add New Airline' }}</h2>
        
        <form @submit.prevent="handleSubmit">
          <div class="space-y-4">
            <!-- Code -->
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Airline Code (IATA)</label>
              <input 
                v-model="form.code"
                type="text"
                maxlength="2"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase"
                placeholder="AA"
                required
              />
            </div>

            <!-- Name -->
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Airline Name</label>
              <input 
                v-model="form.name"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="American Airlines"
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

            <!-- Contact Email -->
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Contact Email</label>
              <input 
                v-model="form.contact_email"
                type="email"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="contact@airline.com"
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
import axios from '../../api/axios'

const airlines = ref([])
const loading = ref(false)
const showModal = ref(false)
const isEditing = ref(false)
const currentAirlineId = ref(null)
const searchQuery = ref('')
const statusFilter = ref('')
const error = ref(null)
const submitting = ref(false)

const form = ref({
  code: '',
  name: '',
  country: '',
  contact_email: '',
  status: 'active'
})

onMounted(() => {
  fetchAirlines()
})

const fetchAirlines = async () => {
  loading.value = true
  try {
    const response = await axios.get('/airlines', {
      params: {
        search: searchQuery.value,
        status: statusFilter.value
      }
    })
    airlines.value = response.data.data
  } catch (err) {
    console.error('Failed to fetch airlines:', err)
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  fetchAirlines()
}

const openCreateModal = () => {
  isEditing.value = false
  currentAirlineId.value = null
  form.value = {
    code: '',
    name: '',
    country: '',
    contact_email: '',
    status: 'active'
  }
  error.value = null
  showModal.value = true
}

const openEditModal = (airline) => {
  isEditing.value = true
  currentAirlineId.value = airline.id
  form.value = { ...airline }
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
      await axios.put(`/admin/airlines/${currentAirlineId.value}`, form.value)
    } else {
      await axios.post('/admin/airlines', form.value)
    }
    closeModal()
    fetchAirlines()
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to save airline'
  } finally {
    submitting.value = false
  }
}

const deleteAirline = async (id) => {
  if (!confirm('Are you sure you want to delete this airline?')) return
  
  try {
    await axios.delete(`/admin/airlines/${id}`)
    fetchAirlines()
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to delete airline')
  }
}
</script>