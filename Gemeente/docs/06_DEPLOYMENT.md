# 🚀 Deployment Guide - Production Ready

**Onderwerp:** Deploy Gemeente Klachtensysteem naar Production  
**Datum:** 6 oktober 2025

---

## 📋 Inhoudsopgave

1. [Pre-Deployment Checklist](#pre-deployment-checklist)
2. [Server Requirements](#server-requirements)
3. [Deployment Opties](#deployment-opties)
4. [Production Setup](#production-setup)
5. [Security Hardening](#security-hardening)
6. [Performance Optimization](#performance-optimization)
7. [Monitoring & Logging](#monitoring--logging)
8. [Backup Strategy](#backup-strategy)
9. [CI/CD Pipeline](#cicd-pipeline)

---

## ✅ Pre-Deployment Checklist

### Code Preparation

```bash
# 1. Run tests
php artisan test

# 2. Static analysis
./vendor/bin/phpstan analyse

# 3. Code style
./vendor/bin/pint

# 4. Security audit
composer audit

# 5. Optimize autoloader
composer install --optimize-autoloader --no-dev

# 6. Build frontend assets
npm run build

# 7. Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Environment Checklist

- [ ] `.env` configured voor production
- [ ] `APP_DEBUG=false` gezet
- [ ] `APP_ENV=production` gezet
- [ ] Sterke `APP_KEY` gegenereerd
- [ ] Database credentials geconfigureerd
- [ ] Mail credentials geconfigureerd
- [ ] Storage permissions correct (755/644)
- [ ] SSL certificaat geïnstalleerd
- [ ] Firewall geconfigureerd
- [ ] Backup systeem operationeel

---

## 💻 Server Requirements

### Minimum Requirements

```yaml
OS: Ubuntu 22.04 LTS / Debian 11
PHP: 8.3 of hoger
Database: MySQL 8.0+ / PostgreSQL 13+ / MariaDB 10.6+
Web Server: Nginx / Apache 2.4+
Memory: 2GB RAM (minimum), 4GB (recommended)
Storage: 20GB (minimum)
Node.js: 18+ (voor asset building)
```

### PHP Extensions

```bash
# Required
php8.3-cli
php8.3-fpm
php8.3-mysql
php8.3-mbstring
php8.3-xml
php8.3-curl
php8.3-zip
php8.3-gd
php8.3-intl
php8.3-bcmath

# Optional (recommended)
php8.3-redis
php8.3-opcache
php8.3-apcu
```

### Install PHP 8.3 op Ubuntu

```bash
# Add repository
sudo add-apt-repository ppa:ondrej/php
sudo apt update

# Install PHP & extensions
sudo apt install -y php8.3-fpm \
  php8.3-cli \
  php8.3-mysql \
  php8.3-mbstring \
  php8.3-xml \
  php8.3-curl \
  php8.3-zip \
  php8.3-gd \
  php8.3-intl \
  php8.3-bcmath \
  php8.3-redis \
  php8.3-opcache

# Verify
php -v
```

---

## 🎯 Deployment Opties

### Optie 1: Shared Hosting (Eenvoudig)

**Voor: TransIP, Hostnet, One.com**

#### Upload Files

```bash
# Via FTP/SFTP upload:
gemeente/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/          # Dit is de web root!
├── resources/
├── routes/
├── storage/         # Zorg voor 755 permissions
├── vendor/
├── .env             # NIET in version control!
└── artisan
```

#### Important Settings

**1. Document Root moet naar `public/` folder:**
```
/domains/gemeente.nl/public_html/public/
```

**2. PHP Version:** 8.3

**3. Environment Variables in cPanel:**
```env
APP_NAME="Gemeente Klachtensysteem"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://gemeente.nl

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=username_gemeente
DB_USERNAME=username_dbuser
DB_PASSWORD=secure_password
```

**4. Run Commands via SSH:**
```bash
cd ~/domains/gemeente.nl/public_html
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### Optie 2: VPS/Cloud (Aanbevolen)

**Platforms: DigitalOcean, Linode, AWS, Vultr**

#### Initial Server Setup

```bash
# 1. Update system
sudo apt update && sudo apt upgrade -y

# 2. Install essentials
sudo apt install -y git curl wget unzip

# 3. Install Nginx
sudo apt install -y nginx

# 4. Install PHP 8.3 (zie hierboven)

# 5. Install MySQL
sudo apt install -y mysql-server
sudo mysql_secure_installation

# 6. Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# 7. Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# 8. Install Redis (optional)
sudo apt install -y redis-server
```

#### Nginx Configuration

**Create file: `/etc/nginx/sites-available/gemeente`**

```nginx
server {
    listen 80;
    server_name gemeente.nl www.gemeente.nl;
    root /var/www/gemeente/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;

    charset utf-8;

    # Logging
    access_log /var/log/nginx/gemeente-access.log;
    error_log /var/log/nginx/gemeente-error.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_types text/plain text/css text/xml text/javascript application/json application/javascript application/xml+rss;
}
```

**Enable site:**
```bash
sudo ln -s /etc/nginx/sites-available/gemeente /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

#### SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Generate certificate
sudo certbot --nginx -d gemeente.nl -d www.gemeente.nl

# Auto-renewal (automatisch ingeschakeld)
sudo systemctl status certbot.timer
```

#### Deploy Application

```bash
# 1. Create directory
sudo mkdir -p /var/www/gemeente
sudo chown -R $USER:$USER /var/www/gemeente

# 2. Clone repository
cd /var/www
git clone https://github.com/abii2024/gemeente.git
cd gemeente

# 3. Install dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# 4. Setup environment
cp .env.example .env
nano .env  # Edit configuration

# 5. Generate app key
php artisan key:generate

# 6. Setup storage
php artisan storage:link
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 7. Run migrations
php artisan migrate --force

# 8. Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 9. Optimize
php artisan optimize
```

---

### Optie 3: Laravel Forge (Eenvoudigst)

**Platform:** https://forge.laravel.com (€12/maand)

#### Setup

1. **Connect Server:**
   - Kies provider (DigitalOcean, Linode, AWS)
   - Forge configureert automatisch PHP, Nginx, MySQL

2. **Create Site:**
   - Domein: `gemeente.nl`
   - Root: `/public`
   - PHP versie: 8.3

3. **Deploy Script:**
```bash
cd /home/forge/gemeente.nl
git pull origin main
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
npm install
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
php artisan optimize
```

4. **Enable Quick Deploy:**
   - Auto-deploy bij Git push

5. **SSL Certificate:**
   - Klik "Let's Encrypt" → Done!

**Voordelen:**
- ✅ Zero-downtime deployment
- ✅ Automated backups
- ✅ Queue management
- ✅ Scheduler monitoring
- ✅ SSL auto-renewal

---

## 🔐 Security Hardening

### 1. Environment Configuration

**`.env` (NEVER commit this!):**

```env
APP_NAME="Gemeente Klachtensysteem"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://gemeente.nl

# Strong random key (php artisan key:generate)
APP_KEY=base64:RANDOM_32_CHARACTER_STRING

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gemeente_prod
DB_USERNAME=gemeente_user
DB_PASSWORD=STRONG_RANDOM_PASSWORD

# Cache (Redis recommended for production)
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=REDIS_PASSWORD
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=noreply@gemeente.nl
MAIL_PASSWORD=APP_SPECIFIC_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@gemeente.nl
MAIL_FROM_NAME="${APP_NAME}"

# Logging
LOG_CHANNEL=daily
LOG_LEVEL=warning
```

### 2. File Permissions

```bash
# Set correct ownership
sudo chown -R www-data:www-data /var/www/gemeente

# Directories: 755
find /var/www/gemeente -type d -exec chmod 755 {} \;

# Files: 644
find /var/www/gemeente -type f -exec chmod 644 {} \;

# Storage & Bootstrap cache: 775
sudo chmod -R 775 /var/www/gemeente/storage
sudo chmod -R 775 /var/www/gemeente/bootstrap/cache

# .env should be 600 (owner only)
chmod 600 /var/www/gemeente/.env
```

### 3. Hide Sensitive Files

**Add to `.gitignore`:**
```
.env
.env.backup
.phpunit.result.cache
node_modules
vendor
storage/*.key
.DS_Store
Thumbs.db
```

**Nginx: Block access to sensitive files:**
```nginx
location ~ /\.(env|git|svn) {
    deny all;
    return 404;
}

location ~ /(composer\.json|composer\.lock|package\.json) {
    deny all;
    return 404;
}
```

### 4. Database Security

**Create dedicated user:**
```sql
-- Login as root
mysql -u root -p

-- Create database
CREATE DATABASE gemeente_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user with limited privileges
CREATE USER 'gemeente_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD';

-- Grant only necessary privileges
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER 
ON gemeente_prod.* TO 'gemeente_user'@'localhost';

FLUSH PRIVILEGES;
```

### 5. Rate Limiting

**Add to `app/Http/Kernel.php`:**
```php
protected $middlewareGroups = [
    'api' => [
        'throttle:60,1', // 60 requests per minute
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],
];
```

**Custom rate limit for specific routes:**
```php
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/complaints', [ComplaintController::class, 'store']);
});
```

### 6. CORS Configuration

**`config/cors.php`:**
```php
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['GET', 'POST', 'PATCH', 'DELETE'],
    'allowed_origins' => ['https://gemeente.nl'],
    'allowed_headers' => ['Content-Type', 'Authorization'],
    'max_age' => 86400,
];
```

### 7. Security Headers

**Add to Nginx config:**
```nginx
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';" always;
```

---

## ⚡ Performance Optimization

### 1. OpCache Configuration

**`/etc/php/8.3/fpm/php.ini`:**
```ini
[opcache]
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0  ; Disable in production
opcache.save_comments=1
opcache.fast_shutdown=1
```

**Restart PHP-FPM:**
```bash
sudo systemctl restart php8.3-fpm
```

### 2. Database Optimization

**Enable query caching in Laravel:**
```php
// In AppServiceProvider
public function boot()
{
    DB::enableQueryLog();
    
    // Cache frequently accessed data
    Config::set('cache.default', 'redis');
}
```

**Add database indexes:**
```php
// Migration
Schema::table('complaints', function (Blueprint $table) {
    $table->index('status');
    $table->index('category');
    $table->index('created_at');
    $table->index(['latitude', 'longitude']);
});
```

### 3. Redis Caching

**Install Redis:**
```bash
sudo apt install redis-server
sudo systemctl enable redis-server
```

**Laravel config:**
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

**Cache API responses:**
```php
Route::get('/api/complaints', function () {
    return Cache::remember('complaints_list', 300, function () {
        return Complaint::with('photos')->get();
    });
});
```

### 4. Queue Workers

**Setup Supervisor:**
```bash
sudo apt install supervisor
```

**Create config `/etc/supervisor/conf.d/gemeente-worker.conf`:**
```ini
[program:gemeente-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/gemeente/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/gemeente/storage/logs/worker.log
stopwaitsecs=3600
```

**Start workers:**
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start gemeente-worker:*
```

### 5. CDN Integration

**For static assets:**
```php
// config/filesystems.php
'disks' => [
    'cdn' => [
        'driver' => 's3',
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
        'url' => env('AWS_URL'),
    ],
],
```

---

## 📊 Monitoring & Logging

### 1. Laravel Telescope (Development)

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

**Only enable in non-production:**
```php
// app/Providers/TelescopeServiceProvider.php
public function register()
{
    if ($this->app->environment('local')) {
        $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
    }
}
```

### 2. Error Tracking (Sentry)

```bash
composer require sentry/sentry-laravel
php artisan sentry:publish --dsn=YOUR_DSN
```

**`.env`:**
```env
SENTRY_LARAVEL_DSN=https://...@sentry.io/...
```

### 3. Application Monitoring

**Setup Laravel Horizon (for queue monitoring):**
```bash
composer require laravel/horizon
php artisan horizon:install
```

**Access at:** `https://gemeente.nl/horizon`

### 4. Server Monitoring

**Install monitoring tools:**
```bash
# htop (resource monitor)
sudo apt install htop

# netdata (real-time monitoring)
bash <(curl -Ss https://my-netdata.io/kickstart.sh)
```

**Access Netdata:** `http://your-ip:19999`

### 5. Log Rotation

**Create `/etc/logrotate.d/gemeente`:**
```
/var/www/gemeente/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0640 www-data www-data
    sharedscripts
    postrotate
        systemctl reload php8.3-fpm
    endscript
}
```

---

## 💾 Backup Strategy

### 1. Database Backups

**Create backup script `/usr/local/bin/gemeente-backup.sh`:**
```bash
#!/bin/bash

# Configuration
DB_NAME="gemeente_prod"
DB_USER="gemeente_user"
DB_PASS="PASSWORD"
BACKUP_DIR="/var/backups/gemeente"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p $BACKUP_DIR

# MySQL dump
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Keep only last 7 days
find $BACKUP_DIR -name "db_*.sql.gz" -mtime +7 -delete

# Upload to S3 (optional)
# aws s3 cp $BACKUP_DIR/db_$DATE.sql.gz s3://my-bucket/backups/
```

**Make executable:**
```bash
sudo chmod +x /usr/local/bin/gemeente-backup.sh
```

**Add to crontab:**
```bash
sudo crontab -e

# Daily backup at 2 AM
0 2 * * * /usr/local/bin/gemeente-backup.sh
```

### 2. File Backups

**Backup storage folder:**
```bash
#!/bin/bash
tar -czf /var/backups/gemeente/storage_$(date +%Y%m%d).tar.gz \
    /var/www/gemeente/storage/app/public
```

### 3. Automated Backups (Laravel Backup)

```bash
composer require spatie/laravel-backup
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
```

**Configure in `.env`:**
```env
BACKUP_ARCHIVE_PASSWORD=secure_password
BACKUP_MAIL_TO=admin@gemeente.nl
```

**Add to cron:**
```bash
# config/backup.php configured
php artisan schedule:run
```

---

## 🔄 CI/CD Pipeline

### GitHub Actions Workflow

**`.github/workflows/deploy.yml`:**
```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          extensions: mbstring, xml, curl, zip, gd
          
      - name: Install Composer dependencies
        run: composer install --no-dev --optimize-autoloader --no-interaction
        
      - name: Setup Node.js
        uses: actions/setup-node@v3
        with:
          node-version: '18'
          
      - name: Install NPM dependencies
        run: npm ci
        
      - name: Build assets
        run: npm run build
        
      - name: Run tests
        run: php artisan test
        
      - name: Deploy to server
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.SERVER_HOST }}
          username: ${{ secrets.SERVER_USER }}
          key: ${{ secrets.SSH_PRIVATE_KEY }}
          script: |
            cd /var/www/gemeente
            git pull origin main
            composer install --no-dev --optimize-autoloader
            npm install
            npm run build
            php artisan migrate --force
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
            php artisan queue:restart
            php artisan optimize
```

**Add secrets in GitHub:**
- `SERVER_HOST`
- `SERVER_USER`
- `SSH_PRIVATE_KEY`

---

## 🎯 Post-Deployment Checklist

- [ ] Website bereikbaar via HTTPS
- [ ] SSL certificaat geldig
- [ ] Database verbinding werkt
- [ ] File uploads werken
- [ ] Email verzending werkt
- [ ] Cron jobs draaien
- [ ] Queue workers actief
- [ ] Backups worden gemaakt
- [ ] Monitoring actief
- [ ] Error tracking werkt
- [ ] API endpoints responderen
- [ ] Performance acceptabel (<200ms)

---

## 📞 Support & Troubleshooting

### Common Issues

**Permission Errors:**
```bash
sudo chown -R www-data:www-data /var/www/gemeente/storage
sudo chmod -R 775 /var/www/gemeente/storage
```

**Cache Issues:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

**Database Connection:**
```bash
# Test MySQL connection
mysql -u gemeente_user -p gemeente_prod

# Check Laravel connection
php artisan tinker
>>> DB::connection()->getPdo();
```

**Nginx Not Starting:**
```bash
# Check syntax
sudo nginx -t

# Check error log
sudo tail -f /var/log/nginx/error.log
```

---

**🎉 Je applicatie is nu production-ready!**

Voor vragen: check de andere docs of Laravel documentatie: https://laravel.com/docs
