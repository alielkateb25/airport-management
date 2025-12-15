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
- [Contributing](#contributing)
- [License](#license)

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

### 4. Create Admin User

```bash
php artisan tinker

# In tinker console
User::create([
    'name' => 'Admin User',
    'email' => 'admin@skyops.com',
    'password' => bcrypt('password'),
    'role' => 'admin',
    'is_active' => true
]);
```

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

```
Users (1) ──> (N) Bookings
Passengers (1) ──> (N) Bookings
Flights (1) ──> (N) Bookings
Bookings (1) ──> (1) Payments

Airlines (1) ──> (N) Aircraft
Airlines (1) ──> (N) Flights
Aircraft (1) ──> (N) Flights

Airports (1) ──> (N) Flights (Origin)
Airports (1) ──> (N) Flights (Destination)
```

---

## 👥 User Roles

### 🔴 Admin
**Full system access**
- Manage airports, airlines, aircraft
- Manage users and permissions
- View all reports and analytics
- System configuration

### 🟡 Staff
**Operations management**
- Manage flights and schedules
- View bookings and passengers
- Handle flight operations
- Limited administrative access

### 🟢 Agent
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

### Dashboard
> Real-time statistics and system overview

### Flight Management
> Create, schedule, and manage flights

### Booking System
> Process bookings with seat selection

### Admin Panel
> Comprehensive system administration

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

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Coding Standards
- Follow PSR-12 for PHP code
- Use ESLint configuration for JavaScript
- Write meaningful commit messages
- Add comments for complex logic
- Update documentation as needed

---

## 🐛 Bug Reports

If you discover a bug, please create an issue with:
- Clear description of the issue
- Steps to reproduce
- Expected vs actual behavior
- Screenshots (if applicable)
- Environment details

---

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Author

**Your Name**
- GitHub: [@yourusername](https://github.com/yourusername)
- Email: your.email@example.com

---

## 🙏 Acknowledgments

- Laravel Framework Team
- Vue.js Core Team
- Open Source Community
- All Contributors

---

## 📞 Support

For support and questions:
- 📧 Email: support@skyops.com
- 💬 Discord: [Join our server](https://discord.gg/skyops)
- 📖 Documentation: [docs.skyops.com](https://docs.skyops.com)

---

<div align="center">
  <p>Made with ❤️ by the SkyOps Team</p>
  <p>⭐ Star this repository if you find it helpful!</p>
</div>
```