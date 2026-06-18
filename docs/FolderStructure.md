# Folder Structure

This document outlines the high-level folder structure of the Ascend-Academy application, explaining the purpose of each key directory.

## Root Directory

- `app/` - The core code of the application.
- `bootstrap/` - Contains the framework bootstrapping files.
- `config/` - All configuration files.
- `database/` - Database migrations, model factories, and seeders.
- `public/` - The web root containing the `index.php` entry point and public assets (CSS, JS, images).
- `resources/` - Uncompiled assets, localization files, and Blade views.
- `routes/` - All route definitions (web, API, console, and role-specific routes).
- `storage/` - Compiled Blade templates, file based sessions, file caches, logs, and user-uploaded files.
- `tests/` - Automated tests (Feature and Unit tests).
- `docs/` - Project Memory and documentation (this directory).

## App Directory Breakdown (`app/`)

- `Console/` - Custom Artisan commands.
- `Filters/` - Query filter classes for advanced searching and filtering.
- `Http/`
  - `Controllers/` - Separated by role (Admin, Teacher, Student, ParentUser, etc.).
  - `Requests/` - Form request classes for validation.
  - `Middleware/` - Custom HTTP middleware.
- `Models/` - Eloquent models representing database tables.
- `Notifications/` - Notification classes (Email, Database, SMS).
- `Providers/` - Service providers to bootstrap application services.
- `Repositories/` - Data access layer classes.
- `Services/` - Business logic layer classes.
- `Traits/` - Reusable PHP traits.
- `View/` - View Composers or components logic.

## Routes Directory Breakdown (`routes/`)

To keep routing manageable, routes are separated by domain/role:
- `admin.php` - Routes for administrators.
- `teacher.php` - Routes for teachers.
- `student.php` - Routes for students.
- `parent.php` - Routes for parents.
- `accountant.php` - Routes for accountants.
- `qualitycontrol.php` - Routes for quality control personnel.
- `superadmin.php` - Routes for super admins.
- `scheduler.php` - Specific scheduling routes.
- `web.php` - General public or non-role specific web routes.
- `console.php` - Artisan console routes.

---
*Note: This document is part of the Project Memory and must be kept updated when the project structure changes.*
