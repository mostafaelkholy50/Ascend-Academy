# Project Architecture

Ascend-Academy follows a modern, robust architecture built on the Laravel framework. The application primarily employs a layered approach to separate concerns and maintain clean, testable, and scalable code.

## Core Architectural Patterns

### 1. MVC (Model-View-Controller)
The foundation of the architecture is the standard Laravel MVC pattern:
- **Models** (`app/Models`): Represent the database entities and their relationships.
- **Views** (`resources/views`): Blade templates rendering the UI for various roles (Admin, Teacher, Student, Parent).
- **Controllers** (`app/Http/Controllers`): Handle incoming HTTP requests, orchestrate business logic execution, and return responses.

### 2. Service-Repository Pattern
To prevent "fat controllers" and encapsulate business logic, the application uses the Service-Repository pattern:
- **Repositories** (`app/Repositories`): Handle all database querying and data access logic. They abstract the Eloquent ORM away from the rest of the application.
- **Services** (`app/Services`): Contain the core business rules and logic. Services call repositories to fetch or persist data. Controllers inject services to perform actions.

### 3. Role-Based Access Control (RBAC)
The application utilizes `spatie/laravel-permission` to manage roles and permissions dynamically. The core roles include:
- `superadmin`
- `admin`
- `teacher`
- `student`
- `parent`
- `accountant`
- `qualitycontrol`

Authorization is handled via Middleware and Policies across the controllers.

### 4. Form Requests (Validation Strategy)
Validation is extracted from controllers into dedicated Form Request classes (`app/Http/Requests`). This ensures that controllers only process valid data.

### 5. Event-Driven Architecture
The application uses Laravel's Event/Listener system to handle decoupled processes (e.g., sending emails upon enrollment or scheduling changes) and Queues (`app/Jobs`) to process background tasks asynchronously.

### 6. Command Query Responsibility Segregation (CQRS) Concept
While not strictly implemented as full CQRS, the separation of read operations (often handled via Filters or complex Repository queries) from write operations (handled by Services and Action classes) leans towards this paradigm, improving maintainability.

## Technology Stack
- **Backend Framework**: Laravel 12 (PHP ^8.2)
- **Frontend**: Blade Templates, Tailwind CSS, Vite for asset building
- **Database**: Relational Database (MySQL/MariaDB via Eloquent)
- **Queue System**: Laravel Queues (Database/Redis driver based on `.env`)

---
*Note: This document is part of the Project Memory and must be kept updated when architectural changes are introduced.*
