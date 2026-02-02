# Database Schema Reference

Complete schema documentation for the Portfolio CMS database.

## Tables Overview

```
┌──────────────┐
│ abouts       │
├──────────────┤
│ id (PK)      │
│ full_name    │
│ title        │
│ bio          │
│ social_links │
│ cv_link      │
│ timestamps   │
└──────────────┘

┌─────────────────┐
│ experiences     │
├─────────────────┤
│ id (PK)         │
│ company_name    │
│ role            │
│ period          │
│ description     │
│ technologies    │
│ order           │
│ timestamps      │
└─────────────────┘

┌────────────────┐
│ skills         │
├────────────────┤
│ id (PK)        │
│ category       │
│ icon           │
│ description    │
│ sub_skills     │
│ order          │
│ timestamps     │
└────────────────┘

┌──────────────────┐
│ projects         │
├──────────────────┤
│ id (PK)          │
│ title            │
│ description      │
│ tech_stack       │
│ github_link      │
│ live_link        │
│ order            │
│ timestamps       │
└──────────────────┘

┌──────────────────┐
│ posts            │
├──────────────────┤
│ id (PK)          │
│ title            │
│ slug (UNIQUE)    │
│ content          │
│ status           │
│ published_at     │
│ timestamps       │
└──────────────────┘

┌──────────────────┐
│ media            │ (Spatie Library)
├──────────────────┤
│ id (PK)          │
│ model_type       │
│ model_id (FK)    │
│ collection_name  │
│ name             │
│ file_name        │
│ mime_type        │
│ disk             │
│ size             │
│ timestamps       │
└──────────────────┘
```

---

## Detailed Schema

### Table: `abouts`

Single-record table storing the bio and profile information.

```sql
CREATE TABLE `abouts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `full_name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `bio` longtext NOT NULL COMMENT 'Markdown content',
  `social_links` json COMMENT '{"twitter": "...", "github": "...", ...}',
  `cv_link` varchar(255) NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Indexes:**
- PRIMARY KEY: `id`

**JSON Structure (social_links):**
```json
{
  "twitter": "https://twitter.com/username",
  "github": "https://github.com/username",
  "linkedin": "https://linkedin.com/in/username",
  "email": "contact@example.com"
}
```

**Use Cases:**
- Display hero section with profile
- Generate navigation links
- SEO meta tags

---

### Table: `experiences`

Timeline of work experience entries.

```sql
CREATE TABLE `experiences` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_name` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `period` varchar(255) NOT NULL COMMENT 'e.g., "2023.07 - Now"',
  `description` text NOT NULL,
  `technologies` json NOT NULL COMMENT '["Laravel", "GraphQL", ...]',
  `order` int NOT NULL DEFAULT 0,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  KEY `idx_order` (`order`)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Indexes:**
- PRIMARY KEY: `id`
- INDEX: `order` (for sorting)

**JSON Structure (technologies):**
```json
[
  "Laravel 11",
  "GraphQL",
  "PostgreSQL",
  "Vue.js 3",
  "Docker"
]
```

**Order Field:**
- Lower numbers appear first in timeline
- Allows custom chronological ordering (newest first, etc.)

**Query Examples:**
```sql
-- Get all experiences sorted by order
SELECT * FROM experiences ORDER BY `order` ASC;

-- Get latest experience (highest order)
SELECT * FROM experiences ORDER BY `order` DESC LIMIT 1;
```

---

### Table: `skills`

Skills organized by category with sub-skills as badges.

```sql
CREATE TABLE `skills` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `category` varchar(255) NOT NULL COMMENT 'Magento, GraphQL, Backend, ...',
  `icon` varchar(255) NULL COMMENT 'SVG path or icon identifier',
  `description` text NOT NULL,
  `sub_skills` json NOT NULL COMMENT '["Apollo", "Schema Design", ...]',
  `order` int NOT NULL DEFAULT 0,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  KEY `idx_category_order` (`category`, `order`)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Indexes:**
