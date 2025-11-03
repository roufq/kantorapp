# KantorApp - Multi-Location Attendance Management System

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About KantorApp

KantorApp is a comprehensive multi-location attendance management system built with Laravel, designed to streamline workforce management across multiple office locations. The system provides GPS-validated attendance tracking, flexible shift scheduling, task management, overtime handling, and comprehensive reporting capabilities.

### Key Features

#### 🏢 Multi-Location Support
- **Location Management**: Create and manage multiple office locations
- **Location-Based Access Control**: Role-based permissions scoped to specific locations
- **Location Settings**: Customize settings per location (GPS coordinates, branding, schedules)
- **Location Transfer**: Seamless employee transfers between locations

#### 📍 GPS-Validated Attendance
- **Real-time GPS Tracking**: Accurate location validation during check-in/out
- **Geofencing**: Configurable location boundaries with radius settings
- **Anti-Spoofing**: Advanced GPS validation to prevent location manipulation
- **Attendance History**: Complete audit trail of attendance records

#### ⏰ Flexible Shift Management
- **Dynamic Shift Scheduling**: Create custom shifts with flexible time slots
- **Shift Assignments**: Assign employees to specific shifts on specific dates
- **Shift Rotation**: Support for rotating schedules and patterns
- **Overtime Integration**: Automatic overtime calculation based on shift hours

#### 📋 Task Management
- **Employee Tasks**: Daily task tracking and progress monitoring
- **Master Tasks**: Administrative task assignment and oversight
- **File Attachments**: Support for photos and documents in tasks
- **Task Categories**: Organize tasks by type and priority

#### 💼 Overtime & Leave Management
- **Overtime Requests**: Employee overtime submission with approval workflow
- **Leave Management**: Comprehensive leave tracking (annual, sick, personal)
- **Holiday Calendar**: Configurable holidays and weekly offs
- **Approval Workflows**: Multi-level approval system for requests

#### 📊 Reporting & Analytics
- **Excel Exports**: Comprehensive attendance and overtime reports
- **Dashboard Analytics**: Real-time metrics and KPIs
- **Custom Reports**: Filtered reports by location, department, date range
- **Data Visualization**: Charts and graphs for attendance patterns

#### 👥 User Management & Communication
- **Role-Based Access**: Super Admin, Location Admin, Employee roles
- **Employee Directory**: Centralized employee information management
- **Internal Messaging**: Real-time communication between users
- **User Transfer**: Administrative tools for user management

#### 🔒 Security & Compliance
- **Data Encryption**: Secure data storage and transmission
- **Audit Trails**: Complete logging of all system activities
- **GDPR Ready**: Privacy-compliant data handling
- **Multi-Tenant Architecture**: Isolated data per organization

## Technical Stack

- **Framework**: Laravel 12.0
- **PHP**: 8.2+
- **Database**: MySQL 8.0+ / MariaDB 10.5+
- **Frontend**: Blade Templates, AdminLTE UI
- **Authentication**: Laravel Sanctum (API-ready)
- **Authorization**: Spatie Laravel Permission
- **Real-time**: Laravel Reverb (WebSockets)
- **Export**: Maatwebsite Laravel Excel
- **Queue**: Database/Redis queue system

## System Requirements

### Server Requirements
- **OS**: Ubuntu 20.04+, CentOS 7+, Windows Server 2019+
- **Web Server**: Apache 2.4+ / Nginx 1.18+
- **PHP**: 8.2+ with extensions:
  - `pdo_mysql`, `mbstring`, `xml`, `curl`, `zip`, `gd`, `fileinfo`
- **Database**: MySQL 8.0+ / MariaDB 10.5+
- **RAM**: Minimum 2GB (Recommended 4GB+)
- **Storage**: Minimum 20GB (Recommended 50GB+)

### Client Requirements
- Modern web browser (Chrome, Firefox, Safari, Edge)
- JavaScript enabled
- GPS-enabled device for mobile attendance
- Stable internet connection

