<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Manage Users</h1>
      <button 
        @click="openCreateModal"
        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition"
      >
        + Add User
      </button>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input 
          v-model="searchQuery"
          @input="handleSearch"
          type="text"
          placeholder="Search users..."
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <select 
          v-model="roleFilter"
          @change="handleSearch"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">All Roles</option>
          <option value="admin">Admin</option>
          <option value="staff">Staff</option>
          <option value="agent">Agent</option>
        </select>
        <select 
          v-model="statusFilter"
          @change="handleSearch"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">All Status</option>
          <option value="1">Active</option>
          <option value="0">Inactive</option>
        </select>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="text-gray-600">Loading users...</div>
    </div>

    <!-- Users Table -->
    <div v-else class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
              {{ user.name }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ user.email }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                :class="{
                  'bg-red-100 text-red-800': user.role === 'admin',
                  'bg-green-100 text-green-800': user.role === 'staff',
                  'bg-purple-100 text-purple-800': user.role === 'agent'
                }"
                class="px-2 py-1 text-xs font-semibold rounded-full capitalize"
              >
                {{ user.role }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                class="px-2 py-1 text-xs font-semibold rounded-full"
              >
                {{ user.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
              <button 
                @click="openEditModal(user)"
                class="text-blue-600 hover:text-blue-900"
              >
                Edit
              </button>
              <button 
                @click="deleteUser(user.id)"
                class="text-red-600 hover:text-red-900"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Empty State -->
      <div v-if="users.length === 0" class="text-center py-12">
        <p class="text-gray-600">No users found.</p>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
        <h2 class="text-2xl font-bold mb-6">{{ isEditing ? 'Edit User' : 'Add New User' }}</h2>
        
        <form @submit.prevent="handleSubmit">
          <div class="space-y-4">
            <!-- Name -->
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Name</label>
              <input 
                v-model="form.name"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="John Doe"
                required
              />
            </div>

            <!-- Email -->
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Email</label>
              <input 
                v-model="form.email"
                type="email"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="user@example.com"
                required
              />
            </div>

            <!-- Password -->
            <div>
              <label class="block text-gray-700 font-semibold mb-2">
                Password {{ isEditing ? '(leave blank to keep current)' : '' }}
              </label>
              <input 
                v-model="form.password"
                type="password"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter password"
                :required="!isEditing"
              />
            </div>

            <!-- Password Confirmation -->
            <div v-if="form.password">
              <label class="block text-gray-700 font-semibold mb-2">Confirm Password</label>
              <input 
                v-model="form.password_confirmation"
                type="password"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Confirm password"
                :required="!!form.password"
              />
            </div>

            <!-- Role -->
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Role</label>
              <select 
                v-model="form.role"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              >
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
                <option value="agent">Agent</option>
              </select>
            </div>

            <!-- Status -->
            <div>
              <label class="flex items-center">
                <input 
                  v-model="form.is_active"
                  type="checkbox"
                  class="mr-2"
                />
                <span class="text-gray-700 font-semibold">Active</span>
              </label>
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

const users = ref([])
const loading = ref(false)
const showModal = ref(false)
const isEditing = ref(false)
const currentUserId = ref(null)
const searchQuery = ref('')
const roleFilter = ref('')
const statusFilter = ref('')
const error = ref(null)
const submitting = ref(false)

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'agent',
  is_active: true
})

onMounted(() => {
  fetchUsers()
})

const fetchUsers = async () => {
  loading.value = true
  try {
    const response = await axios.get('/admin/users', {
      params: {
        search: searchQuery.value,
        role: roleFilter.value,
        is_active: statusFilter.value
      }
    })
    users.value = response.data.data
  } catch (err) {
    console.error('Failed to fetch users:', err)
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  fetchUsers()
}

const openCreateModal = () => {
  isEditing.value = false
  currentUserId.value = null
  form.value = {
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'agent',
    is_active: true
  }
  error.value = null
  showModal.value = true
}

const openEditModal = (user) => {
  isEditing.value = true
  currentUserId.value = user.id
  form.value = {
    name: user.name,
    email: user.email,
    password: '',
    password_confirmation: '',
    role: user.role,
    is_active: user.is_active
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
      await axios.put(`/admin/users/${currentUserId.value}`, form.value)
    } else {
      await axios.post('/admin/users', form.value)
    }
    closeModal()
    fetchUsers()
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to save user'
  } finally {
    submitting.value = false
  }
}

const deleteUser = async (id) => {
  if (!confirm('Are you sure you want to delete this user?')) return
  
  try {
    await axios.delete(`/admin/users/${id}`)
    fetchUsers()
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to delete user')
  }
}
</script>