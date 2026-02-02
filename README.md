# Portfolio CMS - Laravel 11 Headless

A production-ready Headless CMS built with **Laravel 11**, **Filament PHP**, and **Spatie Media Library** for powering your professional Nuxt 3 portfolio.

**Tech Stack:**
- Backend: Laravel 11 + Sanctum + Filament 3
- Database: MySQL 8.0 + Redis (cache/queue)
- Media: Spatie Media Library (image handling)
- Frontend: Nuxt 3 (separate repo)
- Hosting: VPS (2vCPU, 4GB RAM)

---

## 📁 Project Structure

```
portfolio-cms/
├── app/
│   ├── Filament/
│   │   └── Resources/              # Admin panel forms
│   │       ├── AboutResource.php
│   │       ├── ExperienceResource.php
│   │       ├── SkillResource.php
│   │       ├── ProjectResource.php
│   │       └── PostResource.php
│   ├── Http/
│   │   └── Controllers/Api/        # REST API endpoints
│   │       ├── AboutController.php
│   │       ├── ExperienceController.php
│   │       ├── SkillController.php
│   │       ├── ProjectController.php
│   │       ├── PostController.php
│   │       └── ContactController.php
│   └── Models/                     # Eloquent models with traits
│       ├── About.php
│       ├── Experience.php
│       ├── Skill.php
│       ├── Project.php
│       └── Post.php
├── database/
│   └── migrations/                 # Schema definitions
│       ├── 2024_01_01_000001_create_abouts_table.php
│       ├── 2024_01_01_000002_create_experiences_table.php
│       ├── 2024_01_01_000003_create_skills_table.php
│       ├── 2024_01_01_000004_create_projects_table.php
│       └── 2024_01_01_000005_create_posts_table.php
├── routes/
│   └── api.php                     # API route definitions
├── config/
│   └── cors.php                    # CORS configuration
├── storage/
│   └── app/media/                  # Media Library storage
├── SETUP.md                        # Installation guide
├── NUXT_INTEGRATION.md            # Nuxt 3 integration examples
└── README.md                       # This file
```

---

## 🚀 Quick Start

### Installation

```bash
# Clone repository
git clone <repo> portfolio-cms
cd portfolio-cms

# Install dependencies
composer install
cp .env.example .env
php artisan key:generate

# Setup database
mysql -u root -p -e "CREATE DATABASE portfolio_cms;"
php artisan migrate

# Create storage symlink
php artisan storage:link

# Create admin user
php artisan tinker
# App\Models\User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password')])
```

### Run Locally

```bash
# Start server
php artisan serve

# In another terminal, start Redis
redis-server

# Visit admin panel
# http://localhost:8000/admin
```

---

## 📊 Core Modules

### 1. About (Single Record)
**Admin Panel:** Manage your professional bio and profile.

**Fields:**
- Full Name
- Title (e.g., "Senior Magento Developer")
- Bio (Markdown)
- Profile Image (via Spatie Media Library)
- CV Link
- Social Links (JSON: twitter, github, linkedin, etc.)

**API Endpoint:** `GET /api/about`

**Response:**
```json
{
  "full_name": "Aivaras",
  "title": "Senior Magento Developer",
  "bio": "# Professional Bio...",
  "profile_image": "https://storage.yourdomain.com/about/profile.jpg",
  "cv_link": "https://...",
  "social_links": {
    "twitter": "https://twitter.com/...",
    "github": "https://github.com/...",
    "linkedin": "https://linkedin.com/in/..."
  }
}
```

---

### 2. Experience (Timeline)
**Admin Panel:** Manage work experience entries with CRUD and ordering.

**Fields:**
- Company Name
- Role
- Period (e.g., "2023.07 - Now")
- Description
- Technologies (JSON array)
- Order (for sorting)

**API Endpoint:** `GET /api/experience`

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "company_name": "TechCorp",
      "role": "Lead Developer",
      "period": "2023.07 - Now",
      "description": "Led GraphQL integration...",
      "technologies": ["Laravel", "GraphQL", "PostgreSQL"],
      "order": 0
    }
  ]
}
```

---

### 3. Skills (Grid with Categories)
**Admin Panel:** Organize skills by category (Magento, GraphQL, Backend, etc.).

**Fields:**
- Category (e.g., "Magento", "GraphQL")
- Icon (SVG path or file upload)
- Description
- Sub Skills (JSON array: badges)
- Order

**API Endpoint:** `GET /api/skills`

**Response:**
```json
{
  "data": {
    "Magento": [
      {
        "id": 1,
        "category": "Magento",
        "icon": "magento-icon.svg",
        "icon_url": "https://storage.yourdomain.com/skills/icon.svg",
        "description": "E-commerce platform expertise",
        "sub_skills": ["Magento 2", "GraphQL", "Extension Development"],
        "order": 0
      }
    ],
    "GraphQL": [...]
  }
}
```

---

### 4. Projects
**Admin Panel:** Showcase portfolio projects with images and tech stack.

**Fields:**
- Title
- Description
- Tech Stack (JSON array)
- Thumbnail (via Spatie)
- GitHub Link
- Live Link
- Order

**API Endpoint:** `GET /api/projects`

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "E-commerce Platform",
      "description": "Built headless Magento 2 store...",
      "thumbnail": "https://storage.yourdomain.com/projects/thumb.jpg",
      "tech_stack": ["Magento 2", "Laravel", "Vue.js", "GraphQL"],
      "github_link": "https://github.com/...",
      "live_link": "https://example.com",
      "order": 0
    }
  ]
}
```

---

