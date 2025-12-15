import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from '../api/axios'

export const useFlightStore = defineStore('flight', () => {
  const flights = ref([])
  const currentFlight = ref(null)
  const loading = ref(false)
  const error = ref(null)
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    total: 0,
  })

  const fetchFlights = async (params = {}) => {
    loading.value = true
    error.value = null
    try {
      const response = await axios.get('/flights', { params })
      flights.value = response.data.data
      pagination.value = {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        total: response.data.total,
      }
      console.log('Fetched flights:', flights.value)
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch flights'
      throw err
    } finally {
      loading.value = false
    }
  }

  const searchFlights = async (searchParams) => {
    loading.value = true
    error.value = null
    try {
      const response = await axios.get('/agent/flights/search', { params: searchParams })
      console.log('Searched flights:', response.data)
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to search flights'
      throw err
    } finally {
      loading.value = false
    }
  }

  const createFlight = async (flightData) => {
    loading.value = true
    error.value = null
    try {
      const response = await axios.post('/admin/flights', flightData)
      flights.value.unshift(response.data.flight)
      console.log('Created flight:', response.data.flight)
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create flight'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateFlight = async (id, flightData) => {
    loading.value = true
    error.value = null
    try {
      const response = await axios.put(`/admin/flights/${id}`, flightData)
      const index = flights.value.findIndex(f => f.id === id)
      if (index !== -1) {
        flights.value[index] = response.data.flight
      }
      console.log('Updated flight:', response.data.flight)
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update flight'
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteFlight = async (id) => {
    loading.value = true
    error.value = null
    try {
      await axios.delete(`/admin/flights/${id}`)
      flights.value = flights.value.filter(f => f.id !== id)
      console.log('Deleted flight with id:', id)
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete flight'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    flights,
    currentFlight,
    loading,
    error,
    pagination,
    fetchFlights,
    searchFlights,
    createFlight,
    updateFlight,
    deleteFlight,
  }
})