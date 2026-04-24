# OmniPresence HRIS - Enterprise Multi-Location & Geofencing System
### High-Fidelity Management Suite with "Executive Soft" UI (Laravel 12)

**OmniPresence HRIS** is a high-performance, multi-location HR management system designed for modern enterprises. Built with **Laravel 12**, it features a stunning "Executive Soft" UI/UX, robust GPS-validated attendance, complex shift/roster scheduling, and a jobdesk-based task management system.

---

## 🚀 Key Features

### 🏢 Multi-Location Architecture
- **Hierarchical Access Control**: Super Admin, Location Admin, HR, and Employee roles.
- **Location Scoping**: Automatic data scoping based on assigned branch/location.
- **Custom Branding**: Every location can have its own brand name, logo, primary colors, and custom CSS.

### 🛡️ Security & Authentication
- **2FA (Two-Factor Authentication)**: Protected by Email, SMS, or Authenticator Apps (Google Authenticator) with backup codes.
- **Hardened Login**: Brute-force throttling, single-session invalidation, and mandatory GPS logging.
- **Audit Trails**: Detailed logs for critical data changes (shifts, approvals, mutations).

### 📍 GPS & Biometric Attendance
- **Geofencing Verification**: GPS-validated check-in/out within branch radius.
- **Biometric Selfie**: Browser-based camera capture for real-time verification (no spoofing).
- **Shift & Roster Sync**: Prevents check-in on holidays, weekly offs, or outside shift windows.
- **Recap & Reports**: Comprehensive attendance reports with Excel/CSV export and detailed absence tracking.

### 📅 Advanced Shift & Roster System
- **Master vs Location Shifts**: Create global templates and override them locally (slots/hours).
- **Roster Fairness Guard**: Anti-overlap protection, night-to-morning interval checks, and consecutive night shift limits.
- **Weekly Roster Calendar**: Professional visual calendar for managing employee rotations.
- **Auto-Scheduler**: Generate weekly rotations with a single click.

### 📝 Task & Jobdesk Management
- **Jobdesk-Based Catalog**: Employees pick tasks assigned to their specific jobdesk catalog.
- **Progress Slots & Evidence**: Tasks are divided into mandatory slots requiring link/file evidence and manual approval.
- **Productivity Tracking**: Target minutes vs. actual approved minutes per location/month.

### 🤖 Telegram Bot Assistant
- **Real-Time Alerts**: Receive attendance and task notifications directly in Telegram.
- **Transaction Undo**: Revert accidental check-ins/outs via bot commands.
- **Quick Recaps**: View monthly summaries without logging into the web dashboard.

---

## 🛠️ Technology Stack
- **Framework**: Laravel 12.x
- **Language**: PHP 8.2+
- **Styling**: Bootstrap 5 + Custom "Executive Soft" UI Design
- **Database**: MySQL 8.0+ / MariaDB 10.5+
- **Assets**: Vite, Chart.js, Leaflet (Geocoding)

---

## 💻 Installation Guide

### 1. Prerequisites
- PHP 8.2 or higher
- Composer 2.x
- Node.js 18.x & NPM
- MySQL 8.0+

### 2. Step-by-Step Setup
```bash
# Install Dependencies
composer install
npm install && npm run build

# Setup Environment
cp .env.example .env
php artisan key:generate

# Migrate & Seed Database
php artisan migrate --seed

# Create Storage Link
php artisan storage:link

# Serve Application
php artisan serve
```

---

## 🔐 Default Access Credentials
Username & Password: `password`

| Role | Email |
| :--- | :--- |
| **Super Admin** | `superadmin1@example.com` |
| **Location Admin** | `adminlokasi1@example.com` |
| **Employee** | `employee1@example.com` |

---

## 📜 Professional Documentation
- **Marketplace Ready**: Fully i18n ready with clean architectural structure.
- **Premium UX**: Native "Executive Soft" aesthetic optimized for high-end SaaS presentation.
- **Licensed Software**: Developed with ❤️ by **roufq**.

---
*© 2025 OmniPresence HRIS. All rights reserved.*