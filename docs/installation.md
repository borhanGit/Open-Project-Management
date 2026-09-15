# Installation & Deployment Guide

This guide covers setting up **OpenProject Laravel** using Docker Sail for local development and configuration for production.

---

## Local Development with Laravel Sail

### System Requirements
- Docker Engine 24+
- Docker Compose v2 (e.g. `docker compose version`)
- Git

### Quick Setup

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/your-username/openproject.git
   cd openproject
   ```

2. **Environment File**:
   ```bash
   cp .env.example .env
   ```

3. **Start Containers**:
   ```bash
   ./vendor/bin/sail up -d
   ```

4. **Initialize Database & Demo Seeds**:
   ```bash
   ./vendor/bin/sail artisan migrate --seed
   ```

5. **Compile Frontend Assets**:
   ```bash
   ./vendor/bin/sail npm install
   ./vendor/bin/sail npm run dev
   ```

6. **Open in Browser**:
   - Web App: [http://localhost](http://localhost)
   - Mailpit (Email Sandbox): [http://localhost:8025](http://localhost:8025)
   - MySQL Host Port: `3307` (Credentials: `sail` / `password`, DB: `openproject`)
   - Redis Host Port: `6380`

---

## Production Deployment Checklist

1. Configure `.env`:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - Set strong `APP_KEY` with `php artisan key:generate`
   - Configure managed MySQL 8.x and Redis instances
2. Optimize Laravel:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan event:cache
   ```
3. Run migrations:
   ```bash
   php artisan migrate --force
   ```
4. Build assets for production:
   ```bash
   npm run build
   ```
