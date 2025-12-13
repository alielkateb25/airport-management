<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600 px-4">
    <div class="max-w-md w-full bg-white rounded-lg shadow-2xl p-8">
      <!-- Logo/Title -->
      <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">✈️</h1>
        <h2 class="text-2xl font-bold text-gray-800">Airport Management</h2>
        <p class="text-gray-600 mt-2">Sign in to your account</p>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="handleLogin">
        <!-- Email -->
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-2">Email</label>
          <input 
            v-model="form.email"
            type="email"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Enter your email"
            required
          />
        </div>

        <!-- Password -->
        <div class="mb-6">
          <label class="block text-gray-700 font-semibold mb-2">Password</label>
          <input 
            v-model="form.password"
            type="password"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Enter your password"
            required
          />
        </div>

        <!-- Error Message -->
        <div v-if="authStore.error" class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
          {{ authStore.error }}
        </div>

        <!-- Submit Button -->
        <button 
          type="submit"
          :disabled="authStore.loading"
          class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed"
        >
          {{ authStore.loading ? 'Signing in...' : 'Sign In' }}
        </button>
      </form>

      <!-- Demo Accounts -->
      <div class="mt-8 pt-6 border-t border-gray-200">
        <p class="text-sm font-semibold text-gray-700 mb-3">Demo Accounts:</p>
        <div class="space-y-2 text-xs text-gray-600">
          <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
            <span><strong>Admin:</strong> admin@airport.com</span>
            <button 
              @click="fillDemo('admin')"
              class="text-blue-600 hover:text-blue-800 font-semibold"
            >
              Use
            </button>
          </div>
          <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
            <span><strong>Staff:</strong> staff@airport.com</span>
            <button 
              @click="fillDemo('staff')"
              class="text-blue-600 hover:text-blue-800 font-semibold"
            >
              Use
            </button>
          </div>
          <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
            <span><strong>Agent:</strong> agent@airport.com</span>
            <button 
              @click="fillDemo('agent')"
              class="text-blue-600 hover:text-blue-800 font-semibold"
            >
              Use
            </button>
          </div>
          <p class="text-center text-gray-500 mt-2">Password: <strong>password</strong></p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()

const form = ref({
  email: '',
  password: ''
})

const handleLogin = async () => {
  try {
    await authStore.login(form.value)
  } catch (error) {
    console.error('Login failed:', error)
  }
}

const fillDemo = (role) => {
  form.value.email = `${role}@airport.com`
  form.value.password = 'password'
}
</script>