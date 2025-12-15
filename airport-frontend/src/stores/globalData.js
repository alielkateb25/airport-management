import { defineStore } from 'pinia'
import axios from '../api/axios'

export const useGlobalDataStore = defineStore('globalData', {
  state: () => ({
    // Data
    airports: [],
    airlines: [],
    aircraft: [],
    flights: [],
    bookings: [],
    passengers: [],
    users: [],
    
    // Loading states
    loading: {
      airports: false,
      airlines: false,
      aircraft: false,
      flights: false,
      bookings: false,
      passengers: false,
      users: false,
      all: false
    },
    
    // Loaded flags (to prevent re-fetching)
    loaded: {
      airports: false,
      airlines: false,
      aircraft: false,
      flights: false,
      bookings: false,
      passengers: false,
      users: false
    },
    
    // Errors
    errors: {
      airports: null,
      airlines: null,
      aircraft: null,
      flights: null,
      bookings: null,
      passengers: null,
      users: null
    },
    
    // Last updated timestamps
    lastUpdated: {
      airports: null,
      airlines: null,
      aircraft: null,
      flights: null,
      bookings: null,
      passengers: null,
      users: null
    }
  }),

  getters: {
    // Check if all data is loaded
    isAllLoaded: (state) => {
      return Object.values(state.loaded).every(val => val === true)
    },
    
    // Check if any data is loading
    isAnyLoading: (state) => {
      return Object.values(state.loading).some(val => val === true)
    },
    
    // Get statistics
    stats: (state) => ({
      airports: state.airports.length,
      airlines: state.airlines.length,
      aircraft: state.aircraft.length,
      flights: state.flights.length,
      bookings: state.bookings.length,
      passengers: state.passengers.length,
      users: state.users.length,
      revenue: state.bookings.reduce((sum, booking) => {
        return sum + (parseFloat(booking.total_price) || 0)
      }, 0)
    }),
    
    // Get active airports
    activeAirports: (state) => {
      return state.airports.filter(airport => airport.status === 'active')
    },
    
    // Get active airlines
    activeAirlines: (state) => {
      return state.airlines.filter(airline => airline.status === 'active')
    },
    
    // Get active aircraft
    activeAircraft: (state) => {
      return state.aircraft.filter(aircraft => aircraft.status === 'active')
    },
    
    // Get scheduled flights
    scheduledFlights: (state) => {
      return state.flights.filter(flight => flight.status === 'scheduled')
    },
    
    // Get confirmed bookings
    confirmedBookings: (state) => {
      return state.bookings.filter(booking => booking.status === 'confirmed')
    },

    // Get pending bookings
    pendingBookings: (state) => {
      return state.bookings.filter(booking => booking.status === 'pending')
    },

    // Get cancelled bookings
    cancelledBookings: (state) => {
      return state.bookings.filter(booking => booking.status === 'cancelled')
    },

    // Get airport by ID
    getAirportById: (state) => (id) => {
      return state.airports.find(airport => airport.id === id)
    },

    // Get airline by ID
    getAirlineById: (state) => (id) => {
      return state.airlines.find(airline => airline.id === id)
    },

    // Get aircraft by ID
    getAircraftById: (state) => (id) => {
      return state.aircraft.find(aircraft => aircraft.id === id)
    },

    // Get flight by ID
    getFlightById: (state) => (id) => {
      return state.flights.find(flight => flight.id === id)
    },

    // Get booking by ID
    getBookingById: (state) => (id) => {
      return state.bookings.find(booking => booking.id === id)
    },

    // Get passenger by ID
    getPassengerById: (state) => (id) => {
      return state.passengers.find(passenger => passenger.id === id)
    }
  },

  actions: {
    // Fetch all data at once
    async fetchAllData(force = false) {
      // If already loaded and not forcing, skip
      if (this.isAllLoaded && !force) {
        console.log('Data already loaded, skipping fetch')
        return
      }

      this.loading.all = true
      console.log('Fetching all data...')

      try {
        await Promise.all([
          this.fetchAirports(force),
          this.fetchAirlines(force),
          this.fetchAircraft(force),
          this.fetchFlights(force),
          this.fetchBookings(force),
          this.fetchPassengers(force),
          this.fetchUsers(force)
        ])
        
        console.log('All data fetched successfully')
      } catch (error) {
        console.error('Error fetching all data:', error)
      } finally {
        this.loading.all = false
      }
    },

    // Fetch airports
    async fetchAirports(force = false) {
      if (this.loaded.airports && !force) return

      this.loading.airports = true
      this.errors.airports = null

      try {
        const response = await axios.get('/airports')
        this.airports = response.data.data || []
        this.loaded.airports = true
        this.lastUpdated.airports = new Date()
        console.log('Airports loaded:', this.airports.length)
      } catch (error) {
        this.errors.airports = error.response?.data?.message || 'Failed to fetch airports'
        console.error('Error fetching airports:', error)
        this.airports = []
      } finally {
        this.loading.airports = false
      }
    },

    // Fetch airlines
    async fetchAirlines(force = false) {
      if (this.loaded.airlines && !force) return

      this.loading.airlines = true
      this.errors.airlines = null

      try {
        const response = await axios.get('/airlines')
        this.airlines = response.data.data || []
        this.loaded.airlines = true
        this.lastUpdated.airlines = new Date()
        console.log('Airlines loaded:', this.airlines.length)
      } catch (error) {
        this.errors.airlines = error.response?.data?.message || 'Failed to fetch airlines'
        console.error('Error fetching airlines:', error)
        this.airlines = []
      } finally {
        this.loading.airlines = false
      }
    },

    // Fetch aircraft
    async fetchAircraft(force = false) {
      if (this.loaded.aircraft && !force) return

      this.loading.aircraft = true
      this.errors.aircraft = null

      try {
        const response = await axios.get('/aircraft')
        this.aircraft = response.data.data || []
        this.loaded.aircraft = true
        this.lastUpdated.aircraft = new Date()
        console.log('Aircraft loaded:', this.aircraft.length)
      } catch (error) {
        this.errors.aircraft = error.response?.data?.message || 'Failed to fetch aircraft'
        console.error('Error fetching aircraft:', error)
        this.aircraft = []
      } finally {
        this.loading.aircraft = false
      }
    },

    // Fetch flights
    async fetchFlights(force = false) {
      if (this.loaded.flights && !force) return

      this.loading.flights = true
      this.errors.flights = null

      try {
        const response = await axios.get('/flights')
        this.flights = response.data.data || []
        this.loaded.flights = true
        this.lastUpdated.flights = new Date()
        console.log('Flights loaded:', this.flights.length)
      } catch (error) {
        this.errors.flights = error.response?.data?.message || 'Failed to fetch flights'
        console.error('Error fetching flights:', error)
        this.flights = []
      } finally {
        this.loading.flights = false
      }
    },

    // Fetch bookings
    async fetchBookings(force = false) {
      if (this.loaded.bookings && !force) return

      this.loading.bookings = true
      this.errors.bookings = null

      try {
        // Try agent endpoint first, fallback to general endpoint
        let response
        try {
          response = await axios.get('/agent/bookings')
        } catch (err) {
          response = await axios.get('/bookings')
        }
        
        this.bookings = response.data.data || []
        this.loaded.bookings = true
        this.lastUpdated.bookings = new Date()
        console.log('Bookings loaded:', this.bookings.length)
      } catch (error) {
        this.errors.bookings = error.response?.data?.message || 'Failed to fetch bookings'
        console.error('Error fetching bookings:', error)
        this.bookings = []
      } finally {
        this.loading.bookings = false
      }
    },

    // Fetch passengers
    async fetchPassengers(force = false) {
      if (this.loaded.passengers && !force) return

      this.loading.passengers = true
      this.errors.passengers = null

      try {
        const response = await axios.get('/agent/passengers')
        this.passengers = response.data.data || []
        this.loaded.passengers = true
        this.lastUpdated.passengers = new Date()
        console.log('Passengers loaded:', this.passengers.length)
      } catch (error) {
        this.errors.passengers = error.response?.data?.message || 'Failed to fetch passengers'
        console.error('Error fetching passengers:', error)
        this.passengers = []
      } finally {
        this.loading.passengers = false
      }
    },

    // Fetch users
    async fetchUsers(force = false) {
      if (this.loaded.users && !force) return

      this.loading.users = true
      this.errors.users = null

      try {
        const response = await axios.get('/admin/users')
        this.users = response.data.data || []
        this.loaded.users = true
        this.lastUpdated.users = new Date()
        console.log('Users loaded:', this.users.length)
      } catch (error) {
        this.errors.users = error.response?.data?.message || 'Failed to fetch users'
        console.error('Error fetching users:', error)
        this.users = []
      } finally {
        this.loading.users = false
      }
    },

    // Add new item to a collection
    addItem(collection, item) {
      if (this[collection]) {
        this[collection].push(item)
        console.log(`Added item to ${collection}:`, item)
      }
    },

    // Update item in a collection
    updateItem(collection, id, updatedItem) {
      if (this[collection]) {
        const index = this[collection].findIndex(item => item.id === id)
        if (index !== -1) {
          // Merge with existing item to preserve relationships
          this[collection][index] = { ...this[collection][index], ...updatedItem }
          console.log(`Updated item in ${collection}:`, updatedItem)
        }
      }
    },

    // Remove item from a collection
    removeItem(collection, id) {
      if (this[collection]) {
        this[collection] = this[collection].filter(item => item.id !== id)
        console.log(`Removed item from ${collection}:`, id)
      }
    },

    // Cancel booking (special method)
    async cancelBookingStatus(id) {
      const booking = this.bookings.find(b => b.id === id)
      if (booking) {
        booking.status = 'cancelled'
        console.log(`Booking ${id} status set to cancelled`)
      }
    },

    // Clear all data
    clearAllData() {
      this.airports = []
      this.airlines = []
      this.aircraft = []
      this.flights = []
      this.bookings = []
      this.passengers = []
      this.users = []
      
      Object.keys(this.loaded).forEach(key => {
        this.loaded[key] = false
      })
      
      console.log('All data cleared')
    }
  }
})