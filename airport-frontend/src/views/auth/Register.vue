<template>
  <div class="login-container">
    <div class="login-card">
      <div class="login-image">
          <img src="/airport.jpg" alt="Airport" style="display: block; border-radius: 0;" />
        <!-- <div class="image-overlay">
          <h1>✈️ Join Our Team</h1>
          <p>Create an account to get started</p>
        </div> -->
      </div>

      <div class="login-form">
        <div class="form-header">
          <h2>Create Account</h2>
          <p>Fill in your details to register</p>
        </div>

        <form @submit.prevent="handleRegister">
          <div class="form-group">
            <label class="form-label">Full Name</label>
            <input 
              v-model="form.name"
              type="text"
              class="form-control"
              placeholder="Enter your full name"
              required
            />
          </div>

          <div class="form-group">
            <label class="form-label">Email Address</label>
            <input 
              v-model="form.email"
              type="email"
              class="form-control"
              placeholder="Enter your email"
              required
            />
          </div>

          <div class="form-group">
            <label class="form-label">Password</label>
            <input 
              v-model="form.password"
              type="password"
              class="form-control"
              placeholder="Create a password"
              required
            />
          </div>

          <div class="form-group">
            <label class="form-label">Confirm Password</label>
            <input 
              v-model="form.password_confirmation"
              type="password"
              class="form-control"
              placeholder="Confirm your password"
              required
            />
          </div>

          <div class="form-group">
            <label class="form-label">Role</label>
            <select v-model="form.role" class="form-control" required>
              <option value="agent">Agent</option>
              <option value="staff">Staff</option>
              <option value="admin">Admin</option>
            </select>
          </div>

          <div v-if="error" class="error-message">
            {{ error }}
          </div>

          <div v-if="success" class="success-message">
            {{ success }}
          </div>

          <button 
            type="submit"
            :disabled="loading"
            class="btn-login"
          >
            <span v-if="loading" class="spinner-small"></span>
            <span v-else>Create Account</span>
          </button>
        </form>

        <div class="register-link">
          Already have an account? <router-link to="/login">Sign in here</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from '../../api/axios'

const router = useRouter()

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'agent'
})

const error = ref('')
const success = ref('')
const loading = ref(false)

const handleRegister = async () => {
  error.value = ''
  success.value = ''
  loading.value = true
  
  try {
    await axios.post('/register', form.value)
    success.value = 'Account created successfully! Redirecting to login...'
    
    setTimeout(() => {
      router.push('/login')
    }, 2000)
  } catch (err) {
    error.value = err.response?.data?.message || 'Registration failed'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Reuse same styles as Login.vue */
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
}

.login-card {
  display: grid;
  grid-template-columns: 1fr 1fr;
  max-width: 1500px;
  width: 100%;
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.login-image {
  position: relative;
  min-height: 600px;
}

.login-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.image-overlay {
  position: absolute;
  inset: 0;
  /*background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);*/
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 40px;
  color: white;
  text-align: center;
}

.image-overlay h1 {
  /*background: linear-gradient(135deg, rgba(102, 126, 234, 0.45) 0%, rgba(118, 75, 162, 0.45) 100%);*/
  margin-bottom: 16px;
  font-weight: 700;
}

.login-form {
  padding: 60px 50px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.form-header {
  margin-bottom: 30px;
}

.form-header h2 {
  font-size: 28px;
  margin-bottom: 8px;
  color: #2c3e50;
}

.btn-login {
  width: 100%;
  padding: 14px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 10px;
}

.btn-login:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

.error-message {
  background: #fee;
  color: #c33;
  padding: 12px;
  border-radius: 8px;
  margin-bottom: 16px;
  font-size: 14px;
}

.success-message {
  background: #d4edda;
  color: #155724;
  padding: 12px;
  border-radius: 8px;
  margin-bottom: 16px;
  font-size: 14px;
}

.register-link {
  text-align: center;
  margin-top: 30px;
  font-size: 14px;
  color: #7f8c8d;
}

.register-link a {
  color: #667eea;
  font-weight: 600;
  text-decoration: none;
}

@media (max-width: 768px) {
  .login-card {
    grid-template-columns: 1fr;
  }

  .login-image {
    display: none;
  }
}
</style>