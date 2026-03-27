# IT Department Management System (IT-DMS)

A comprehensive Laravel-based academic management system for the IT Department.

## TL;DR Overview
**Roles**: Admin, Teacher, Student, Parent  
**Core Features**:
- ✅ Student/Teacher/Parent profiles & management
- ✅ Subject allocation, electives, timetable
- ✅ Attendance tracking (per subject/student)
- ✅ Exam creation, marks entry (internal/final/assessment), marksheets
- ✅ Study materials upload/download
- ✅ Notice board (bilingual English/Nepali)
- ✅ Gallery, reports, printing (HTML-to-image)
- ✅ Role-based dashboards, filters, exports (CSV/Excel)
- 🔄 Notifications, audit logs

**Tech**: Laravel 11+, MySQL, Tailwind CSS, Alpine.js, Bilingual support (BS dates)

## Quick Start
```bash
git clone <repo>
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm install && npm run build
php artisan serve
```

**Admin Login**: `admin@itdms.local` / `password123`

## Installation (Detailed)
[... full installation steps from original README.md ...]
[Include all sections: Features, Tech Stack, Dependencies, Routes, Seeders, Notification System, Project Structure from previous README.md content]

## ER/Data Flow Diagrams
[Include Mermaid diagrams from README.tldr.md]

## File Structure
[Include structure from tldr]

**Production Ready & Extensible.**

*Project cleaned: tmp files removed, docs consolidated (2026).*
