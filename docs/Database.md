# Database Architecture

The Ascend-Academy application uses a relational database structure designed to handle users, roles, course enrollments, scheduling, attendance, and reporting.

## Core Entities & Models

### 1. User Management & Access Control
- **`users`**: The central table for all users. Distinguishes users via the `spatie/laravel-permission` roles. Includes fields for `hourly_rate`, `timezone`, and location data.
- **`roles` & `permissions`**: Handled via the Spatie package to manage granular access control.
- **`user_availabilities`**: Tracks when teachers/staff are available.

### 2. Enrollment & Courses
- **`courses`**: Represents the subjects/courses offered.
- **`enrollments`**: Links Users (Students) to Courses. Contains complex scheduling preferences (`schedule_pattern`, `flexible_scheduling`).
- **`enrollment_payments`**: Tracks payments made for specific enrollments.
- **`pricing_tiers`**: Defines pricing rules based on course, duration, and currency (EGP, EUR).

### 3. Scheduling & Attendance
- **`schedules`**: Individual class sessions generated based on enrollments. Tracks status, start/end times.
- **`attendances`**: Records student attendance for specific schedules.
- **`reports`**: Links to attendances to store teacher feedback for the session.

### 4. HR & Teacher Management
- **`teacher_applications`**: Manages recruitment/applications of new teachers.
- **`teacher_hours`**: Tracks hours worked by teachers for payroll calculation.
- **`teacher_evaluations`**: Stores quality control evaluations of teachers.
- **`student_evaluations`**: Stores evaluations of students.

### 5. Other Entities
- **`inquiries`**: Leads or general queries from public users.
- **`books` & `resources`**: Educational materials linked to courses.
- **`news`**: Announcements or blog-style news items.
- **`notifications`**: System-generated database notifications.

## Database Rules

1. **Migrations**: All schema changes must be done via migrations. Never modify the database directly.
2. **Relationships**: Eloquent relationships must be clearly defined in the models (e.g., `hasMany`, `belongsTo`, `belongsToMany`).
3. **Transactions**: Any operation that writes to multiple tables (e.g., creating an enrollment and its first schedule) MUST be wrapped in a database transaction (`DB::transaction()`).
4. **Indexes**: Ensure appropriate indexes are created on frequently queried columns (e.g., `schedules.starts_at`, `enrollments.user_id`) to maintain performance.

---
*Note: This document is part of the Project Memory. Keep it updated when introducing new database tables or relationships.*
