<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Create New Booking</h1>

    <div class="bg-white rounded-lg shadow p-6">
      <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Step 1: Select Passenger -->
        <div>
          <h2 class="text-xl font-bold text-gray-800 mb-4">1. Select Passenger</h2>
          
          <div class="space-y-4">
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Existing Passenger</label>
              <select 
                v-model="form.passenger_id"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
              >
                <option value="">Select a passenger or create new</option>
                <!-- In real app, fetch passengers from API -->
              </select>
            </div>

            <div class="text-center text-gray-600">
              <p>OR</p>
            </div>

            <button 
              type="button"
              class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 rounded-lg font-semibold transition"
            >
              + Create New Passenger
            </button>
          </div>
        </div>

        <!-- Step 2: Select Flight -->
        <div>
          <h2 class="text-xl font-bold text-gray-800 mb-4">2. Select Flight</h2>
          
          <div>
            <label class="block text-gray-700 font-semibold mb-2">Flight</label>
            <select 
              v-model="form.flight_id"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
              required
            >
              <option value="">Select a flight</option>
              <!-- In real app, fetch available flights -->
            </select>
          </div>
        </div>

        <!-- Step 3: Booking Details -->
        <div>
          <h2 class="text-xl font-bold text-gray-800 mb-4">3. Booking Details</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Class</label>
              <select 
                v-model="form.class"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                required
              >
                <option value="economy">Economy</option>
                <option value="business">Business</option>
                <option value="first">First Class</option>
              </select>
            </div>

            <div>
              <label class="block text-gray-700 font-semibold mb-2">Seat Number (Optional)</label>
              <input 
                v-model="form.seat_number"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                placeholder="e.g., 12A"
              />
            </div>

            <div>
              <label class="block text-gray-700 font-semibold mb-2">Total Price</label>
              <input 
                v-model.number="form.total_price"
                type="number"
                step="0.01"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                required
              />
            </div>
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="p-3 bg-red-100 border border-red-400 text-red-700 rounded">
          {{ error }}
        </div>

        <!-- Submit Button -->
        <div class="flex gap-4">
          <button 
            type="submit"
            :disabled="submitting"
            class="flex-1 bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-lg font-semibold transition disabled:bg-gray-400"
          >
            {{ submitting ? 'Creating Booking...' : '✓ Create Booking' }}
          </button>
          <router-link 
            to="/agent/search-flights"
            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 py-3 rounded-lg font-semibold transition text-center"
          >
            Cancel
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>