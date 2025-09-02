# Promptly Backend (Laravel v1)

This is the **Laravel backend API** for **Promptly v1 – a simple AI generator**.  
It exposes REST API endpoints consumed by the [Promptly Next.js frontend](https://github.com/<your-username>/promptly-frontend-nextjs-v1).

---

## 🚀 Features
- REST API endpoints for AI prompt handling
- Secure environment variable management
- Database migrations and seeding
- Ready for local development and deployment
- Easily extendable for future versions (v2, v3...)

---

## 🛠️ Tech Stack
- [Laravel 11](https://laravel.com/)
- PHP 8+
- MySQL / SQLite (configurable)
- Composer for dependency management

---

## ⚡ Getting Started

### Prerequisites
- PHP 8+
- Composer
- MySQL or SQLite

### Installation
```bash
# Clone the repository
git clone https://github.com/<your-username>/promptly-backend-laravel-v1
cd promptly-backend-laravel-v1

# Install dependencies
composer install

# Copy and configure environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate
