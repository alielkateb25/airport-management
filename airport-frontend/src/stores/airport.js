import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from '../api/axios'

export const useAirportStore = defineStore('airport', () => {
  const airports = ref([])
  const currentAirport = ref(null)
  const loading = ref(false)
  const error = ref(null)
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    total: 0,
  })

  const fetchAirports = async (params = {}) => {
    loading.value = true
    error.value = null
    try {
      const response = await axios.get('/airports', { params })
      airports.value = response.data.data
      pagination.value = {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        total: response.data.total,
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch airports'
      throw err
    } finally {
      loading.value = false
    }
  }

  const createAirport = async (airportData) => {
    loading.value = true
    error.value = null
    try {
      const response = await axios.post('/admin/airports', airportData)
      airports.value.unshift(response.data.airport)
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create airport'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateAirport = async (id, airportData) => {
    loading.value = true
    error.value = null
    try {
      const response = await axios.put(`/admin/airports/${id}`, airportData)
      const index = airports.value.findIndex(a => a.id === id)
      if (index !== -1) {
        airports.value[index] = response.data.airport
      }
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update airport'
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteAirport = async (id) => {
    loading.value = true
    error.value = null
    try {
      await axios.delete(`/admin/airports/${id}`)
      airports.value = airports.value.filter(a => a.id !== id)
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete airport'
      throw err
    } finally {
      loading.value = false
    }
  }

  const getAirport = async (id) => {
    loading.value = true
    error.value = null
    try {
      const response = await axios.get(`/admin/airports/${id}`)
      currentAirport.value = response.data
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch airport'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    airports,
    currentAirport,
    loading,
    error,
    pagination,
    fetchAirports,
    createAirport,
    updateAirport,
    deleteAirport,
    getAirport,
  }
})