## Installation

### Quick Setup (Development)

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd kantorapp
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database configuration**
   - Create MySQL database
   - Update `.env` with database credentials
   ```bash
   php artisan migrate --seed
   ```

5. **Build assets**
   ```bash
   npm run build
   # or for development
   npm run dev
   ```

6. **Start the application**
   ```bash
   php artisan serve
   ```

### Automated Setup Script

The project includes an automated setup script:

```bash
composer run setup
```

This will:
- Install PHP dependencies
- Generate application key
- Run database migrations
- Seed initial data
- Install and build frontend assets

## Usage

### User Roles

#### Super Admin
- Full system access across all locations
- User management and role assignment
- Location creation and configuration
- System-wide reporting and analytics

#### Location Admin (Admin Lokasi)
- Manage users within their location
- Configure location-specific settings
- Approve attendance and overtime requests
- Generate location-specific reports

#### Employee (Karyawan)
- Daily attendance check-in/out
- Task management and submission
- Overtime request submission
- Leave request management

### Key Workflows

#### Daily Attendance
1. Employee checks in via web/mobile interface
2. GPS validation confirms location
3. System records check-in time and location
4. Employee checks out at end of shift
5. System calculates total hours worked

#### Shift Assignment
1. Admin creates shift schedules
2. Assigns employees to specific shifts
3. System validates shift conflicts
4. Employees receive shift notifications
5. Attendance system uses shift data for validation

#### Overtime Management
1. Employee submits overtime request
2. Location admin reviews and approves
3. System calculates overtime hours
4. Payroll integration (if configured)
5. Reports generated for accounting

## Application Modules

The following modules and menus are available in the web application. Each item lists the main routes and required roles.

- Dashboard
  - `GET /dashboard` — Overview cards, recent assignments, notices (holidays/leaves), location metrics.

- Messages
  - `GET /messages` — List conversations
  - `GET /messages/{user}` — Conversation detail
  - `POST /messages` — Send message
  - `PATCH /messages/{id}/read` — Mark as read
  - `DELETE /messages/{id}` — Delete (Super Admin)

- Tasks (Employee Tasks)
  - `GET /tasks` — List
  - `GET /tasks/create` — Create (assign)
  - `POST /tasks` — Store
  - `GET /tasks/create-self` — Create task for self
  - `POST /tasks/store-self` — Store self task
  - `GET /tasks/{task}` — Show
  - `GET /tasks/{task}/edit` — Edit
  - `PATCH /tasks/{task}` — Update
  - `DELETE /tasks/{task}` — Delete
  - `GET /tasks/{task}/download-photo` — Download attachment (photo)
  - `GET /tasks/{task}/download-document` — Download attachment (document)

- Master Tasks (Super Admin)
  - `GET /master-tasks` — List (Super Admin)
  - `GET /master-tasks/create` — Create (Super Admin)
  - `POST /master-tasks` — Store
  - `GET /master-tasks/create-self` — Create for self
  - `POST /master-tasks/store-self` — Store self
  - `GET /master-tasks/{masterTask}` — Show
  - `GET /master-tasks/{masterTask}/edit` — Edit
  - `PATCH /master-tasks/{masterTask}` — Update
  - `DELETE /master-tasks/{masterTask}` — Delete
  - `GET /master-tasks/{masterTask}/download-photo` — Download photo
  - `GET /master-tasks/{masterTask}/download-document` — Download document

- Attendance
  - `GET /attendance/checkin` — Check In/Out page
  - `POST /attendance/checkin` — Check in
  - `POST /attendance/checkout` — Check out
  - `GET /attendance/report` — Attendance report
  - `GET /attendance/export` — Export to Excel
  - `PATCH /attendance/{id}/approval` — Update approval
  - `GET /attendance/absences` — Absence list
  - `GET /attendance/recap` — Recap view

