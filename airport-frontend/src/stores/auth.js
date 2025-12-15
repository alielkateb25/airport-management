import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from '../api/axios'
import router from '../router'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(JSON.parse(localStorage.getItem('user')) || null)
  const token = ref(localStorage.getItem('token') || null)
  const loading = ref(false)
  const error = ref(null)

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin')
  const isStaff = computed(() => user.value?.role === 'staff')
  const isAgent = computed(() => user.value?.role === 'agent')
  const userRole = computed(() => user.value?.role)
  const userName = computed(() => user.value?.name)

  const login = async (credentials) => {
    loading.value = true
    error.value = null
    try {
      const response = await axios.post('/login', credentials)
      token.value = response.data.token
      user.value = response.data.user
      localStorage.setItem('token', token.value)
      localStorage.setItem('user', JSON.stringify(user.value))
      router.push(response.data.redirect)
      console.log('Logged in user:', user.value)
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Login failed'
      throw err
    } finally {
      loading.value = false
    }
  }

  const logout = async () => {
    try {
      await axios.post('/logout')
    } catch (err) {
      console.error('Logout error:', err)
    } finally {
      user.value = null
      token.value = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      router.push('/login')
      console.log('Logged out')
    }
  }

  const fetchUser = async () => {
    try {
      const response = await axios.get('/me')
      user.value = response.data.user
      localStorage.setItem('user', JSON.stringify(user.value))
      console.log('Fetched user:', user.value)
    } catch (err) {
      logout()
    }
  }

  const initializeAuth = () => {
    if (token.value && user.value) {
      fetchUser()
    }
  }

  return {
    user,
    token,
    loading,
    error,
    isAuthenticated,
    isAdmin,
    isStaff,
    isAgent,
    userRole,
    userName,
    login,
    logout,
    fetchUser,
    initializeAuth,
  }
})