# nada-zirari-CarRentalApi
  Car Rental API

A robust Laravel-based REST API for managing car rentals, payments, and user authentication.

## Features

- User Authentication (Register/Login/Logout)
- Car Management (CRUD operations)
- Rental Management
- Payment Processing with Stripe Integration
- API Documentation with Swagger

## Tech Stack

- Laravel
- MySQL
- Stripe Payment Gateway
- Laravel Sanctum for Authentication
- L5-Swagger for API Documentation

## Installation

1. Clone the repository:

git clone https://github.com/Youcode-Classe-E-2024-2025/nada-zirari-CarRentalApi.git


cd nada-zirari-CarRentalApi

2. Configure your database in .env:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password


3. Configure Stripe keys in .env:
```
STRIPE_KEY=your_stripe_public_key
STRIPE_SECRET=your_stripe_secret_key
```

4. Run migrations:
```bash
php artisan migrate
```

## API Endpoints

### Authentication
- POST /api/register - Register new user
- POST /api/login - User login
- POST /api/logout - User logout

### Cars
- GET /api/cars - List all cars
- POST /api/cars - Create new car
- GET /api/cars/{id} - Get car details
- PUT /api/cars/{id} - Update car
- DELETE /api/cars/{id} - Delete car

### Payments
- POST /api/rentals/{rental}/payment-intent - Create payment intent
- POST /api/rentals/{rental}/payments - Process payment
- GET /api/payments - List all payments
- GET /api/payments/{id} - Get payment details


