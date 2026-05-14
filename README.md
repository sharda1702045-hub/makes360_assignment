# MailFlow - Bulk Email Management System

MailFlow is a high-performance, enterprise-grade Bulk Email SaaS Portal designed to handle large-scale email marketing campaigns with precision. It provides a robust set of tools for audience management, template creation, campaign tracking, and real-time delivery monitoring.

## Project Purpose
The primary goal of MailFlow is to simplify the complexities of bulk email delivery by providing a scalable architecture that integrates seamlessly with providers like Amazon SES. It ensures high deliverability through efficient queue management and provides deep insights via real-time analytics.

---

# Tech Stack

MailFlow is built on the latest modern web technologies to ensure speed, security, and scalability.

*   **Framework:** Laravel 13.9.0
*   **Language:** PHP 8.3+
*   **Database:** MySQL / SQLite
*   **Caching & Queue:** Redis (via Predis)
*   **Frontend Stack:** Tailwind CSS 4.0, Vite 8.0, Blade Templates
*   **Dependencies:**
    *   `maatwebsite/excel` for high-performance contact imports.
    *   `laravel/sanctum` for secure API authentication.
    *   `concurrently` for unified development workflow.

---

# Requirements

Ensure you have the following installed on your development machine:

*   **PHP:** >= 8.3
*   **Composer:** >= 2.x
*   **MySQL:** >= 8.0 (or SQLite)
*   **Node.js:** >= 20.x
*   **npm:** >= 10.x
*   **Redis:** Optional but recommended for queue/caching
*   **Git:** Latest version

---

# Project Setup Instructions

Follow these steps to get your development environment up and running.

## Step 1: Clone Repository
```bash
git clone <repository-url>
cd makes360_assignment
```

## Step 2: Install Dependencies
```bash
composer install
npm install
```

## Step 3: Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

## Step 4: Configure Database
Open your `.env` file and update the following variables to match your local environment:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mailflow_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

## Step 5: Run Migration
```bash
php artisan migrate
```

## Step 6: Seed Database
```bash
php artisan db:seed
```

## Step 7: Storage Link
```bash
php artisan storage:link
```

## Step 8: Build Frontend
```bash
npm run build
```

## Step 9: Start Development Server
```bash
php artisan serve
```

---

# Queue Setup

MailFlow relies heavily on background jobs for processing imports and sending emails.

To start the queue worker:
```bash
php artisan queue:work
```

### Redis Configuration
If you are using Redis for your queue, ensure your `.env` is configured:
```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

---

# Scheduler Setup

The task scheduler handles recurring tasks like campaign statistics updates and log cleanup.

To run the scheduler locally:
```bash
php artisan schedule:work
```

### Linux Cron Job
On production, add the following entry to your server's crontab:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

---

# Environment Variables

Key variables used in MailFlow:

*   `APP_URL`: The base URL of your application.
*   `APP_ENV`: Application environment (`local`, `production`).
*   `APP_DEBUG`: Set to `true` for development, `false` for production.
*   `DB_*`: Database connection details.
*   `MAIL_*`: SMTP/Driver settings for email delivery.
*   `REDIS_*`: Redis connection parameters.
*   `QUEUE_CONNECTION`: Defines how jobs are processed (`sync`, `database`, `redis`).

---

# Folder Structure

MailFlow follows a modular and clean folder structure:

*   **`app/`**: Core application logic.
    *   `Services/`: Business logic layer.
    *   `Actions/`: Single-responsibility action classes.
    *   `DTOs/`: Data Transfer Objects for type-safe data handling.
    *   `Jobs/`: Background tasks for email delivery and imports.
*   **`routes/`**: All web and API route definitions.
*   **`resources/`**: Frontend assets (Blade, CSS, JS).
*   **`database/`**: Migrations, seeders, and factories.
*   **`storage/`**: Application logs, file uploads, and cached templates.
*   **`public/`**: Entry point for the web server and compiled assets.

---

# Coding Standards & Best Practices

We adhere to industry-standard practices to ensure code maintainability and quality:

*   **Service Layer Architecture:** Keeps controllers thin by moving complex business logic into dedicated Service classes.
*   **Repository Pattern:** Decouples the application from the data access layer (Eloquent), allowing for easier testing and swapping of data sources.
*   **SOLID Principles:** Core design principles for building understandable, flexible, and maintainable software.
*   **DRY (Don't Repeat Yourself):** Reusing logic through traits, helpers, and base classes.
*   **Form Request Validation:** Centralized validation logic for cleaner controller methods.
*   **API Resource Classes:** Transforming models into JSON structures consistently.
*   **Database Transactions:** Ensuring data integrity during multi-step write operations.
*   **Eager Loading:** Preventing N+1 query issues to optimize performance.
*   **Clean Code Standards:** Following PSR-12 and using `laravel/pint` for automated formatting.

---

# Security Best Practices

*   **CSRF Protection:** Built-in protection for all state-changing requests.
*   **XSS Prevention:** Automatic output escaping via Blade.
*   **SQL Injection Prevention:** Utilization of Eloquent ORM and prepared statements.
*   **Password Hashing:** Using Argon2 or Bcrypt via Laravel's `Hash` facade.
*   **Access Control:** Robust authorization using Policies and Gates.
*   **Input Sanitization:** Strict validation of all incoming user data.

---

# Deployment Instructions

When deploying to production, follow these steps to optimize performance:

1.  **Production Build:**
    ```bash
    npm run build
    ```
2.  **Optimize Laravel:**
    ```bash
    php artisan optimize
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```
3.  **Run Migrations:**
    ```bash
    php artisan migrate --force
    ```

---

# Useful Commands

*   `php artisan make:service {Name}`: Create a new service.
*   `php artisan monitor:queues`: View real-time queue metrics.
*   `php artisan logs:clear`: Clear application log files.
*   `npm run dev`: Start Vite development server.

---

# Troubleshooting

*   **Queue Not Working:** Ensure the `QUEUE_CONNECTION` is correctly set in `.env` and `php artisan queue:work` is running.
*   **Vite Assets Not Loading:** Run `npm run dev` or `npm run build` to generate the manifest files.
*   **Permission Denied:** Ensure `storage` and `bootstrap/cache` directories are writable by the web server.

---

# Contribution Guide

1.  **Branch Naming:** `feature/feature-name`, `fix/issue-name`.
2.  **Commit Format:** [Conventional Commits](https://www.conventionalcommits.org/).
3.  **PR Process:** All code must pass linting (`npm run lint` / `pint`) and tests before merging.

---

# License

Distributed under the MIT License. See `LICENSE` for more information.
