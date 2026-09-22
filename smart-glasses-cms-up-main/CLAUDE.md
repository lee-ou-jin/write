# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

AmuzCMS UP - Laravel-based CMS built with Laravel Nova, Jetstream, and Inertia.js. This system features a modular architecture with custom packages (amuz-packages) and themes (amuz-themes).

## Development Commands

### Setup & Installation
```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Run database seeders
php artisan db:seed
```

### Development Server
```bash
# Start Laravel development server
php artisan serve

# Start Vite development server (for frontend assets)
npm run dev

# Build frontend assets for production
npm run build
```

### Testing
```bash
# Run PHPUnit tests
php artisan test

# Run specific test file
php artisan test tests/Unit/ExampleTest.php

# Run tests with coverage
php artisan test --coverage
```

### Code Quality
```bash
# Laravel Pint (code formatting)
./vendor/bin/pint

# Fix code style issues
./vendor/bin/pint --repair
```

### Nova Components
```bash
# Build ChatCard component
npm run build-chat-card       # Development
npm run build-chat-card-prod  # Production

# Build LiveCard component
npm run build-live-card       # Development
npm run build-live-card-prod  # Production

# Build TaskLogModal component
npm run build-task-log-modal       # Development
npm run build-task-log-modal-prod  # Production
```

### Artisan Commands
```bash
# Create Nova resource with model and migration
php artisan amuz-cms:resource {ModelName} {packageName}

# Clear various caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Architecture Overview

### Core Structure
- **Laravel 10.x** with PHP 8.2+ requirement
- **Nova 4.x** for admin panel functionality
- **Jetstream** for authentication scaffolding
- **Inertia.js** with Vue 3 for SPA-like experience
- **Real-time features** using Laravel Echo with Pusher/Socket.io

### Key Directories

#### `/app`
- `Http/ApiControllers/` - API endpoints for glasses app integration (Agora video, tasks, messages)
- `Models/` - Eloquent models with relationships
- `Nova/Resources/` - Nova admin resources (overrides package resources with same uri-key)
- `Services/` - Business logic and external service integrations
- `WebSocketServer.php` - Custom WebSocket server implementation

#### `/nova-components`
- `ChatCard/` - Real-time chat interface component
- `LiveCard/` - Live streaming/video component
- `TaskLogModal/` - Task logging interface with image upload

#### `/amuz-packages`
- Custom packages following amuz-package type
- Currently includes `vimeo-field` package
- Packages auto-discovered via composer merge plugin

#### `/amuz-themes`
- Theme system managed by hexadog/laravel-themes-manager
- Multi-tenant theme support with isolated assets

### Database & Storage
- MySQL/MariaDB as primary database
- Redis for caching and session storage
- Spatie Media Library for file management
- Vimeo integration for video storage

### API Architecture
- RESTful API endpoints under `/api`
- Sanctum authentication for API security
- Key integrations:
  - Agora for real-time video/audio
  - Task management with image capture from smart glasses
  - Real-time messaging system
  - Document management

### Nova Customization
- Resources in `/app/Nova/Resources` override package resources
- Custom Nova cards for specialized functionality
- Multi-language support via badinansoft/nova-language-switch

### Package Management
- Composer merge plugin includes amuz-packages
- Custom installer paths for amuz-package and amuz-theme types
- Helper functions for package path resolution (see README)

### Real-time Features
- WebSocket server for live updates
- Laravel Echo integration
- Pusher/Socket.io broadcasting
- Event-driven architecture for task updates

### Localization
- Multi-language support throughout
- JSON language files in resources/lang
- Automatic language pack integration for packages