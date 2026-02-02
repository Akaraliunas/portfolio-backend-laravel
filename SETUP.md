# Portfolio CMS - Laravel 11 Setup Guide

Complete Headless CMS for your Nuxt 3 portfolio with Filament admin panel.

## Prerequisites

- PHP 8.2+
- MySQL 8.0+
- Redis (for caching & queue)
- Composer
- Node.js (for frontend, separate repo)

## Installation

### 1. Clone & Dependencies

```bash
git clone <your-repo> portfolio-cms
cd portfolio-cms

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate
```

### 2. Database Setup

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE portfolio_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate

# Seed initial data (optional)
php artisan db:seed
```

### 3. Storage & Media Library

```bash
# Create storage symlink for public media access
php artisan storage:link

# Create media library directories
mkdir -p storage/app/media
chmod -R 755 storage/app
```

### 4. Environment Configuration

Edit `.env` with your settings:

```env
APP_URL=https://api.yourdomain.com
FRONTEND_URL=https://yourdomain.com
DB_DATABASE=portfolio_cms
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

MAIL_FROM_ADDRESS=noreply@yourdomain.com
ADMIN_EMAIL=your-email@example.com
```

### 5. Create Admin User

```bash
# Create first admin user via Filament command or directly:
php artisan tinker

# In tinker shell:
# App\Models\User::create([
#     'name' => 'Your Name',
#     'email' => 'admin@example.com',
#     'password' => bcrypt('password'),
# ]);
```

### 6. Access Admin Panel

Navigate to: `https://api.yourdomain.com/admin`

Login with your credentials.

## Deployment (VPS - 2vCPU, 4GB RAM)

### Using Supervisor + Nginx

```bash
# Install supervisor for queue jobs
sudo apt-get install supervisor

# Create supervisor config
sudo nano /etc/supervisor/conf.d/laravel-worker.conf

[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/portfolio-cms/artisan queue:work redis --sleep=3 --tries=3 --timeout=90
autostart=true
autorestart=true
stderr_logfile=/var/log/laravel-worker.log
stdout_logfile=/var/log/laravel-worker.log

# Restart supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

### Nginx Configuration

```nginx
server {
    listen 443 ssl http2;
    server_name api.yourdomain.com;

    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;

    root /var/www/portfolio-cms/public;
    index index.php;

    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "SAMEORIGIN" always;

    client_max_body_size 50M;

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

## API Endpoints (for Nuxt 3)

### Portfolio Data

```
GET  /api/about           → Returns bio, profile image, links
GET  /api/experience      → Returns timeline experiences
GET  /api/skills          → Returns skills grouped by category
GET  /api/projects        → Returns all projects
GET  /api/posts           → Returns published blog posts
GET  /api/posts/{slug}    → Returns single blog post
POST /api/contact         → Send contact message (rate limited)
```

### Example Nuxt 3 Usage

```typescript
// composables/usePortfolio.ts
export const useAbout = async () => {
  const { data } = await useFetch('/api/about');
  return data.value;
};

export const useExperience = async () => {
  const { data } = await useFetch('/api/experience');
  return data.value?.data || [];
};

export const useSkills = async () => {
  const { data } = await useFetch('/api/skills');
  return data.value?.data || {};
};
```

## Performance Tips

### Redis Caching

```bash
# Clear cache
php artisan cache:clear

# Cache config
php artisan config:cache

# Cache routes
php artisan route:cache
```

### Database Optimization

```sql
-- Indexes are created in migrations, verify:
SHOW INDEX FROM about;
SHOW INDEX FROM experiences;
SHOW INDEX FROM skills;
SHOW INDEX FROM projects;
SHOW INDEX FROM posts;
```

### Image Optimization

Images uploaded via Filament are stored in `storage/app/media/`. Configure CDN for static assets:

```env
APP_URL=https://api.yourdomain.com
MEDIA_DISK=public
```

## Security

- **Sanctum**: Configured for SPA authentication (if needed)
- **CORS**: Allowed origins configured in `config/cors.php`
- **Rate Limiting**: Contact form limited to 3 requests per hour per IP
- **Validation**: All API inputs are validated server-side

## Troubleshooting

### Storage Link Issues

```bash
# Remove and recreate symlink
rm public/storage
php artisan storage:link
```

### Redis Connection

```bash
# Test Redis connection
php artisan tinker
Redis::ping()  # Should return "PONG"
```

### Queue Jobs Not Processing

```bash
# Check supervisor status
sudo supervisorctl status

# Monitor logs
tail -f /var/log/laravel-worker.log
```

## Maintenance

### Backup Database

```bash
# Weekly backup
mysqldump -u root -p portfolio_cms > backups/portfolio_cms_$(date +%Y%m%d).sql
```

### Clear Old Sessions

```bash
php artisan session:table
php artisan migrate
```

## Support

For issues or questions about the CMS structure, check:
- `/app/Filament/Resources/` for admin forms
- `/app/Http/Controllers/Api/` for API endpoints
- `/app/Models/` for data models

---

**Built with Laravel 11, Filament 3, Spatie Media Library**
