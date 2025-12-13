import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/auth/Login.vue'),
    meta: { requiresGuest: true }
  },
  
  // Admin Routes
  {
    path: '/admin',
    component: () => import('../layouts/AdminLayout.vue'),
    meta: { requiresAuth: true, roles: ['admin'] },
    children: [
      {
        path: 'dashboard',
        name: 'AdminDashboard',
        component: () => import('../views/admin/Dashboard.vue')
      },
      {
        path: 'airports',
        name: 'AdminAirports',
        component: () => import('../views/admin/Airports.vue')
      },/* 
      {
        path: 'airlines',
        name: 'AdminAirlines',
        component: () => import('../views/admin/Airlines.vue')
      },
      {
        path: 'aircraft',
        name: 'AdminAircraft',
        component: () => import('../views/admin/Aircraft.vue')
      },
      {
        path: 'flights',
        name: 'AdminFlights',
        component: () => import('../views/admin/Flights.vue')
      },
      {
        path: 'bookings',
        name: 'AdminBookings',
        component: () => import('../views/admin/Bookings.vue')
      },
      {
        path: 'users',
        name: 'AdminUsers',
        component: () => import('../views/admin/Users.vue')
      }, */
    ]
  },

  // Staff Routes
  /* {
    path: '/staff',
    component: () => import('../layouts/StaffLayout.vue'),
    meta: { requiresAuth: true, roles: ['admin', 'staff'] },
    children: [
      {
        path: 'dashboard',
        name: 'StaffDashboard',
        component: () => import('../views/staff/Dashboard.vue')
      },
      {
        path: 'flights',
        name: 'StaffFlights',
        component: () => import('../views/staff/Flights.vue')
      },
      {
        path: 'bookings',
        name: 'StaffBookings',
        component: () => import('../views/staff/Bookings.vue')
      },
      {
        path: 'passengers',
        name: 'StaffPassengers',
        component: () => import('../views/staff/Passengers.vue')
      },
    ]
  },

  // Agent Routes
  {
    path: '/agent',
    component: () => import('../layouts/AgentLayout.vue'),
    meta: { requiresAuth: true, roles: ['admin', 'staff', 'agent'] },
    children: [
      {
        path: 'dashboard',
        name: 'AgentDashboard',
        component: () => import('../views/agent/Dashboard.vue')
      },
      {
        path: 'search-flights',
        name: 'AgentSearchFlights',
        component: () => import('../views/agent/SearchFlights.vue')
      },
      {
        path: 'create-booking',
        name: 'AgentCreateBooking',
        component: () => import('../views/agent/CreateBooking.vue')
      },
      {
        path: 'my-bookings',
        name: 'AgentMyBookings',
        component: () => import('../views/agent/MyBookings.vue')
      },
      {
        path: 'passengers',
        name: 'AgentPassengers',
        component: () => import('../views/agent/Passengers.vue')
      },
    ]
  }, */

  // Redirect root to login
  {
    path: '/',
    redirect: '/login'
  },

  // Catch all 404
  {
    path: '/:pathMatch(.*)*',
    redirect: '/login'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  // Redirect authenticated users away from login
  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    const redirectPath = getDefaultRoute(authStore.userRole)
    return next(redirectPath)
  }
  
  // Check authentication
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return next('/login')
  }
  
  // Check role authorization
  if (to.meta.roles && authStore.user) {
    if (!to.meta.roles.includes(authStore.userRole)) {
      const redirectPath = getDefaultRoute(authStore.userRole)
      return next(redirectPath)
    }
  }
  
  next()
})

function getDefaultRoute(role) {
  switch(role) {
    case 'admin': return '/admin/dashboard'
    case 'staff': return '/staff/dashboard'
    case 'agent': return '/agent/dashboard'
    default: return '/login'
  }
}

export default router