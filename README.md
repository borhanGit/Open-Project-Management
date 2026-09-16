# OpenProject Laravel

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

<p align="center">
  <strong>Modern, fast, open-source project management platform built on Laravel, Docker Sail, and MySQL.</strong>
</p>

<p align="center">
  <a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-blue.svg" alt="License"></a>
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-12.x-red.svg" alt="Laravel"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-8.4%2B-777bb4.svg" alt="PHP"></a>
  <a href="https://www.mysql.com"><img src="https://img.shields.io/badge/MySQL-8.4-00758f.svg" alt="MySQL"></a>
  <a href="https://laravel.com/docs/sail"><img src="https://img.shields.io/badge/Docker-Sail-2496ed.svg" alt="Docker Sail"></a>
</p>

---

## 🌟 Highlights & Features

- 🚀 **Projects & Portfolios**: Hierarchical projects and subprojects with public/private visibility and health summaries.
- 📋 **Work Packages (Tasks, Bugs, Features, Milestones)**: Rich issue tracking with customizable workflows, progress sliders, and estimations.
- 🗂️ **Interactive Drag & Drop Kanban Board**: Real-time column management powered by Alpine.js and SortableJS.
- ⏱️ **Time & Velocity Tracking**: Log hours directly against tasks with detailed work notes and progress comparison.
- 👥 **Role-Based Access Control**: Project-level roles (Administrator, Project Manager, Developer, Viewer).
- 💬 **Discussion Threads & Activity**: In-line task discussions, audit history, and collaboration logs.
- 📖 **Embedded Documentation Engine**: Repository `/docs/*.md` files rendered with live Markdown parser in the browser.
- 🐳 **Zero-Configuration Docker**: Powered by Laravel Sail with pre-configured MySQL, Redis, and Mailpit sandbox.

---

## 🚀 Quick Start with Docker Sail

Get the entire application and database running in under 2 minutes:

### 1. Clone & Setup Environment
```bash
git clone https://github.com/your-username/openproject.git
cd openproject
cp .env.example .env
```

### 2. Boot Containers
```bash
./vendor/bin/sail up -d
```

### 3. Migrate and Seed Demo Data
```bash
./vendor/bin/sail artisan migrate --seed
```

### 4. Build Frontend Assets
```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
# Or for live development:
./vendor/bin/sail npm run dev
```

### 5. Access the Application
- **Web App**: [http://localhost](http://localhost)
- **Database GUI (phpMyAdmin)**: [http://localhost:8080](http://localhost:8080) (User: `sail`, Password: `password`)
- **Email Sandbox (Mailpit)**: [http://localhost:8025](http://localhost:8025)
- **Database (MySQL)**: `127.0.0.1:3307` (User: `sail`, Password: `password`, DB: `openproject`)

---

## 📚 Maintainable Documentation

All documentation is stored directly in the repository in Markdown format:

| Document | Description |
| :--- | :--- |
| [`docs/index.md`](docs/index.md) | Introduction and project vision |
| [`docs/architecture.md`](docs/architecture.md) | Architecture, ERD diagrams, and domain design |
| [`docs/installation.md`](docs/installation.md) | Setup and deployment guide |
| [`docs/work-packages.md`](docs/work-packages.md) | Task, bug, milestone workflows |
| [`docs/api.md`](docs/api.md) | REST API endpoints and payload examples |

You can also browse these docs directly inside the running application at [http://localhost/docs](http://localhost/docs).

---

## 🛠️ Tech Stack

- **Framework**: [Laravel 12](https://laravel.com)
- **Containerization**: [Laravel Sail](https://laravel.com/docs/sail) (Docker Compose)
- **Database**: [MySQL 8.4](https://www.mysql.com)
- **Cache & Queues**: [Redis Alpine](https://redis.io)
- **Email Testing**: [Mailpit](https://mailpit.axllent.org)
- **Frontend**: Laravel Blade + [Tailwind CSS v4](https://tailwindcss.com) + [Alpine.js](https://alpinejs.dev) + [SortableJS](https://sortablejs.github.io/Sortable/)

---

## 🤝 Contributing

Contributions from the open-source community are welcome! Please read [`CONTRIBUTING.md`](CONTRIBUTING.md) for details on code style, testing, and pull request procedures.

---

## 📄 License

OpenProject Laravel is open-sourced software licensed under the [MIT license](LICENSE).
