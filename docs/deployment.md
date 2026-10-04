# OneStop POS + ERP Production Deployment Guide

This guide outlines deployment procedures for production environments (Linux/Nginx/MySQL/PHP 8.2+ or containerized Docker).

---

## 1. System Requirements
- **PHP**: 8.2 or higher
- **PHP Extensions**: `BCMath`, `Ctype`, `cURL`, `DOM`, `Fileinfo`, `JSON`, `Mbstring`, `OpenSSL`, `PCRE`, `PDO`, `PDO_MySQL`, `Tokenizer`, `XML`
- **Database**: MySQL 8.0+ or MariaDB 10.5+
- **Web Server**: Nginx or Apache
- **Node.js & NPM**: Node 18+ (for asset bundling)
- **Composer**: Composer 2.x

---

## 2. Environment Configuration
Copy `.env.example` to `.env` and configure production settings:

```ini
APP_NAME=OneStop
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://pos.onestop.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=onestop_erp
DB_USERNAME=onestop_user
DB_PASSWORD=your_secure_password

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

TIMEZONE=Asia/Dhaka
CURRENCY=BDT
```

---

## 3. Installation & Build Steps

```bash
# 1. Clone repository
git clone <repo_url> /var/www/onestop
cd /var/www/onestop

# 2. Install PHP dependencies without dev packages
composer install --no-dev --optimize-autoloader

# 3. Generate App Key if not present
php artisan key:generate --force

# 4. Run Migrations and Seeders
php artisan migrate --force
php artisan db:seed --force

# 5. Build Tailwind & Frontend Assets
npm ci
npm run build

# 6. Optimize Laravel Cache & Configs
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 7. Set Permissions
chown -R www-data:www-data /var/www/onestop
chmod -R 775 /var/www/onestop/storage /var/www/onestop/bootstrap/cache
```

---

## 4. Nginx Configuration
Sample Nginx server block:

```nginx
server {
    listen 80;
    server_name pos.onestop.com;
    root /var/www/onestop/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 5. Security & Maintenance Checklist
- Ensure `APP_DEBUG=false` in production.
- Configure daily MySQL backups (e.g. `mysqldump`).
- Set timezone in `config/app.php` to `'Asia/Dhaka'`.
- Force HTTPS using TLS/SSL (Let's Encrypt / Certbot).