- PRIMARY KEY: `id`
- COMPOSITE INDEX: `category, order` (for grouped queries)

**JSON Structure (sub_skills):**
```json
[
  "Apollo Client",
  "Schema Design",
  "Federation",
  "Performance Optimization"
]
```

**Sample Data:**
```sql
INSERT INTO skills VALUES
(1, 'Magento', NULL, 'E-commerce expertise', '["Magento 2", "Extension Dev", "GraphQL"]', 0),
(2, 'Backend', NULL, 'Server-side development', '["Laravel", "Node.js", "Python"]', 0),
(3, 'Frontend', NULL, 'UI/UX development', '["Vue.js", "React", "Nuxt"]', 0);
```

**Query Examples:**
```sql
-- Get all skills grouped by category
SELECT category, COUNT(*) FROM skills GROUP BY category;

-- Get all sub-skills from a category
SELECT sub_skills FROM skills WHERE category = 'Magento';
```

---

### Table: `projects`

Portfolio projects showcase.

```sql
CREATE TABLE `projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `tech_stack` json NOT NULL COMMENT '["Laravel", "Vue.js", ...]',
  `github_link` varchar(255) NULL,
  `live_link` varchar(255) NULL,
  `order` int NOT NULL DEFAULT 0,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  KEY `idx_order` (`order`)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Indexes:**
- PRIMARY KEY: `id`
- INDEX: `order` (for sorting)

**JSON Structure (tech_stack):**
```json
[
  "Laravel 11",
  "Vue.js 3",
  "GraphQL",
  "MySQL",
  "Docker"
]
```

**Media Library Integration:**
- Collection: `thumbnail` (single image)
- Storage: `public/media/projects/{id}/`
- Access: via Spatie `getFirstMediaUrl('thumbnail')`

**Query Examples:**
```sql
-- Get all projects ordered by custom order
SELECT * FROM projects ORDER BY `order` ASC;

-- Get project with specific GitHub link
SELECT * FROM projects WHERE github_link LIKE '%github.com%';
```

---

### Table: `posts`

Blog posts with Markdown content.

```sql
CREATE TABLE `posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL UNIQUE,
  `content` longtext NOT NULL COMMENT 'Markdown content',
  `status` enum('draft', 'published', 'archived') DEFAULT 'draft',
  `published_at` timestamp NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  KEY `idx_status_published` (`status`, `published_at`)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Indexes:**
- PRIMARY KEY: `id`
- UNIQUE INDEX: `slug` (for URL-friendly routing)
- COMPOSITE INDEX: `status, published_at` (for filtering)

**Status Values:**
- `draft` — Not visible on frontend
- `published` — Visible, has published_at timestamp
- `archived` — Visible but marked as old

**Query Examples:**
```sql
-- Get all published posts ordered by date
SELECT * FROM posts 
WHERE status = 'published' AND published_at IS NOT NULL
ORDER BY published_at DESC;

-- Get post by slug
SELECT * FROM posts WHERE slug = 'scaling-graphql-apis';

-- Count published posts per month
SELECT YEAR(published_at), MONTH(published_at), COUNT(*)
FROM posts
WHERE status = 'published'
GROUP BY YEAR(published_at), MONTH(published_at);
```

**Slug Generation (Nuxt 3):**
```php
// In Post model, use Spatie Sluggable:
class Post extends Model {
    use \Spatie\Sluggable\HasSlug;
    
    public function getSlugOptions(): SlugOptions {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }
}
```

---

### Table: `media` (Spatie Media Library)

Manages all uploaded media files.

