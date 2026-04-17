# KantorApp - Multi-Location HR, Attendance & Shift Management
### Modern "Cyberpunk Glassmorphism" Edition (Laravel 12)

**KantorApp** is a high-performance, multi-location HR management system designed for modern enterprises. Built with **Laravel 12**, it features a stunning "Cyberpunk Glassmorphism" UI, robust GPS-validated attendance, complex shift/roster scheduling, and a jobdesk-based task management system.

---

## 🚀 Key Features

### 🏢 Multi-Location Architecture
- **Hierarchical Access Control**: Super Admin, Location Admin, HR, and Employee roles.
- **Location Scoping**: Automatic data scoping based on assigned branch/location.
- **Custom Branding**: Every location can have its own brand name, logo, primary colors, and custom CSS.

### 🛡️ Security & Authentication
- **2FA (Two-Factor Authentication)**: Protected by Email, SMS, or Authenticator Apps (with backup codes).
- **Hardened Login**: Brute-force throttling, single-session invalidation, and mandatory GPS/Device ID logging.
- **Audit Trails**: Detailed logs for critical data changes (shifts, approvals, mutations).

### 📍 GPS & Biometric Attendance
- **Geofencing Verification**: GPS-validated check-in/out within branch radius.
- **Biometric Selfie**: Browser-based camera capture for real-time verification (no spoofing).
- **Shift & Roster Sync**: Prevents check-in on holidays, weekly offs, or outside shift windows.
- **Recap & Reports**: Full-width attendance reports with Excel/CSV export and detailed absence tracking.

### 📅 Advanced Shift & Roster System
- **Master vs Location Shifts**: Create global templates and override them locally (slots/hours).
- **Roster Fairness Guard**: Anti-overlap protection, night-to-morning interval checks, and consecutive night shift limits.
- **Weekly Roster Calendar**: Visual calendar for managing employee rotations.
- **Auto-Scheduler**: Generate weekly rotations with a single click.

### 📝 Task & Jobdesk Management
- **Jobdesk-Based Catalog**: Employees can only pick tasks assigned to their jobdesk catalog.
- **Point-to-Minute Conversion**: Automated duration estimates (e.g., 1 point = 30 minutes).
- **Progress Slots & Evidence**: Tasks are divided into mandatory slots with 100% completion target. Slots require link/file evidence and manual approval.
- **Productivity Tracking**: Target minutes vs. actual approved minutes per location/month.

### 📊 Reporting & Efficiency
- **Monthly Work Recap**: Comprehensive overview of targets, approved slots, attendance, and remaining hours.
- **PDF Export**: One-click professional PDF generation with employee-specific naming.
- **Internal Messaging**: Integrated communication system for task-related discussions.
- **Location Change Requests**: Formal workflow for employees to request branch transfers.

---

## 🛠️ Technology Stack

- **Framework**: Laravel 12.x
- **Language**: PHP 8.2+
- **Styling**: Bootstrap 5 + Custom Glassmorphism UI
- **Database**: MySQL 8.0+ / MariaDB 10.5+
- **Assets**: Vite, Chart.js, Leaflet (Geocoding)
- **Dependencies**: Spatie Permission, Maatwebsite Excel, DomPDF

---

## 💻 Installation Guide

### Prerequisites
- PHP 8.2 or higher
- Composer 2.x
- Node.js 18.x & NPM
- MySQL 8.0+

### Step-by-Step Installation
1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd kantorapp
   ```
2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```
3. **Setup Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. **Configure Database**
   Edit `.env` and set your `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`.
5. **Migrate & Seed**
   ```bash
   php artisan migrate --seed
   php artisan db:seed --class=RoleSeeder
   ```
6. **Build Assets**
   ```bash
   npm run build
   ```
7. **Serve Application**
   ```bash
   php artisan serve
   ```

---

## 📦 Marketplace Requirements (Envato/Codester)

- **Documentation**: Comprehensive HTML guide included in `/documentation`.
- **Clean Code**: SOLID principles followed across controllers and models.
- **Localization**: Fully i18n ready (edit `lang/en.json` for translations).
- **Dark Mode**: Native cyberpunk aesthetic optimized for high-end SaaS presentation.

---

## 📜 Support & License

For support, please visit our help center or contact the developer via the marketplace profile. 
Developed with ❤️ by **mawantorouf**.

---
*© 2025 KantorApp. All rights reserved.*