### 5. Blog Posts
**Admin Panel:** Publish articles with Markdown editor and scheduling.

**Fields:**
- Title
- Slug (auto-generated)
- Content (Markdown)
- Status (draft, published, archived)
- Published At (timestamp)

**API Endpoints:**
- `GET /api/posts` — List published articles
- `GET /api/posts/{slug}` — Single article

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Scaling GraphQL APIs",
      "slug": "scaling-graphql-apis",
      "content": "# Scaling GraphQL APIs...",
      "published_at": "2024-01-15T10:30:00Z",
      "created_at": "2024-01-15T10:30:00Z"
    }
  ]
}
```

---

### 6. Contact Form
**Admin Panel:** Not stored in database (can be configured).

**Endpoint:** `POST /api/contact` (Rate limited: 3 requests per hour)

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "subject": "Portfolio Inquiry",
  "message": "Your message here..."
}
```

**Response (Success):**
```json
{
  "message": "Message sent successfully!"
}
```

**Response (Rate Limited):**
```json
{
  "message": "Too many contact form submissions. Please try again later."
}
```

---

## 🔧 Configuration

### Environment Variables (`.env`)

```env
# App
APP_URL=https://api.yourdomain.com
FRONTEND_URL=https://yourdomain.com
APP_DEBUG=false

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=portfolio_cms
DB_USERNAME=root
DB_PASSWORD=secret

# Cache & Queue
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_FROM_ADDRESS=noreply@yourdomain.com
ADMIN_EMAIL=your-email@example.com

# CORS
SESSION_DOMAIN=.yourdomain.com
SANCTUM_STATEFUL_DOMAINS=yourdomain.com,*.yourdomain.com
```

### CORS Configuration (`config/cors.php`)

```php
'allowed_origins' => [
    'https://yourdomain.com',  // Your Vercel domain
    'http://localhost:3000',   // Local Nuxt dev
],
```

---

## 📱 API Documentation

All endpoints return JSON and are CORS-enabled.

### Request Headers
```
Content-Type: application/json
Accept: application/json
```

### Response Format
```json
{
  "data": [...],
  "message": "optional"
}
```

### Error Handling
```json
{
  "message": "Error description",
  "error": "...",  // Only in debug mode
  "status": 400
}
```

---

## 🎨 Admin Panel Features

- **Filament 3** — Modern, responsive admin UI
- **Markdown Editor** — For bio and blog posts
- **Image Upload** — Integrated Spatie Media Library
- **Sortable Lists** — Drag-and-drop ordering
- **Validation** — Server-side field validation
- **Status Badges** — Visual indicators for post status
- **Date Pickers** — Publishing schedule control
- **JSON Fields** — Technologies, social links as JSON

---

## 🔒 Security

- **Sanctum** — API token authentication (configurable)
- **CORS** — Whitelist Vercel domains only
- **Rate Limiting** — Contact form limited to 3/hour per IP
- **Validation** — All inputs validated server-side
- **SQL Injection** — Protected via Eloquent ORM
- **CSRF** — CSRF tokens for form submissions

---

## ⚡ Performance

- **Redis Caching** — Cache portfolio data
- **Database Indexes** — On order, status, slug
- **Media Optimization** — Automatic image handling via Spatie
- **Global Scopes** — Automatic ordering in models
- **Query Optimization** — Eager loading in controllers

### Cache Portfolio Data (Nuxt 3)

```typescript
// Fetch once, cache for 1 hour
const { data: about } = await useFetch('/api/about', {
  headers: {
    'Cache-Control': 'public, max-age=3600'
  }
});
```

---

## 🚢 Deployment

### VPS Setup (2vCPU, 4GB RAM)

**Requirements:**
- PHP 8.2+
- MySQL 8.0
- Redis
- Nginx

See **SETUP.md** for full deployment guide including:
- Supervisor configuration for queue workers
- Nginx reverse proxy setup
- SSL/TLS with Let's Encrypt
- Database backups

---

## 📚 Documentation

- **SETUP.md** — Installation & deployment
- **NUXT_INTEGRATION.md** — Nuxt 3 components & composables
- **Filament Docs** — https://filamentphp.com
- **Laravel Docs** — https://laravel.com/docs
- **Spatie Media Library** — https://spatie.be/docs/laravel-media-library

---

## 🔄 Development Workflow

### Local Setup

```bash
# Terminal 1: Laravel server
php artisan serve --host=localhost --port=8000

# Terminal 2: Redis
redis-server

# Terminal 3: Queue worker (optional)
php artisan queue:work
```

### Migrations

```bash
# Create new migration
php artisan make:migration create_table_name

# Run migrations
php artisan migrate

# Rollback
php artisan migrate:rollback
```

### Tinker (REPL)

```bash
php artisan tinker

# Create test data
App\Models\About::create([
  'full_name' => 'Your Name',
  'title' => 'Title',
  'bio' => '# Bio'
]);
```

---

## 🐛 Troubleshooting

### Storage Link
```bash
rm public/storage
php artisan storage:link
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:cache
php artisan route:cache
```

### Database Issues
```bash
php artisan migrate:refresh --seed  # Warning: Deletes data!
```

---

## 📝 License

MIT — Feel free to fork, modify, and use for your portfolio.

---

## 🤝 Support

Questions about the code? Check:
- `/app/Filament/Resources/` — Admin forms
- `/app/Http/Controllers/Api/` — API logic
- `/app/Models/` — Data structure
- **SETUP.md** — Installation
- **NUXT_INTEGRATION.md** — Frontend examples

---

**Built with Laravel 11, Filament 3, Spatie Media Library** ⚙️
