import { ref } from 'vue'
import { defineStore } from 'pinia'
import axios from '../api/axios'

export const useUserStore = defineStore('user', () => {
  const users = ref([])
  const currentUser = ref(null)
  const loading = ref(false)
  const error = ref(null)
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    total: 0,
  })

  const fetchUsers = async (params = {}) => {
    loading.value = true
    error.value = null
    try {
      const response = await axios.get('/admin/users', { params })
      users.value = response.data.data || []
      if (response.data.meta) {
        pagination.value = {
          current_page: response.data.meta.current_page,
          last_page: response.data.meta.last_page,
          total: response.data.meta.total,
        }
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch users'
      users.value = []
    } finally {
      loading.value = false
    }
  }

  const createUser = async (userData) => {
    loading.value = true
    error.value = null
    try {
      await axios.post('/admin/users', userData)
      await fetchUsers()
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create user'
    } finally {
      loading.value = false
    }
  }

  const updateUser = async (id, userData) => {
    loading.value = true
    error.value = null
    try {
      await axios.put(`/admin/users/${id}`, userData)
      await fetchUsers()
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update user'
    } finally {
      loading.value = false
    }
  }

  const deleteUser = async (id) => {
    loading.value = true
    error.value = null
    try {
      await axios.delete(`/admin/users/${id}`)
      await fetchUsers()
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete user'
    } finally {
      loading.value = false
    }
  }

  const getUser = async (id) => {
    loading.value = true
    error.value = null
    try {
      const response = await axios.get(`/admin/users/${id}`)
      currentUser.value = response.data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch user'
      currentUser.value = null
    } finally {
      loading.value = false
    }
  }

  return {
    users,
    currentUser,
    loading,
    error,
    pagination,
    fetchUsers,
    createUser,
    updateUser,
    deleteUser,
    getUser,
  }
})
