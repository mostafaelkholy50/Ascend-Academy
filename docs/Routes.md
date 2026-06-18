# Route Structure

The Ascend-Academy application explicitly separates routes by role to maintain security and clarity. Routes are loaded via `bootstrap/app.php` into the `web` middleware group.

## Route Files (`routes/`)
- `admin.php`: Administrative endpoints (Dashboard, Users, Enrollments, Schedules).
- `teacher.php`: Teacher portal (My Students, Schedule, Reports, Attendance, Resources).
- `student.php`: Student portal (Schedule, Resources, Evaluations).
- `parent.php`: Parent dashboard (Children overview).
- `accountant.php`: Payroll and payment tracking.
- `qualitycontrol.php`: Evaluations and QC reports.
- `superadmin.php`: System configuration and role management.
- `scheduler.php`: Class scheduling overrides.

## Route Protection
- Every single role-based route is protected by the `auth` middleware inside the respective file (e.g., `Route::middleware(['auth'])->prefix('teacher')`).
- Role authorization (e.g., ensuring a teacher cannot access `admin.php`) is managed either via explicit Spatie middleware within the files or handled downstream.
