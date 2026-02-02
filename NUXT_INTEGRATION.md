# Nuxt 3 Integration Guide

How to consume the Portfolio CMS API in your Nuxt 3 frontend.

## Setup

### 1. Environment Variables

In your Nuxt 3 project's `.env`:

```env
VITE_API_BASE_URL=https://api.yourdomain.com
```

### 2. Configure Fetch in `nuxt.config.ts`

```typescript
export default defineNuxtConfig({
  nitro: {
    prerender: {
      crawlLinks: true,
      ignore: ['/admin']
    }
  },
  runtimeConfig: {
    public: {
      apiBase: process.env.VITE_API_BASE_URL || 'http://localhost:8000',
    }
  }
})
```

## Composables

### `composables/usePortfolioApi.ts`

```typescript
export const usePortfolioApi = () => {
  const config = useRuntimeConfig();
  const apiBase = config.public.apiBase;

  return {
    getAbout: () => $fetch(`${apiBase}/api/about`),
    getExperience: () => $fetch(`${apiBase}/api/experience`),
    getSkills: () => $fetch(`${apiBase}/api/skills`),
    getProjects: () => $fetch(`${apiBase}/api/projects`),
    getPosts: () => $fetch(`${apiBase}/api/posts`),
    getPost: (slug: string) => $fetch(`${apiBase}/api/posts/${slug}`),
    sendContact: (data: {
      name: string;
      email: string;
      subject: string;
      message: string;
    }) => $fetch(`${apiBase}/api/contact`, {
      method: 'POST',
      body: data,
    }),
  };
};
```

## Components

### Hero Section with About Data

```vue
<template>
  <section class="hero">
    <div v-if="about" class="container">
      <img :src="about.profile_image" :alt="about.full_name" class="avatar" />
      <h1>{{ about.full_name }}</h1>
      <p class="title">{{ about.title }}</p>
      <div class="bio" v-html="marked(about.bio)"></div>
      
      <div class="social-links">
        <a
          v-for="(url, platform) in about.social_links"
          :key="platform"
          :href="url"
          target="_blank"
          rel="noopener"
          class="social-link"
        >
          {{ platform }}
        </a>
      </div>
      
      <a v-if="about.cv_link" :href="about.cv_link" class="btn">Download CV</a>
    </div>
  </section>
</template>

<script setup lang="ts">
import { marked } from 'marked';

const api = usePortfolioApi();
const about = ref(null);

onMounted(async () => {
  about.value = await api.getAbout();
});
</script>

<style scoped>
.hero {
  padding: 4rem 1rem;
  text-align: center;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
}

.avatar {
  width: 180px;
  height: 180px;
  border-radius: 50%;
  margin-bottom: 2rem;
  border: 4px solid #60a5fa;
}

h1 {
  font-size: 3rem;
  font-weight: bold;
  margin-bottom: 0.5rem;
}

.title {
  font-size: 1.5rem;
  color: #94a3b8;
  margin-bottom: 2rem;
}

.bio {
  max-width: 600px;
  margin: 0 auto 2rem;
  line-height: 1.8;
}

.social-links {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-bottom: 2rem;
}

.social-link {
  color: #60a5fa;
  text-decoration: none;
  transition: color 0.3s;
}

.social-link:hover {
  color: #3b82f6;
}

.btn {
  display: inline-block;
  padding: 0.75rem 2rem;
  background: #3b82f6;
  color: white;
  border-radius: 0.5rem;
  text-decoration: none;
  transition: background 0.3s;
}

.btn:hover {
  background: #2563eb;
}
</style>
```

### Experience Timeline

