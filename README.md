# Promptly Backend (Laravel v1)

**Promptly** is a simple AI chat and text-generation application.

This repository contains the **Laravel backend API** for Promptly v1. It handles authentication, conversations, messages, and AI integration, and exposes REST endpoints consumed by the Next.js frontend.

## ✨ Features

* 🔐 User authentication
* 💭 Conversation and message management
* 🤖 AI response generation
* 🗂️ Conversation history per user
* 🔗 REST API consumed by the Next.js frontend
* 🧱 Database migrations and seeders
* ⚠️ Consistent JSON responses with proper HTTP status codes (404 for missing resources, 500 for server errors)
* 🔒 Secure environment variable management

## 🛠️ Tech Stack

* **Laravel 11**
* **PHP 8.2+**
* **MySQL / SQLite** (configurable)
* **Composer**

### Related Services

Promptly consists of multiple services:

* **Frontend:** Next.js
* **Backend API:** Laravel
* **Transcription API:** Python + FastAPI + Faster-Whisper

## 📁 Related Repositories

| Repository                                                                     | Description                                                                          |
| ------------------------------------------------------------------------------ | ------------------------------------------------------------------------------------ |
| [Promptly Frontend](https://github.com/sajid13673/promptly-frontend-nextjs-v1)       | Next.js frontend for the Promptly application                                        |
| [Promptly Backend](https://github.com/sajid13673/promptly-backend-laravel-v1)        | Laravel API responsible for authentication, conversations, AI integration, and data  |
| [Promptly Whisper API](https://github.com/sajid13673/promptly-whishper-api-v1)       | FastAPI service responsible for converting recorded voice/audio into text            |

> The voice transcription service is called directly by the frontend, so it is not required to run this backend. It is only needed to test voice input in the complete application.

## ⚡ Getting Started

### Prerequisites

Make sure you have the following installed:

* [PHP](https://www.php.net/) 8.2+
* [Composer](https://getcomposer.org/)
* MySQL or SQLite
* An API key for your AI provider

### Installation

Clone the repository:

```bash
git clone https://github.com/sajid13673/promptly-backend-laravel-v1.git

cd promptly-backend-laravel-v1
```

Install dependencies:

```bash
composer install
```

Create the environment file:

```bash
cp .env.example .env
```

For Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Configure the required environment variables in `.env`, then run the migrations:

```bash
php artisan migrate
```

### Run the Development Server

```bash
php artisan serve
```

The API will be available at:

```text
http://localhost:8000
```

## 🔐 Environment Variables

Environment-specific configuration is stored in `.env`.

Do not commit `.env` or any file containing sensitive credentials.

Example:

```env
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:3000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=promptly
DB_USERNAME=root
DB_PASSWORD=

# AI provider
AI_API_KEY=
```

> Variable names for the AI provider and authentication may differ. Use the ones defined in `.env.example`.

## 🔗 API Overview

Responses follow a consistent JSON shape:

```json
{
  "status": true,
  "data": {}
}
```

Errors return `status: false` with a `message` and the matching HTTP status code.

| Method | Endpoint               | Description                              |
| ------ | ---------------------- | ---------------------------------------- |
| GET    | `/api/conversations`      | List the user's conversations         |
| GET    | `/api/conversations/{id}` | Get a conversation with its messages  |
| DELETE | `/api/conversations/{id}` | Delete a conversation                 |

> This table lists the main conversation endpoints. Authentication and message/AI endpoints are omitted; add them here.

## 📂 Project Structure

```text
app/
├── Http/
│   └── Controllers/
├── Models/
└── ...
database/
├── migrations/
└── seeders/
routes/
└── api.php
```

The exact structure may evolve as new features are added.

## 🚀 Production

Before deploying, make sure:

* Production environment variables are configured and `APP_DEBUG=false`.
* The database is created and migrations have been run (`php artisan migrate --force`).
* CORS allows the deployed frontend origin.
* HTTPS is enabled.
* Config and routes are cached for performance:

```bash
php artisan config:cache
php artisan route:cache
```

## 📌 Project Status

Promptly v1 is an ongoing personal project focused on building an AI chat application while exploring modern web technologies and API integration.

## 📄 License

This project is for personal/learning purposes.