```sql
CREATE TABLE `media` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `uuid` char(36) NULL UNIQUE,
  `collection_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) NULL,
  `disk` varchar(255) NOT NULL,
  `conversions_disk` varchar(255) NULL,
  `size` bigint unsigned NOT NULL,
  `manipulations` json,
  `custom_properties` json,
  `responsive_images` json,
  `order_column` int unsigned NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  UNIQUE KEY `uuid` (`uuid`),
  KEY `model_type_id` (`model_type`, `model_id`),
  KEY `collection_name` (`collection_name`)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Collections Used in CMS:**
- `profile_image` (About model, single file)
- `icon` (Skill model, single file)
- `thumbnail` (Project model, single file)

**Disk:** `public` (files accessible via public/storage/)

**Storage Paths:**
```
storage/app/media/abouts/1/profile_image.jpg
storage/app/media/skills/1/icon.svg
storage/app/media/projects/1/thumbnail.jpg
```

**Access in API:**
```php
// Get media URL
$about->getFirstMediaUrl('profile_image');
// Output: https://yourdomain.com/storage/media/abouts/1/profile_image.jpg
```

---

## Data Relationships

```
About (1)
  └── has many Media (profile_image)
  └── contains JSON social_links

Experience (many)
  └── has many (order)
  └── contains JSON technologies

Skill (many)
  └── has many Media (icon)
  └── has many (order)
  └── contains JSON sub_skills

Project (many)
  └── has many Media (thumbnail)
  └── has many (order)
  └── contains JSON tech_stack

Post (many)
  └── has Markdown content
  └── status: draft|published|archived
  └── order by published_at
```

---

## Query Performance Tips

### Indexes Present
```sql
-- View all indexes
SHOW INDEX FROM experiences;
SHOW INDEX FROM skills;
SHOW INDEX FROM projects;
SHOW INDEX FROM posts;
```

### Common Queries

**Get all published content for frontend:**
```sql
SELECT * FROM about LIMIT 1;
SELECT * FROM experiences ORDER BY `order` ASC;
SELECT * FROM skills ORDER BY category, `order` ASC;
SELECT * FROM projects ORDER BY `order` ASC;
SELECT * FROM posts 
WHERE status = 'published' AND published_at IS NOT NULL
ORDER BY published_at DESC;
```

**Optimize with EXPLAIN:**
```sql
EXPLAIN SELECT * FROM posts WHERE status = 'published' AND published_at IS NOT NULL;
```

---

## JSON Fields Usage

All JSON fields are indexed (MySQL 5.7+), enabling efficient searches:

```sql
-- Find all posts by specific tech
SELECT * FROM projects 
WHERE JSON_CONTAINS(tech_stack, '"Laravel"');

-- Extract tech stack as column
SELECT id, JSON_EXTRACT(tech_stack, '$[0]') AS first_tech
FROM projects;
```

---

## Backup & Recovery

### Backup Schema Only
```bash
mysqldump -u root -p --no-data portfolio_cms > schema.sql
```

### Backup with Data
```bash
mysqldump -u root -p portfolio_cms > backup.sql
```

### Restore
```bash
mysql -u root -p portfolio_cms < backup.sql
```

---

## Scaling Considerations

**Current Setup (2vCPU, 4GB RAM):**
- Suitable for ~50k visitors/month
- ~1k blog posts
- Images via CDN recommended

**For Growth:**
1. **Add READ REPLICAS** — Separate read/write databases
2. **Cache Layer** — Redis for query results
3. **CDN** — CloudFront/Cloudflare for media
4. **Full-text Search** — Elasticsearch for posts
5. **Partitioning** — Archive old posts to separate tables

---

## Database Maintenance

### Weekly
```bash
# Optimize all tables
OPTIMIZE TABLE abouts, experiences, skills, projects, posts, media;

# Check for errors
CHECK TABLE abouts, experiences, skills, projects, posts, media;
```

### Monthly
```bash
# Analyze table statistics
ANALYZE TABLE abouts, experiences, skills, projects, posts, media;

# Backup
mysqldump -u root -p portfolio_cms > monthly_backup.sql
```

---

**Schema built for performance, clarity, and scalability.** ⚙️
