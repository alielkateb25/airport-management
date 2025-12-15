<template>
  <div>
    <div class="page-header">
      <h2 class="page-title">User Management</h2>
      <button @click="openCreateModal" class="btn btn-primary">
        <span>+</span>
        Add User
      </button>
    </div>

    <!-- Search and Filters -->
    <div class="card filter-card">
      <div class="filter-grid-3">
        <input 
          v-model="searchQuery"
          @input="handleSearch"
          type="text"
          placeholder="Search users..."
          class="form-control"
        />
        <select 
          v-model="roleFilter"
          @change="handleSearch"
          class="form-control"
        >
          <option value="">All Roles</option>
          <option value="admin">Admin</option>
          <option value="staff">Staff</option>
          <option value="agent">Agent</option>
        </select>
        <select 
          v-model="statusFilter"
          @change="handleSearch"
          class="form-control"
        >
          <option value="">All Status</option>
          <option value="true">Active</option>
          <option value="false">Inactive</option>
        </select>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="globalDataStore.loading.users" class="loading-state">
      <div class="spinner"></div>
      <p>Loading users...</p>
    </div>

    <!-- Users Table -->
    <div v-else class="table-container">
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in filteredUsers" :key="user.id">
            <td>
              <div class="user-info">
                <div class="user-avatar">{{ getUserInitials(user.name) }}</div>
                <strong>{{ user.name }}</strong>
              </div>
            </td>
            <td>{{ user.email }}</td>
            <td>
              <span class="badge" :class="getRoleBadge(user.role)">
                {{ user.role }}
              </span>
            </td>
            <td>
              <span class="badge" :class="user.is_active ? 'badge-success' : 'badge-danger'">
                {{ user.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td>
              <small class="text-muted">{{ formatDate(user.created_at) }}</small>
            </td>
            <td>
              <div class="action-buttons">
                <button @click="openEditModal(user)" class="btn-icon btn-edit" title="Edit">
                  ✏️
                </button>
                <button @click="deleteUserHandler(user.id)" class="btn-icon btn-delete" title="Delete">
                  🗑️
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Empty State -->
      <div v-if="filteredUsers.length === 0" class="empty-state">
        <div class="empty-icon">👤</div>
        <p>No users found</p>
        <button @click="openCreateModal" class="btn btn-primary">Add First User</button>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content modal-large">
        <div class="modal-header">
          <h2>{{ isEditing ? 'Edit User' : 'Add New User' }}</h2>
          <button @click="closeModal" class="close-btn">✕</button>
        </div>
        
        <form @submit.prevent="handleSubmit">
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Name *</label>
              <input 
                v-model="form.name"
                type="text"
                class="form-control"
                placeholder="Full name"
                required
              />
            </div>

            <div class="form-group">
              <label class="form-label">Email *</label>
              <input 
                v-model="form.email"
                type="email"
                class="form-control"
                placeholder="email@example.com"
                required
              />
            </div>

            <div class="form-group">
              <label class="form-label">Role *</label>
              <select v-model="form.role" class="form-control" required>
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
                <option value="agent">Agent</option>
              </select>
              <small class="help-text">
                <strong>Admin:</strong> Full system access | 
                <strong>Staff:</strong> Operations management | 
                <strong>Agent:</strong> Booking & customer service
              </small>
            </div>

            <div class="form-group">
              <label class="form-label">Status *</label>
              <select v-model="form.is_active" class="form-control" required>
                <option :value="true">Active</option>
                <option :value="false">Inactive</option>
              </select>
            </div>

            <div class="form-group" v-if="!isEditing">
              <label class="form-label">Password *</label>
              <input 
                v-model="form.password"
                type="password"
                class="form-control"
                placeholder="Min. 8 characters"
                :required="!isEditing"
                minlength="8"
              />
            </div>

            <div class="form-group" v-if="!isEditing">
              <label class="form-label">Confirm Password *</label>
              <input 
                v-model="form.password_confirmation"
                type="password"
                class="form-control"
                placeholder="Re-enter password"
                :required="!isEditing"
              />
            </div>

            <div class="form-group full-width" v-if="isEditing">
              <div class="password-reset-notice">
                <span>💡</span>
                <span>Leave password fields empty to keep current password</span>
              </div>
            </div>

            <div class="form-group" v-if="isEditing">
              <label class="form-label">New Password</label>
              <input 
                v-model="form.password"
                type="password"
                class="form-control"
                placeholder="Leave empty to keep current"
                minlength="8"
              />
            </div>

            <div class="form-group" v-if="isEditing">
              <label class="form-label">Confirm New Password</label>
              <input 
                v-model="form.password_confirmation"
                type="password"
                class="form-control"
                placeholder="Re-enter new password"
              />
            </div>
          </div>

          <div v-if="error" class="alert alert-error">{{ error }}</div>

          <div class="modal-footer">
            <button type="button" @click="closeModal" class="btn btn-secondary">Cancel</button>
            <button type="submit" :disabled="submitting" class="btn btn-primary">
              <span v-if="submitting" class="spinner-small"></span>
              <span v-else>{{ isEditing ? 'Update User' : 'Create User' }}</span>
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
const currentUserId = ref(null)
const searchQuery = ref('')
const roleFilter = ref('')
const statusFilter = ref('')
const submitting = ref(false)
const error = ref(null)

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'agent',
  is_active: true
})

