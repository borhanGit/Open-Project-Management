# Contributing to OpenProject Laravel

Thank you for your interest in contributing to **OpenProject Laravel**! We are building a powerful, community-driven open-source project management platform.

---

## Code of Conduct

Please be respectful, collaborative, and constructive when interacting with other contributors and maintaining issues or discussions.

---

## Getting Started with Development

This repository uses **Laravel Sail** (Docker Compose) so you do not need PHP, MySQL, or Redis installed on your local host system.

### 1. Prerequisites
- Docker Engine 24+ & Docker Compose v2
- Git

### 2. Fork and Clone
```bash
git clone https://github.com/your-username/openproject.git
cd openproject
```

### 3. Environment Setup
```bash
cp .env.example .env
```

Ensure the ports in `.env` do not conflict with host services (default forwarded MySQL is `3307`, Redis is `6380`, App is `80`).

### 4. Boot Containers with Sail
```bash
./vendor/bin/sail up -d
```

### 5. Run Migrations & Seeders
```bash
./vendor/bin/sail artisan migrate --seed
```

### 6. Install & Compile Frontend Assets
```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

Visit [http://localhost](http://localhost) in your browser. Default seeded login credentials:
- **Email**: `admin@openproject.local`
- **Password**: `password`

---

## Development Workflow

1. **Create a Topic Branch**:
   ```bash
   git checkout -b feature/my-new-feature
   ```
2. **Code Standards**:
   - Adhere to PSR-12 and Laravel naming conventions.
   - Run Laravel Pint before submitting code:
     ```bash
     ./vendor/bin/sail pint
     ```
3. **Write Tests**:
   - Add feature or unit tests for new behavior in `tests/Feature` or `tests/Unit`.
   - Run tests:
     ```bash
     ./vendor/bin/sail test
     ```
4. **Documentation**:
   - If adding new features, database models, or API endpoints, update the corresponding file in `/docs/`.

---

## Submitting Pull Requests

1. Commit your changes with clear, descriptive commit messages.
2. Push your branch to your fork.
3. Open a Pull Request against the `main` branch with a thorough description of your changes, context, and screenshots/recordings if UI changes were made.