```vue
<template>
  <section class="experience">
    <h2>Experience</h2>
    
    <div v-if="experiences" class="timeline">
      <div
        v-for="exp in experiences"
        :key="exp.id"
        class="timeline-item"
      >
        <div class="timeline-marker"></div>
        <div class="timeline-content">
          <h3>{{ exp.role }}</h3>
          <p class="company">{{ exp.company_name }}</p>
          <p class="period">{{ exp.period }}</p>
          <p class="description">{{ exp.description }}</p>
          
          <div class="tech-badges">
            <span
              v-for="tech in exp.technologies"
              :key="tech"
              class="badge"
            >
              {{ tech }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const api = usePortfolioApi();
const experiences = ref(null);

onMounted(async () => {
  const res = await api.getExperience();
  experiences.value = res.data;
});
</script>

<style scoped>
.experience {
  padding: 4rem 2rem;
}

h2 {
  font-size: 2rem;
  margin-bottom: 3rem;
}

.timeline {
  position: relative;
  padding-left: 40px;
}

.timeline::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 2px;
  background: #60a5fa;
}

.timeline-item {
  position: relative;
  margin-bottom: 2rem;
}

.timeline-marker {
  position: absolute;
  left: -14px;
  top: 0;
  width: 12px;
  height: 12px;
  background: #3b82f6;
  border: 2px solid white;
  border-radius: 50%;
}

.timeline-content {
  background: #f8fafc;
  padding: 1.5rem;
  border-radius: 0.5rem;
}

h3 {
  font-size: 1.25rem;
  margin-bottom: 0.25rem;
}

.company {
  color: #60a5fa;
  font-weight: 500;
  margin-bottom: 0.25rem;
}

.period {
  color: #94a3b8;
  font-size: 0.875rem;
  margin-bottom: 1rem;
}

.description {
  margin-bottom: 1rem;
  line-height: 1.6;
}

.tech-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  background: #e0e7ff;
  color: #3730a3;
  border-radius: 9999px;
  font-size: 0.875rem;
}
</style>
```

### Skills Grid

```vue
<template>
  <section class="skills">
    <h2>Skills</h2>
    
    <div v-if="skillsByCategory" class="skills-container">
      <div
        v-for="(categorySkills, category) in skillsByCategory"
        :key="category"
        class="skill-group"
      >
        <h3>{{ category }}</h3>
        <div class="skill-cards">
          <div
            v-for="skill in categorySkills"
            :key="skill.id"
            class="skill-card"
          >
            <div class="skill-icon" v-if="skill.icon_url">
              <img :src="skill.icon_url" :alt="category" />
            </div>
            <h4>{{ skill.category }}</h4>
            <p>{{ skill.description }}</p>
            <div class="sub-skills">
              <span
                v-for="sub in skill.sub_skills"
                :key="sub"
                class="sub-skill"
              >
                {{ sub }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const api = usePortfolioApi();
const skillsByCategory = ref(null);

onMounted(async () => {
  const res = await api.getSkills();
  skillsByCategory.value = res.data;
});
</script>

<style scoped>
.skills {
  padding: 4rem 2rem;
  background: #f8fafc;
}

h2 {
  font-size: 2rem;
  margin-bottom: 3rem;
  text-align: center;
}

.skills-container {
  max-width: 1200px;
  margin: 0 auto;
}

.skill-group {
  margin-bottom: 3rem;
}

.skill-group h3 {
  font-size: 1.5rem;
  margin-bottom: 1.5rem;
  color: #3b82f6;
}

.skill-cards {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
}

.skill-card {
  background: white;
  padding: 2rem;
  border-radius: 0.75rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s, box-shadow 0.3s;
}

.skill-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.skill-icon {
  width: 60px;
  height: 60px;
  margin-bottom: 1rem;
}

.skill-icon img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

h4 {
  font-size: 1.1rem;
  margin-bottom: 0.5rem;
}

.skill-card p {
  color: #64748b;
  margin-bottom: 1rem;
  font-size: 0.95rem;
  line-height: 1.6;
}

.sub-skills {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.sub-skill {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  background: #dbeafe;
  color: #0c4a6e;
  border-radius: 9999px;
  font-size: 0.8rem;
}
</style>
```

### Projects Showcase

