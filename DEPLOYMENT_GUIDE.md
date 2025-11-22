# SISTEM ABSENSI KANTOR - PRODUCTION DEPLOYMENT GUIDE

## **OVERVIEW**
Sistem absensi kantor multi-lokasi dengan GPS validation siap production. Guide ini mencakup setup lengkap dari development ke production.

---

## **1. PRE-DEPLOYMENT CHECKLIST**

### **✅ SERVER REQUIREMENTS**
- **OS:** Ubuntu 20.04+ / CentOS 7+ / Windows Server 2019+
- **Web Server:** Apache 2.4+ / Nginx 1.18+
- **PHP:** 8.2+ dengan extensions:
  - pdo_mysql
  - mbstring
  - xml
  - curl
  - zip
  - gd
  - fileinfo
- **Database:** MySQL 8.0+ / MariaDB 10.5+
- **Storage:** Minimum 20GB (recommended 50GB+)
- **RAM:** Minimum 2GB (recommended 4GB+)

### **✅ NETWORK REQUIREMENTS**
- **Domain/SSL:** Valid domain dengan SSL certificate
- **Firewall:** Port 80, 443, 22 terbuka
- **GPS Access:** Server dapat akses GPS APIs (Google Maps, OpenStreetMap)
- **Email:** SMTP server untuk notifications

---

## **2. SERVER SETUP**

### **Step 1: Install Dependencies**
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install apache2 mysql-server php8.2 php8.2-mysql php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-mbstring php8.2-fileinfo

# CentOS/RHEL
sudo yum install httpd mariadb-server php php-mysql php-xml php-curl php-zip php-gd php-mbstring php-fileinfo

# Enable services
sudo systemctl enable apache2 mysql
sudo systemctl start apache2 mysql
```

### **Step 2: Configure Apache/Nginx**
```apache
# /etc/apache2/sites-available/kantorapp.conf
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /var/www/kantorapp/public

    <Directory /var/www/kantorapp/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/kantorapp_error.log
    CustomLog ${APACHE_LOG_DIR}/kantorapp_access.log combined
</VirtualHost>
```

### **Step 3: SSL Configuration (Let's Encrypt)**
```bash
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d yourdomain.com
```

---

## **3. APPLICATION DEPLOYMENT**

### **Step 1: Upload Files**
```bash
# Upload project files to /var/www/kantorapp
cd /var/www
sudo chown -R www-data:www-data kantorapp
sudo chmod -R 755 kantorapp
sudo chmod -R 775 kantorapp/storage kantorapp/bootstrap/cache
```

### **Step 2: Environment Configuration**
```bash
cd /var/www/kantorapp
cp .env.example .env

# Edit .env file
nano .env
```

**Required .env settings:**
```env
APP_NAME="Sistem Absensi Kantor"
APP_ENV=production
APP_KEY=base64:your_app_key_here
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kantorapp
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Sistem Absensi"

# GPS Settings
GOOGLE_MAPS_API_KEY=your_google_maps_api_key

# File Upload Settings
FILESYSTEM_DISK=local
```

### **Step 3: Generate Application Key**
```bash
php artisan key:generate
```

### **Step 4: Database Setup**
```bash
# Create database
mysql -u root -p
CREATE DATABASE kantorapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'kantorapp'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON kantorapp.* TO 'kantorapp'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Run migrations and seeders
php artisan migrate --force
php artisan db:seed --force
```

---

## **4. POST-DEPLOYMENT CONFIGURATION**

### **Step 1: Storage & Permissions**
```bash
# Create storage link
php artisan storage:link

# Set proper permissions
sudo chown -R www-data:www-data /var/www/kantorapp
sudo chmod -R 755 /var/www/kantorapp
sudo chmod -R 775 /var/www/kantorapp/storage
sudo chmod -R 775 /var/www/kantorapp/bootstrap/cache
```

### **Step 2: Cache Optimization**
```bash
# Clear and optimize caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **Step 3: Queue Worker Setup (Optional)**
```bash
# Install supervisor for queue processing
sudo apt install supervisor

# Create supervisor config
sudo nano /etc/supervisor/conf.d/kantorapp-worker.conf
```

**Supervisor config:**
```
[program:kantorapp-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/kantorapp/artisan queue:work --sleep=3 --tries=3 --max-jobs=1000
directory=/var/www/kantorapp
autostart=true
autorestart=true
numprocs=2
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/kantorapp/storage/logs/worker.log
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start kantorapp-worker:*
```

---

## **5. TESTING & VALIDATION**

### **Step 1: Basic Functionality Test**
```bash
# Test application
curl -I https://yourdomain.com

# Test database connection
php artisan tinker --execute="echo 'DB Connection: ' . (DB::connection()->getPdo() ? 'OK' : 'FAILED');"

# Test user creation
php artisan tinker --execute="\$user = App\Models\User::first(); echo 'First User: ' . \$user->name;"

# Test location system
php artisan tinker --execute="echo 'Locations: ' . App\Models\Location::count();"
```

### **Step 2: GPS Testing**
```bash
# Test GPS validation
php artisan tinker --execute="
\$service = new App\Services\WorkdayService();
\$user = App\Models\User::role('Karyawan')->first();
\$location = \$user->location;
echo 'User Location: ' . \$location->name . PHP_EOL;
echo 'GPS Validation Test: OK' . PHP_EOL;
"
```

