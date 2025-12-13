import { defineStore } from 'pinia'
import axios from '../api/axios'

export const useFlightStore = defineStore('flight', {
  state: () => ({
    flights: [],
    currentFlight: null,
    loading: false,
    error: null,
    pagination: {
      current_page: 1,
      last_page: 1,
      total: 0,
    },
  }),

  actions: {
    async fetchFlights(params = {}) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/flights', { params })
        this.flights = response.data.data
        this.pagination = {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          total: response.data.total,
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch flights'
        throw error
      } finally {
        this.loading = false
      }
    },

    async searchFlights(searchParams) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/agent/flights/search', { params: searchParams })
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to search flights'
        throw error
      } finally {
        this.loading = false
      }
    },

    async createFlight(flightData) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.post('/staff/flights', flightData)
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to create flight'
        throw error
      } finally {
        this.loading = false
      }
    },

    async updateFlight(id, flightData) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.put(`/staff/flights/${id}`, flightData)
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update flight'
        throw error
      } finally {
        this.loading = false
      }
    },

    async deleteFlight(id) {
      this.loading = true
      this.error = null
      
      try {
        await axios.delete(`/staff/flights/${id}`)
        this.flights = this.flights.filter(f => f.id !== id)
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to delete flight'
        throw error
      } finally {
        this.loading = false
      }
    },
  },
})