```vue
<template>
  <section class="projects">
    <h2>Featured Projects</h2>
    
    <div v-if="projects" class="projects-grid">
      <div
        v-for="project in projects"
        :key="project.id"
        class="project-card"
      >
        <div class="project-image" v-if="project.thumbnail">
          <img :src="project.thumbnail" :alt="project.title" />
        </div>
        
        <div class="project-content">
          <h3>{{ project.title }}</h3>
          <p>{{ project.description }}</p>
          
          <div class="tech-stack">
            <span
              v-for="tech in project.tech_stack"
              :key="tech"
              class="tech"
            >
              {{ tech }}
            </span>
          </div>
          
          <div class="project-links">
            <a
              v-if="project.github_link"
              :href="project.github_link"
              target="_blank"
              class="link github"
            >
              GitHub
            </a>
            <a
              v-if="project.live_link"
              :href="project.live_link"
              target="_blank"
              class="link live"
            >
              Live Demo
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const api = usePortfolioApi();
const projects = ref(null);

onMounted(async () => {
  const res = await api.getProjects();
  projects.value = res.data;
});
</script>

<style scoped>
.projects {
  padding: 4rem 2rem;
}

h2 {
  font-size: 2rem;
  margin-bottom: 3rem;
  text-align: center;
}

.projects-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.project-card {
  background: white;
  border-radius: 0.75rem;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s;
}

.project-card:hover {
  transform: translateY(-8px);
}

.project-image {
  width: 100%;
  height: 200px;
  overflow: hidden;
  background: #e2e8f0;
}

.project-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.project-content {
  padding: 1.5rem;
}

h3 {
  font-size: 1.25rem;
  margin-bottom: 0.75rem;
}

.project-content p {
  color: #64748b;
  margin-bottom: 1.5rem;
  line-height: 1.6;
}

.tech-stack {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.tech {
  display: inline-block;
  padding: 0.3rem 0.75rem;
  background: #f1f5f9;
  color: #475569;
  border-radius: 9999px;
  font-size: 0.8rem;
}

.project-links {
  display: flex;
  gap: 1rem;
}

.link {
  display: inline-block;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  text-decoration: none;
  font-size: 0.9rem;
  transition: background 0.3s;
}

.link.github {
  background: #1f2937;
  color: white;
}

.link.github:hover {
  background: #111827;
}

.link.live {
  background: #3b82f6;
  color: white;
}

.link.live:hover {
  background: #2563eb;
}
</style>
```

### Blog Posts List

```vue
<template>
  <section class="blog">
    <h2>Latest Articles</h2>
    
    <div v-if="posts" class="posts-list">
      <article
        v-for="post in posts"
        :key="post.id"
        class="post-card"
      >
        <NuxtLink :to="`/blog/${post.slug}`" class="post-title">
          <h3>{{ post.title }}</h3>
        </NuxtLink>
        
        <p class="post-date">
          {{ new Date(post.published_at).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
          }) }}
        </p>
        
        <p class="post-excerpt">{{ post.content.substring(0, 150) }}...</p>
        
        <NuxtLink :to="`/blog/${post.slug}`" class="read-more">
          Read More →
        </NuxtLink>
      </article>
    </div>
  </section>
</template>

<script setup lang="ts">
const api = usePortfolioApi();
const posts = ref(null);

onMounted(async () => {
  const res = await api.getPosts();
  posts.value = res.data;
});
</script>

<style scoped>
.blog {
  padding: 4rem 2rem;
  background: #f8fafc;
}

h2 {
  font-size: 2rem;
  margin-bottom: 3rem;
  text-align: center;
}

.posts-list {
  max-width: 700px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.post-card {
  background: white;
  padding: 2rem;
  border-radius: 0.75rem;
  border-left: 4px solid #3b82f6;
  transition: box-shadow 0.3s;
}

.post-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.post-title {
  text-decoration: none;
  color: inherit;
}

h3 {
  font-size: 1.5rem;
  margin-bottom: 0.75rem;
  transition: color 0.3s;
}

.post-title:hover h3 {
  color: #3b82f6;
}

.post-date {
  color: #94a3b8;
  font-size: 0.9rem;
  margin-bottom: 1rem;
}

.post-excerpt {
  color: #64748b;
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.read-more {
  color: #3b82f6;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s;
}

.read-more:hover {
  color: #2563eb;
}
</style>
```

## SEO

Use Nuxt 3's `useHead()` to optimize meta tags:

```typescript
useHead({
  title: about.value?.full_name + ' | Portfolio',
  meta: [
    {
      name: 'description',
      content: about.value?.bio?.substring(0, 160)
    }
  ]
})
```

## Error Handling

```typescript
try {
  const data = await api.getAbout();
  about.value = data;
} catch (error) {
  console.error('Failed to load portfolio data:', error);
  // Show error state to user
}
```

---

**Ready to build your portfolio frontend!**
