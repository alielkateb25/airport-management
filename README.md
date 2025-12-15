# ✈️ SkyOps - Airport Management System

<div align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel" />
  <img src="https://img.shields.io/badge/Vue.js-35495E?style=for-the-badge&logo=vue.js&logoColor=4FC08D" alt="Vue.js" />
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
  <img src="https://img.shields.io/badge/Pinia-FFD859?style=for-the-badge&logo=vuedotjs&logoColor=white" alt="Pinia" />
</div>

<p align="center">
  <strong>A comprehensive airport management system built with Laravel and Vue.js</strong>
</p>

---

## 📋 Table of Contents

- [About](#about)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Database Structure](#database-structure)
- [User Roles](#user-roles)
- [API Endpoints](#api-endpoints)
- [Screenshots](#screenshots)


---

## 🎯 About

**SkyOps** is a modern, full-stack airport management system designed to streamline airport operations, flight management, booking processes, and passenger services. The system provides role-based access control for administrators, staff members, and booking agents.

### Key Highlights

- 🚀 **Real-time Data Management** - Instant updates across all modules
- 🎨 **Modern UI/UX** - Clean, intuitive interface with smooth animations
- 🔐 **Secure Authentication** - JWT-based authentication with role-based access
- 📊 **Comprehensive Dashboard** - Real-time statistics and insights
- 🌍 **Multi-Airport Support** - Manage multiple airports and airlines
- 💳 **Payment Integration** - Built-in payment processing system

---

## ✨ Features

### 🏢 Airport Management
- Create and manage multiple airports
- Track airport status (Active/Inactive)
- Store timezone and location information
- IATA code validation

### ✈️ Airline Management
- Register and manage airlines
- Track airline status and contact information
- IATA airline code system
- Country-based organization

### 🛩️ Aircraft Fleet Management
- Manage aircraft fleet by airline
- Track aircraft status (Active/Maintenance/Retired)
- Seat capacity configuration (Economy/Business/First Class)
- Registration number tracking

### 🛫 Flight Operations
- Create and schedule flights
- Assign aircraft and routes
- Real-time flight status updates
- Departure and arrival time management
- Dynamic pricing system

### 🎫 Booking System
- Passenger booking management
- Seat selection and class assignment
- Booking reference generation
- Status tracking (Pending/Confirmed/Cancelled)
- Multi-step booking process

### 👥 Passenger Management
- Passenger profile creation
- Passport and identification tracking
- Booking history
- Personal information management

### 💰 Payment Processing
- Multiple payment methods support
- Transaction tracking
- Payment status management
- Refund handling

### 👨‍💼 User Management
- Role-based access control (Admin/Staff/Agent)
- User activity tracking
- Account activation/deactivation
- Secure password management

---

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 10.x
- **Database**: MySQL 8.0
- **Authentication**: Laravel Sanctum (API Tokens)
- **API**: RESTful API Architecture

### Frontend
- **Framework**: Vue.js 3 (Composition API)
- **State Management**: Pinia
- **Routing**: Vue Router
- **HTTP Client**: Axios
- **Styling**: Custom CSS with Modern Design System

### Development Tools
- **Package Manager**: Composer (Backend), npm (Frontend)
- **Version Control**: Git
- **API Testing**: Postman

---

## 📦 Prerequisites

Before you begin, ensure you have the following installed:

- **PHP** >= 8.1
- **Composer** >= 2.5
- **Node.js** >= 18.x
- **npm** >= 9.x
- **MySQL** >= 8.0
- **Git**

---

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/skyops-airport-management.git
cd skyops-airport-management
```

### 2. Backend Setup (Laravel)

```bash
# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env file
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=skyops_db
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed

# Start Laravel development server
php artisan serve
```

The backend will be available at `http://localhost:8000`

### 3. Frontend Setup (Vue.js)

```bash
# Navigate to frontend directory
cd frontend

# Install dependencies
npm install

# Configure API endpoint in .env or config file
# Create .env file in frontend directory
VITE_API_URL=http://localhost:8000/api

# Start development server
npm run dev
```

The frontend will be available at `http://localhost:5173`

---

## 🗄️ Database Structure

### Core Tables

#### Users
- Manages system users (Admin, Staff, Agents)
- Role-based access control
- Account activation status

#### Airports
- Airport information and IATA codes
- Location and timezone data
- Operational status tracking

#### Airlines
- Airline registration and details
- IATA airline codes
- Contact information

#### Aircraft
- Fleet management by airline
- Capacity configuration
- Maintenance status tracking

#### Flights
- Flight scheduling and routes
- Aircraft assignment
- Status management
- Pricing information

#### Passengers
- Passenger profiles
- Passport and identification
- Contact information

#### Bookings
- Booking records and references
- Seat assignments
- Class selection
- Payment status

#### Payments
- Transaction records
- Payment method tracking
- Refund management

### Entity Relationships

<img width="760" height="777" alt="image" src="https://github.com/user-attachments/assets/2d938422-1110-443f-84f0-e9a36375f72a" />

---

## 👥 User Roles

### 🔴 Admin
**Full system access**
- Manage airports, airlines, aircraft
- Manage users and permissions
- View all reports and analytics
- System configuration

### 🟡 Staff (In Progress)
**Operations management**
- Manage flights and schedules
- View bookings and passengers
- Handle flight operations
- Limited administrative access

### 🟢 Agent (In Progress)
**Booking and customer service**
- Create and manage bookings
- Register passengers
- Process payments
- View flight information

---

## 🔌 API Endpoints

### Authentication
```
POST   /api/login              # User login
POST   /api/logout             # User logout
GET    /api/user               # Get authenticated user
```

### Airports
```
GET    /api/airports           # List all airports
POST   /api/admin/airports     # Create airport
PUT    /api/admin/airports/:id # Update airport
DELETE /api/admin/airports/:id # Delete airport
```

### Airlines
```
GET    /api/airlines           # List all airlines
POST   /api/admin/airlines     # Create airline
PUT    /api/admin/airlines/:id # Update airline
DELETE /api/admin/airlines/:id # Delete airline
```

### Aircraft
```
GET    /api/aircraft           # List all aircraft
POST   /api/admin/aircraft     # Create aircraft
PUT    /api/admin/aircraft/:id # Update aircraft
DELETE /api/admin/aircraft/:id # Delete aircraft
```

### Flights
```
GET    /api/flights            # List all flights
POST   /api/staff/flights      # Create flight
PUT    /api/staff/flights/:id  # Update flight
DELETE /api/staff/flights/:id  # Delete flight
```

### Bookings
```
GET    /api/bookings           # List all bookings
GET    /api/agent/bookings     # List agent bookings
POST   /api/agent/bookings     # Create booking
PUT    /api/admin/bookings/:id # Update booking
DELETE /api/admin/bookings/:id # Delete booking
```

### Passengers
```
GET    /api/passengers         # List all passengers
POST   /api/passengers         # Create passenger
PUT    /api/passengers/:id     # Update passenger
DELETE /api/passengers/:id     # Delete passenger
```

### Users
```
GET    /api/admin/users        # List all users
POST   /api/admin/users        # Create user
PUT    /api/admin/users/:id    # Update user
DELETE /api/admin/users/:id    # Delete user
```

---

## 📸 Screenshots

### Admin Dashboard
> Real-time statistics and system overview
<img width="1680" height="886" alt="image" src="https://github.com/user-attachments/assets/7a419efa-3726-44be-ac3d-f04c85ecf963" />

### Flight Management
> Create, schedule, and manage flights
<img width="1468" height="609" alt="image" src="https://github.com/user-attachments/assets/7cf54aea-dee0-4614-b156-7e8ba2192e1d" />

---

## 🏗️ Project Structure

```
skyops-airport-management/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   ├── Models/
│   └── Services/
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   ├── api.php
│   └── web.php
├── frontend/
│   ├── src/
│   │   ├── api/
│   │   ├── components/
│   │   ├── stores/
│   │   ├── views/
│   │   └── router/
│   ├── package.json
│   └── vite.config.js
└── README.md
```

---

## 🐛 Bug Reports

If you discover a bug, please create an issue with:
- Clear description of the issue
- Steps to reproduce
- Expected vs actual behavior
- Screenshots (if applicable)
- Environment details

---

## Future Steps

This project isn't 100% complete so there are steps to do :
- Build the Staff Dashboard
- Build the Agent Dashboard
- Make HomePage
- Test & see if everything works accordingly

---

## 👨‍💻 Author

**Your Name**
- GitHub: [@alielkateb25](https://github.com/alielkateb25)
- Email: elkatebali777@gmail.com@example.com

---

<div align="center">
  <p>Made with ❤️ by Me</p>
  <p>⭐ Star this repository if you find it helpful!</p>
</div>
