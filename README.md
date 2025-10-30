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