const filteredUsers = computed(() => {
  let users = [...globalDataStore.users]
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    users = users.filter(u => 
      u.name.toLowerCase().includes(query) ||
      u.email.toLowerCase().includes(query)
    )
  }
  
  if (roleFilter.value) {
    users = users.filter(u => u.role === roleFilter.value)
  }
  
  if (statusFilter.value) {
    const isActive = statusFilter.value === 'true'
    users = users.filter(u => u.is_active === isActive)
  }
  
  return users
})

const handleSearch = () => {
  console.log('Filtered users:', filteredUsers.value.length)
}

const getUserInitials = (name) => {
  const parts = name.split(' ')
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return name.substring(0, 2).toUpperCase()
}

const getRoleBadge = (role) => {
  const badges = {
    'admin': 'badge-admin',
    'staff': 'badge-staff',
    'agent': 'badge-agent'
  }
  return badges[role] || 'badge-info'
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString()
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
  if (form.value.password && form.value.password !== form.value.password_confirmation) {
    error.value = 'Passwords do not match'
    return
  }

  submitting.value = true
  error.value = null
  
  try {
    const submitData = { ...form.value }
    
    if (isEditing.value && !submitData.password) {
      delete submitData.password
      delete submitData.password_confirmation
    }
    
    if (isEditing.value) {
      const response = await axios.put(`/admin/users/${currentUserId.value}`, submitData)
      globalDataStore.updateItem('users', currentUserId.value, response.data.data || submitData)
      console.log('User updated:', response.data)
    } else {
      const response = await axios.post('/admin/users', submitData)
      globalDataStore.addItem('users', response.data.data || { ...submitData, id: Date.now() })
      console.log('User created:', response.data)
    }
    closeModal()
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to save user'
    console.error('Submit error:', err)
  } finally {
    submitting.value = false
  }
}

const deleteUserHandler = async (id) => {
  if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) return
  
  try {
    await axios.delete(`/admin/users/${id}`)
    globalDataStore.removeItem('users', id)
    console.log('User deleted:', id)
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to delete user')
    console.error('Delete error:', err)
  }
}
</script>

<style scoped>
.filter-grid-3 {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 16px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 14px;
}

.text-muted {
  color: #6c757d;
  font-size: 13px;
}

.badge-admin {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  color: white;
}

.badge-staff {
  background: #d1ecf1;
  color: #0c5460;
}

.badge-agent {
  background: #d4edda;
  color: #155724;
}

.help-text {
  display: block;
  margin-top: 6px;
  color: #6c757d;
  font-size: 12px;
  line-height: 1.4;
}

.password-reset-notice {
  background: #e7f3ff;
  padding: 12px 16px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 8px;
  color: #0066cc;
  font-size: 14px;
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
  .filter-grid-3 {
    grid-template-columns: 1fr;
  }
  
  .form-grid {
    grid-template-columns: 1fr;
  }
}
</style>