- Overtime
  - `GET /overtime` — List requests
  - `GET /overtime/create` — Create
  - `POST /overtime` — Store
  - `GET /overtime/{overtime}` — Show
  - `GET /overtime/export` — Export to Excel
  - `GET /overtime-report` — Report view
  - `PATCH /overtime/{overtime}/approve` — Approve (Super Admin)

- Holidays, Weekly Offs, Leaves
  - Holidays (Super Admin, Admin Lokasi): `GET/POST/DELETE /holidays`
  - Weekly Offs (Super Admin, Admin Lokasi): `GET/POST/DELETE /weekly-offs`
  - Leaves: `GET /leaves`, `POST /leaves` (Super Admin, Admin Lokasi, Karyawan), `PATCH /leaves/{leave}/status` (Super/Admin Lokasi)

- Locations
  - Location settings: `GET /locations/{location}` show, `GET /locations/{location}/settings`, `PATCH /locations/{location}/settings`
  - Location admins (Super Admin): `resource /location-admins`
  - Location admin tasks (Super/Admin Lokasi): `resource /location-admin-tasks` + download routes
  - Location change requests: `GET /location-change-requests` (index/create/store), `PATCH /location-change-requests/{id}/status` (Admin Lokasi)

- Shifts & Assignments
  - Shifts (Super Admin): `resource /shifts`
  - Location shifts (Super Admin): `resource /location-shifts`, `POST /location-shifts/{location}/attach-shift`, `DELETE /location-shifts/{location}/detach-shift/{shift}`
  - Shift assignments (Super/Admin Lokasi): `resource /shift-assignments`, `GET /shift-assignments-export`

- Employees (Karyawan)
  - `resource /karyawans` (Super Admin, Admin Lokasi)

- Two-Factor Authentication (2FA)
  - `GET /2fa/setup` — Choose method: SMS, Email, Authenticator App
  - `POST /2fa/setup` — Persist method (and secret for app)
  - `GET /2fa/verify` — Enter code (email/sms/app)
  - `POST /2fa/verify` — Verify code (enables 2FA and generates backup codes on first confirmation)
  - `POST /2fa/disable` — Disable 2FA (requires authenticated session)
  - Challenge flow: `GET /2fa/challenge` during login, then redirect to `2fa.verify`

## Security & Middleware

- Role-based authorization via Spatie Permission
  - Roles used: `Super Admin`, `Admin Lokasi`, `Karyawan`
  - Custom `RoleMiddleware` supports comma/pipe separated roles
- TwoFactorMiddleware
  - Protects authenticated routes until 2FA is verified (`session('2fa_verified')`)
  - Skips only setup/verify routes (not disable)
- 2FA storage
  - `users.two_factor_secret` stored encrypted
  - Backup codes generated and consumed on use
  - Basic session-based brute-force guard for verification attempts

## Exports

- Attendance, Overtime, Shift Assignments exports using Laravel Excel
- Files are generated through dedicated controller actions: `/attendance/export`, `/overtime/export`, `/shift-assignments-export`

## Frontend & UX

- Layout based on AdminLTE (Bootstrap 5)
- Mobile-friendly sidebar
  - Header hamburger and in-sidebar hamburger
  - Auto-close on menu/submenu click in mobile
  - Overlay managed to prevent interaction lock
- Per-location branding
  - Dynamic CSS variables for primary/secondary colors
  - Optional custom CSS per location
  - Optional theme stylesheet: `public/themes/{theme}.css`
- Custom assets
  - `public/asset/app.css` — app styles (helpers + sidebar overlay)
  - `public/asset/app.js` — sidebar behavior and OverlayScrollbars init

## Configuration

- `.env` notable keys
  - `SESSION_DRIVER=database`
  - `QUEUE_CONNECTION=database`
  - `CACHE_STORE=database`
  - `MAIL_MAILER=log` (dev) — set SMTP in production
  - Twilio (if SMS 2FA enabled): `TWILIO_SID`, `TWILIO_TOKEN`, `TWILIO_FROM`
  - Reverb (WebSocket) keys if used for real-time

