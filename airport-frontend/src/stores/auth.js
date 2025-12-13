import { defineStore } from 'pinia'
import axios from '../api/axios'
import router from '../router'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
    loading: false,
    error: null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === 'admin',
    isStaff: (state) => state.user?.role === 'staff',
    isAgent: (state) => state.user?.role === 'agent',
    userRole: (state) => state.user?.role,
    userName: (state) => state.user?.name,
  },

  actions: {
    async login(credentials) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.post('/login', credentials)
        
        this.token = response.data.token
        this.user = response.data.user
        
        // Save to localStorage
        localStorage.setItem('token', this.token)
        localStorage.setItem('user', JSON.stringify(this.user))
        
        // Redirect based on role
        router.push(response.data.redirect)
        
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Login failed'
        throw error
      } finally {
        this.loading = false
      }
    },

    async logout() {
      try {
        await axios.post('/logout')
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.user = null
        this.token = null
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        router.push('/login')
      }
    },

    async fetchUser() {
      try {
        const response = await axios.get('/me')
        this.user = response.data.user
        localStorage.setItem('user', JSON.stringify(this.user))
      } catch (error) {
        this.logout()
      }
    },

    initializeAuth() {
      if (this.token && this.user) {
        // Token and user exist, verify with backend
        this.fetchUser()
      }
    },
  },
})