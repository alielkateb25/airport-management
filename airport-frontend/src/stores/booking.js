import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from '../api/axios'

export const useBookingStore = defineStore('booking', () => {
  const bookings = ref([])
  const currentBooking = ref(null)
  const loading = ref(false)
  const error = ref(null)
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    total: 0,
  })

  const fetchBookings = async (params = {}) => {
    loading.value = true
    error.value = null
    try {
      const response = await axios.get('/agent/bookings', { params })
      bookings.value = response.data.data
      pagination.value = {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        total: response.data.total,
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch bookings'
      throw err
    } finally {
      loading.value = false
    }
  }

  const createBooking = async (bookingData) => {
    loading.value = true
    error.value = null
    try {
      const response = await axios.post('/agent/bookings', bookingData)
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create booking'
      throw err
    } finally {
      loading.value = false
    }
  }

  const getBooking = async (id) => {
    loading.value = true
    error.value = null
    try {
      const response = await axios.get(`/agent/bookings/${id}`)
      currentBooking.value = response.data
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch booking'
      throw err
    } finally {
      loading.value = false
    }
  }

  const cancelBooking = async (id) => {
    loading.value = true
    error.value = null
    try {
      await axios.delete(`/agent/bookings/${id}`)
      const index = bookings.value.findIndex(b => b.id === id)
      if (index !== -1) {
        bookings.value[index].status = 'cancelled'
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to cancel booking'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    bookings,
    currentBooking,
    loading,
    error,
    pagination,
    fetchBookings,
    createBooking,
    getBooking,
    cancelBooking,
  }
})