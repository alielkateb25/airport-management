import { ref } from 'vue'
import { defineStore } from 'pinia'
import axios from '../api/axios'

export const useAircraftStore = defineStore('aircraft', () => {
  const aircraftList = ref([])
  const currentAircraft = ref(null)
  const loading = ref(false)
  const error = ref(null)
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    total: 0,
  })

  const fetchAircraft = async (params = {}) => {
    loading.value = true
    error.value = null
    try {
      const response = await axios.get('/aircraft', { params })
      aircraftList.value = response.data.data || []
      // If paginated, set pagination here
      if (response.data.meta) {
        pagination.value = {
          current_page: response.data.meta.current_page,
          last_page: response.data.meta.last_page,
          total: response.data.meta.total,
        }
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch aircraft'
      aircraftList.value = []
    } finally {
      loading.value = false
    }
  }

  const createAircraft = async (aircraftData) => {
    loading.value = true
    error.value = null
    try {
      await axios.post('/admin/aircraft', aircraftData)
      await fetchAircraft()
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create aircraft'
    } finally {
      loading.value = false
    }
  }

  const updateAircraft = async (id, aircraftData) => {
    loading.value = true
    error.value = null
    try {
      await axios.put(`/admin/aircraft/${id}`, aircraftData)
      await fetchAircraft()
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update aircraft'
    } finally {
      loading.value = false
    }
  }

  const deleteAircraft = async (id) => {
    loading.value = true
    error.value = null
    try {
      await axios.delete(`/admin/aircraft/${id}`)
      await fetchAircraft()
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete aircraft'
    } finally {
      loading.value = false
    }
  }

  const getAircraft = async (id) => {
    loading.value = true
    error.value = null
    try {
      const response = await axios.get(`/aircraft/${id}`)
      currentAircraft.value = response.data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch aircraft'
      currentAircraft.value = null
    } finally {
      loading.value = false
    }
  }

  return {
    aircraftList,
    currentAircraft,
    loading,
    error,
    pagination,
    fetchAircraft,
    createAircraft,
    updateAircraft,
    deleteAircraft,
    getAircraft,
  }
})
