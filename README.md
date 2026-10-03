# Lernyxa LMS

> **Learn. Build. Progress.**

A cohort-aware, enterprise-grade Learning Management Platform by [Sardauna Tech Lab Ltd](https://sardaunatechlab.com).

---

## 🏗️ Tech Stack

| Layer | Technology |
|-------|-----------|
| **Frontend** | Next.js 14+ (App Router), TypeScript, Tailwind CSS, shadcn/ui |
| **Backend API** | Laravel 11+, PHP 8.3+, Laravel Sanctum |
| **Database** | PostgreSQL 16 (Supabase managed in production) |
| **Cache/Queue** | Redis 7 |
| **Object Storage** | S3-compatible (MinIO local / Supabase Storage production) |
| **Email** | Resend (Mailpit local) |
| **Video** | Zoom (Meeting SDK + REST API) |
| **PDF/QR** | Spatie Laravel PDF + Simple QR Code |

## 📁 Project Structure

```
Learnyxa LMS/
├── backend/              # Laravel 11 API
│   ├── app/
│   ├── database/
│   ├── routes/
│   └── tests/
├── frontend/             # Next.js 14+ SPA
│   ├── src/
│   │   ├── app/          # App Router pages
│   │   ├── components/   # Reusable UI components
│   │   ├── lib/          # Utilities & API client
│   │   └── styles/       # Global styles & design tokens
│   └── public/
├── docker/               # Docker configuration
│   ├── nginx/            # Nginx reverse proxy config
│   └── php/              # PHP-FPM Dockerfile
├── docker-compose.yml    # All development services
├── .env.example          # Environment template
└── README.md
```

## 🚀 Getting Started

### Prerequisites
- Docker & Docker Compose
- Node.js 20+ (for local frontend dev)
- PHP 8.3+ & Composer (for local backend dev)

### Quick Start (Docker)

```bash
# 1. Clone the repository
git clone <repo-url> && cd "Learnyxa LMS"

# 2. Copy environment file
cp .env.example backend/.env

# 3. Start all services
docker-compose up -d

# 4. Install backend dependencies & setup
docker exec lernyxa-api composer install
docker exec lernyxa-api php artisan key:generate
docker exec lernyxa-api php artisan migrate --seed

# 5. Access the application
# Frontend:  http://localhost:3000
# API:       http://localhost:8000
# Mailpit:   http://localhost:8025
# MinIO:     http://localhost:9001
```

### Local Development (Without Docker)

```bash
# Backend
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan serve --port=8000

# Frontend
cd frontend
npm install
npm run dev
```

## 🔐 Default Roles

| Role | Access Level |
|------|-------------|
| `super_admin` | Global configuration & security |
| `admin` | Platform operations & governance |
| `program_manager` | Programs, cohorts & reporting |
| `facilitator` | Content, assessments & grading |
| `mentor` | Q&A support & learner guidance |
| `student` | Learning, submissions & progress |

## 🧪 Testing

```bash
# Backend (PHPUnit)
cd backend && php artisan test

# Frontend (Vitest)
cd frontend && npm run test

# E2E (Playwright)
cd frontend && npx playwright test
```

## 📄 Documentation

- [Product Requirements (PRD)](./Lernyxa_Product_Requirements_Document_PRD.pdf)
- [Technical Requirements (TRD)](./Lernyxa_Technical_Requirements_Document_TRD.pdf)
- [System Architecture & ERD](./Lernyxa_System_Architecture_and_Database_ERD_v1.0.pdf)

---

**Lernyxa** © 2026 Sardauna Tech Lab Ltd. All rights reserved.
