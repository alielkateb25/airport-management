import { ref } from 'vue'
import { defineStore } from 'pinia'
import axios from '../api/axios'

export const useAirlineStore = defineStore('airline', () => {
  const airlines = ref([])
  const currentAirline = ref(null)
  const loading = ref(false)
  const error = ref(null)
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    total: 0,
  })

  const fetchAirlines = async (params = {}) => {
    loading.value = true
    error.value = null
    try {
      const response = await axios.get('/airlines', { params })
      airlines.value = response.data.data || []
      // If paginated, set pagination here
      if (response.data.meta) {
        pagination.value = {
          current_page: response.data.meta.current_page,
          last_page: response.data.meta.last_page,
          total: response.data.meta.total,
        }
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch airlines'
      airlines.value = []
    } finally {
      loading.value = false
    }
  }

  const createAirline = async (airlineData) => {
    loading.value = true
    error.value = null
    try {
      await axios.post('/admin/airlines', airlineData)
      await fetchAirlines()
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create airline'
    } finally {
      loading.value = false
    }
  }

  const updateAirline = async (id, airlineData) => {
    loading.value = true
    error.value = null
    try {
      await axios.put(`/admin/airlines/${id}`, airlineData)
      await fetchAirlines()
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update airline'
    } finally {
      loading.value = false
    }
  }

  const deleteAirline = async (id) => {
    loading.value = true
    error.value = null
    try {
      await axios.delete(`/admin/airlines/${id}`)
      await fetchAirlines()
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete airline'
    } finally {
      loading.value = false
    }
  }

  const getAirline = async (id) => {
    loading.value = true
    error.value = null
    try {
      const response = await axios.get(`/airlines/${id}`)
      currentAirline.value = response.data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch airline'
      currentAirline.value = null
    } finally {
      loading.value = false
    }
  }

  return {
    airlines,
    currentAirline,
    loading,
    error,
    pagination,
    fetchAirlines,
    createAirline,
    updateAirline,
    deleteAirline,
    getAirline,
  }
})
