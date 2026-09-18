<h1 align="center">HRMS-Filament</h1>

<p align="center">
A full-featured Human Resource Management System built with Laravel and Filament PHP, delivering role-based dashboards for administrators, HR personnel, and employees.
</p>

<p align="center">
<img src="https://img.shields.io/badge/version-1.0.0-blue.svg" />
<img src="https://img.shields.io/badge/php-^8.2-8892BF.svg" />
<img src="https://img.shields.io/badge/laravel-13-FF2D20.svg" />
<img src="https://img.shields.io/badge/filament-5-7B61FF.svg" />
<img src="https://img.shields.io/badge/tailwind--css-4-06B6D4.svg" />
</p>

---

## Overview

HRMS-Filament is a self-hosted workforce management platform designed for small to mid-sized organizations. It centralizes employee records, attendance tracking, leave management, payroll processing, and performance reviews into a single application with three purpose-built panels for administrators, HR staff, and employees.

Each panel provides a tailored interface with granular permission controls powered by Spatie Permission and Filament Shield, ensuring users only access data relevant to their role.

## Key Features

- **Multi-Panel System** - Separate Filament panels for Admin, HR, and Employee roles with independent dashboards
- **Role-Based Access Control** - Granular permissions managed through Filament Shield with Spatie Permission
- **Employee Management** - Centralized profiles with employment details, department assignments, and position hierarchy
- **Real-Time Attendance** - Check-in/check-out system with late detection (after 09:00), working hours calculation, and monthly summaries
- **Leave Management** - Configurable leave types, request submissions, and approval/rejection workflows with reasons
- **Payroll Automation** - Queue-based background job processing for bulk payroll generation with configurable allowances, deductions, and bonuses
- **Performance Reviews** - Multi-criteria evaluation (quality, productivity, communication, teamwork, leadership) with auto-calculated overall ratings
- **Department & Position Hierarchy** - Organizational structure with manager assignments and salary ranges
- **Live Dashboard Widgets** - Real-time statistics including employee counts, pending requests, attendance rates, and payroll summaries
- **Queue Processing** - Background job handling via Laravel Queue for scalability

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13, PHP 8.2+ |
| Admin Panel | Filament PHP 5 |
| Authorization | Spatie Laravel Permission, Filament Shield |
| Frontend | Livewire 3, Alpine.js |
| Styling | Tailwind CSS 4 |
| Build Tool | Vite 8 |
| Database | SQLite (default), MySQL, PostgreSQL |
| Testing | Pest PHP 5 |

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- SQLite, MySQL, or PostgreSQL

### Quick Setup

```bash
git clone https://github.com/shadrackjm/hrms.git
cd hrms
composer setup
```

The `composer setup` script runs all steps automatically: dependency installation, environment configuration, key generation, database migration, seeding, and asset compilation.

### Manual Setup

```bash
git clone https://github.com/shadrackjm/hrms.git
cd hrms

composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate --seed

npm run build
```

## Configuration

### Environment Variables

Copy `.env.example` to `.env` and configure the following key variables:

| Variable | Description | Default |
|---|---|---|
| `APP_NAME` | Application name displayed in the panel | `Laravel` |
| `APP_URL` | Base URL of the application | `http://localhost` |
| `APP_DEBUG` | Enable debug mode | `true` |
| `DB_CONNECTION` | Database driver (`sqlite`, `mysql`, `pgsql`) | `sqlite` |
| `DB_HOST` | Database host | `127.0.0.1` |
| `DB_PORT` | Database port | `3306` |
| `DB_DATABASE` | Database name | `laravel` |
| `DB_USERNAME` | Database username | `root` |
| `DB_PASSWORD` | Database password | (empty) |
| `QUEUE_CONNECTION` | Queue driver for background jobs | `database` |
| `SESSION_DRIVER` | Session storage driver | `database` |

### Default Accounts

After running `php artisan migrate --seed`, the following test accounts are available:

| Role | Email | Password |
|---|---|---|
| Super Admin | `superadmin@example.com` | `password` |
| Admin | `admin@example.com` | `password` |
| HR | `hr@example.com` | `password` |
| Employee | `employee@example.com` | `password` |

> Change all default credentials before deploying to production.

## Usage

### Starting the Application

```bash
# Run all services concurrently (server, queue, vite)
composer dev

# Or start individually
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

### Panel Routing

Users are automatically redirected to the appropriate panel based on their assigned role:

| Panel | URL | Roles |
|---|---|---|
| Admin | `/admin` | `super_admin`, `admin` |
| HR | `/hr` | `hr` |
| Employee | `/employee` | `employee` |

### Employee Check-In/Out

Employees access a dedicated page with:
- Live clock display
- One-click check-in and check-out with confirmation
- Automatic late detection (check-in after 09:00)
- Working hours calculation
- Monthly attendance summary (present, late, absent counts)

### Payroll Generation

Payrolls are generated via a background job:

```bash
php artisan queue:work
```

The job processes active employees, calculates net salary using the formula:

```
net_salary = basic_salary + allowances + bonus - deductions
```

Default values: allowances = 500, bonus = 200, deductions = 150.

### Running Tests

```bash
composer test
```

## Project Structure

```
app/
├── Filament/
│   ├── Resources/              # Admin panel resources (Users, Departments, Positions, LeaveTypes)
│   ├── Hr/Resources/           # HR panel resources (Attendances, LeaveRequests, Payrolls, PerformanceReviews)
│   ├── Employee/Resources/     # Employee panel resources (self-service CRUD)
│   ├── Pages/Auth/             # Custom login page with role-based redirect
│   └── Widgets/                # Dashboard statistics widgets
├── Jobs/
│   └── GeneratePayrollJob.php  # Queue-based payroll generation
├── Models/                     # 8 Eloquent models
├── Policies/                   # 9 authorization policies
└── Providers/
    └── Filament/               # Panel providers (Admin, HR, Employee)

database/
├── migrations/                 # 13 database migrations
└── seeders/                    # Roles and default user seeding

resources/
├── css/                        # Tailwind CSS entry
├── js/                         # JavaScript entry
└── views/                      # Blade templates (check-in/out page)
```

## Available Scripts

| Command | Description |
|---|---|
| `composer setup` | Full project setup (install, configure, migrate, build) |
| `composer dev` | Start server, queue worker, and Vite concurrently |
| `composer test` | Clear config cache and run Pest test suite |
| `npm run dev` | Start Vite development server with hot reload |
| `npm run build` | Compile production-ready assets |
| `php artisan migrate --seed` | Run all migrations and seed default data |
| `php artisan queue:work` | Process background jobs |
| `php artisan shield:generate` | Regenerate Filament Shield permissions |

## Roles and Permissions

| Role | Panels | Capabilities |
|---|---|---|
| `super_admin` | Admin, HR, Employee | Full access to all resources and panels |
| `admin` | Admin | User, Department, Position, LeaveType CRUD; LeaveRequest, Payroll, PerformanceReview CRUD |
| `hr` | HR | LeaveRequest, Payroll, PerformanceReview CRUD; Attendance view/create/update |
| `employee` | Employee | View own LeaveRequest, Payroll, PerformanceReview; Attendance check-in/out |

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/your-feature`)
3. Commit your changes (`git commit -m 'Add your feature'`)
4. Push to the branch (`git push origin feature/your-feature`)
5. Open a Pull Request

### Guidelines

- Follow PSR-12 coding standards
- Write Pest tests for new features
- Run `composer test` before submitting
- Update documentation for user-facing changes
