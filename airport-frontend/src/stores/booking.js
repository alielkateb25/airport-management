import { defineStore } from 'pinia'
import axios from '../api/axios'

export const useBookingStore = defineStore('booking', {
  state: () => ({
    bookings: [],
    currentBooking: null,
    loading: false,
    error: null,
    pagination: {
      current_page: 1,
      last_page: 1,
      total: 0,
    },
  }),

  actions: {
    async fetchBookings(params = {}) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/agent/bookings', { params })
        this.bookings = response.data.data
        this.pagination = {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          total: response.data.total,
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch bookings'
        throw error
      } finally {
        this.loading = false
      }
    },

    async createBooking(bookingData) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.post('/agent/bookings', bookingData)
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to create booking'
        throw error
      } finally {
        this.loading = false
      }
    },

    async getBooking(id) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get(`/agent/bookings/${id}`)
        this.currentBooking = response.data
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch booking'
        throw error
      } finally {
        this.loading = false
      }
    },

    async cancelBooking(id) {
      this.loading = true
      this.error = null
      
      try {
        await axios.delete(`/agent/bookings/${id}`)
        const index = this.bookings.findIndex(b => b.id === id)
        if (index !== -1) {
          this.bookings[index].status = 'cancelled'
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to cancel booking'
        throw error
      } finally {
        this.loading = false
      }
    },
  },
})