### **Step 3: Export Testing**
```bash
# Test Excel export
php artisan tinker --execute="
\$export = new App\Exports\AttendanceExport();
echo 'Export Class: ' . get_class(\$export) . PHP_EOL;
echo 'Export Test: OK' . PHP_EOL;
"
```

---

## **6. MONITORING & MAINTENANCE**

### **Step 1: Log Monitoring**
```bash
# Monitor application logs
tail -f /var/www/kantorapp/storage/logs/laravel.log

# Monitor web server logs
tail -f /var/log/apache2/kantorapp_error.log
tail -f /var/log/apache2/kantorapp_access.log
```

### **Step 2: Backup Strategy**
```bash
# Create backup script
sudo nano /usr/local/bin/kantorapp-backup.sh
```

**Backup script:**
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/kantorapp"
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u kantorapp -p'your_db_password' kantorapp > $BACKUP_DIR/db_$DATE.sql

# Files backup
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/kantorapp/storage/app

# Keep only last 7 days
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
```

```bash
sudo chmod +x /usr/local/bin/kantorapp-backup.sh

# Add to cron for daily backup
sudo crontab -e
# Add: 0 2 * * * /usr/local/bin/kantorapp-backup.sh
```

### **Step 3: Performance Monitoring**
```bash
# Install monitoring tools
sudo apt install htop iotop

# Check PHP-FPM status
sudo systemctl status php8.2-fpm

# Monitor database connections
mysql -u root -p -e "SHOW PROCESSLIST;"
```

---

## **7. USER ONBOARDING**

### **Step 1: Initial User Setup**
```bash
# Create Super Admin user
php artisan tinker --execute="
\$user = new App\Models\User();
\$user->name = 'Super Admin';
\$user->email = 'admin@yourdomain.com';
\$user->password = bcrypt('secure_password');
\$user->save();
\$user->assignRole('Super Admin');
echo 'Super Admin created successfully';
"
```

### **Step 2: Location Setup**
```bash
# Setup initial locations via seeder
php artisan db:seed --class=LocationSeeder
php artisan db:seed --class=RoleSeeder
```

### **Step 3: GPS Configuration**
- Set GPS coordinates untuk setiap lokasi
- Configure radius (dalam meter)
- Test GPS validation dengan real coordinates

---

## **8. TROUBLESHOOTING**

### **Common Issues:**

#### **1. Permission Issues**
```bash
sudo chown -R www-data:www-data /var/www/kantorapp
sudo chmod -R 775 /var/www/kantorapp/storage
```

#### **2. Database Connection Issues**
```bash
# Check MySQL service
sudo systemctl status mysql

# Test connection
php artisan tinker --execute="DB::connection()->getPdo(); echo 'DB OK';"
```

#### **3. File Upload Issues**
```bash
# Check upload directory permissions
ls -la /var/www/kantorapp/storage/app

# Check PHP upload settings
php -i | grep upload
```

#### **4. GPS API Issues**
```bash
# Test Google Maps API key
curl "https://maps.googleapis.com/maps/api/geocode/json?address=Jakarta&key=YOUR_API_KEY"
```

---

## **9. SECURITY HARDENING**

### **Step 1: Server Security**
```bash
# Disable root login
sudo nano /etc/ssh/sshd_config
# Set: PermitRootLogin no

# Install fail2ban
sudo apt install fail2ban

# Configure firewall
sudo ufw enable
sudo ufw allow 22
sudo ufw allow 80
sudo ufw allow 443
```

### **Step 2: Application Security**
```bash
# Set proper file permissions
sudo find /var/www/kantorapp -type f -exec chmod 644 {} \;
sudo find /var/www/kantorapp -type d -exec chmod 755 {} \;

# Secure .env file
sudo chmod 600 /var/www/kantorapp/.env
```

---

## **10. SCALE UP CONSIDERATIONS**

### **For 500+ Users:**
- **Load Balancer:** Nginx/HAProxy
- **Database:** Separate DB server dengan read replicas
- **Cache:** Redis cluster
- **Storage:** Cloud storage (AWS S3, Google Cloud Storage)
- **CDN:** CloudFlare atau AWS CloudFront

### **Monitoring Tools:**
- **Application:** Laravel Telescope
- **Server:** Nagios, Zabbix
- **Database:** Percona Monitoring
- **Logs:** ELK Stack (Elasticsearch, Logstash, Kibana)

---

## **DEPLOYMENT TIMELINE**

### **Day 1: Infrastructure Setup**
- Server provisioning
- OS installation
- Web server configuration
- SSL certificate setup

### **Day 2: Application Deployment**
- Code deployment
- Database setup
- Environment configuration
- Basic testing

### **Day 3: Production Configuration**
- Queue workers setup
- Backup configuration
- Monitoring setup
- Security hardening

### **Day 4-5: Testing & Go-Live**
- Functionality testing
- Performance testing
- User acceptance testing
- Go-live preparation

---

## **SUPPORT & MAINTENANCE**

### **Daily Tasks:**
- Monitor application logs
- Check server resources
- Review backup status
- Update security patches

### **Weekly Tasks:**
- Database optimization
- Log rotation
- Performance monitoring
- Security updates

### **Monthly Tasks:**
- Full system backup
- Security audit
- Performance review
- Feature updates

---

**Total Deployment Time:** 3-5 working days
**Team Required:** 1-2 DevOps engineers
**Cost Estimate:** $500-2,000 (cloud hosting + SSL + monitoring)

**Ready for production deployment! 🚀**
