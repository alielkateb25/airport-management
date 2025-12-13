import { defineStore } from 'pinia'
import axios from '../api/axios'

export const useAirportStore = defineStore('airport', {
  state: () => ({
    airports: [],
    currentAirport: null,
    loading: false,
    error: null,
    pagination: {
      current_page: 1,
      last_page: 1,
      total: 0,
    },
  }),

  actions: {
    async fetchAirports(params = {}) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/airports', { params })
        this.airports = response.data.data
        this.pagination = {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          total: response.data.total,
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch airports'
        throw error
      } finally {
        this.loading = false
      }
    },

    async createAirport(airportData) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.post('/admin/airports', airportData)
        this.airports.unshift(response.data.airport)
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to create airport'
        throw error
      } finally {
        this.loading = false
      }
    },

    async updateAirport(id, airportData) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.put(`/admin/airports/${id}`, airportData)
        const index = this.airports.findIndex(a => a.id === id)
        if (index !== -1) {
          this.airports[index] = response.data.airport
        }
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update airport'
        throw error
      } finally {
        this.loading = false
      }
    },

    async deleteAirport(id) {
      this.loading = true
      this.error = null
      
      try {
        await axios.delete(`/admin/airports/${id}`)
        this.airports = this.airports.filter(a => a.id !== id)
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to delete airport'
        throw error
      } finally {
        this.loading = false
      }
    },

    async getAirport(id) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get(`/admin/airports/${id}`)
        this.currentAirport = response.data
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch airport'
        throw error
      } finally {
        this.loading = false
      }
    },
  },
})