## Testing

- Run tests
  - `composer test` or `php artisan test`
- Included feature tests
  - `tests/Feature/TwoFactorAuthTest.php` — covers 2FA setup/verify/disable and backup codes

## Developer Scripts

- Composer
  - `composer run setup` — one-shot local setup
  - `composer run dev` — serve app, queue listener, and Vite dev server concurrently
  - `composer run test` — clear config cache and run tests


## Deployment

### Production Deployment

For detailed production deployment instructions, see [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md).

#### Quick Production Setup

1. **Server preparation**
   ```bash
   # Ubuntu/Debian
   sudo apt update
   sudo apt install apache2 mysql-server php8.2 php8.2-mysql php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-mbstring php8.2-fileinfo
   ```

2. **Application deployment**
   ```bash
   # Upload files to /var/www/kantorapp
   cd /var/www/kantorapp
   composer install --optimize-autoloader --no-dev
   npm install && npm run build
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   # Edit .env with production settings
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   ```

5. **Optimization**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan storage:link
   ```

6. **Queue worker (optional)**
   ```bash
   php artisan queue:work --daemon
   ```

### SSL Configuration

```bash
# Using Let's Encrypt
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d yourdomain.com
```

### Monitoring & Maintenance

- **Logs**: Monitor `/storage/logs/laravel.log`
- **Backups**: Automated daily database backups
- **Updates**: Regular security updates and patches
- **Performance**: Monitor response times and resource usage

## Configuration

### Environment Variables

Key configuration options in `.env`:

```env
# Application
APP_NAME="KantorApp"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=kantorapp
DB_USERNAME=your_user
DB_PASSWORD=your_password

# GPS Settings
GOOGLE_MAPS_API_KEY=your_api_key

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password

# Queue (Redis recommended for production)
QUEUE_CONNECTION=database
# QUEUE_CONNECTION=redis
```

### Location Settings

Each location can be configured with:
- GPS coordinates and radius
- Working hours and shifts
- Branding colors and logo
- Custom policies and rules

## API Documentation

The system includes REST API endpoints for integration:

- Authentication: `/api/login`, `/api/logout`
- Attendance: `/api/attendance/checkin`, `/api/attendance/checkout`
- Tasks: `/api/tasks`, `/api/tasks/{id}`
- Reports: `/api/reports/attendance`, `/api/reports/overtime`

API documentation available at `/api/documentation` when in development mode.

## Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

### Test Coverage

- Unit tests for models and services
- Feature tests for critical workflows
- Integration tests for API endpoints
- Browser tests for UI interactions

## Security

### Security Features

- **CSRF Protection**: All forms protected against CSRF attacks
- **XSS Prevention**: Input sanitization and output escaping
- **SQL Injection Prevention**: Parameterized queries
- **Rate Limiting**: API and login attempt limiting
- **Data Encryption**: Sensitive data encrypted at rest
- **Audit Logging**: Complete activity logging

### Security Best Practices

- Regular security updates
- Strong password policies
- Two-factor authentication ready
- Secure session management
- File upload restrictions

## Contributing

Thank you for considering contributing to KantorApp!

### Development Setup

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new features
5. Ensure all tests pass
6. Submit a pull request

### Coding Standards

- Follow PSR-12 coding standards
- Use meaningful commit messages
- Write comprehensive tests
- Update documentation for new features

## License

KantorApp is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

### Documentation
- [Deployment Guide](DEPLOYMENT_GUIDE.md)
- [API Documentation](api/documentation)
- [User Manual](docs/user-manual.md)

### Community
- Report issues on GitHub
- Join our Discord community
- Check documentation for FAQs

### Professional Support
- Email: support@kantorapp.com
- Priority support packages available
- Custom development services

---

**Built with ❤️ using Laravel**

*Empowering businesses with intelligent workforce management*
