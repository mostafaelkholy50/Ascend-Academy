# Authorization Rules

- **Global Authorization**: Role-based macro authorization uses `spatie/laravel-permission` (Superadmin, Admin, Teacher, Student, Parent, Accountant, QualityControl).
- **Route Authorization**: Route files (e.g. `routes/admin.php`, `routes/teacher.php`) handle grouping and authentication (`auth` middleware). 
- **Controller-level Authorization**: The project currently relies heavily on Form Requests and Route middleware for authorization rather than traditional Controller Policies (`Gate::authorize()`).
- **Data Scoping**: Services (e.g., `TeacherDashboardService`, `StudentDashboardService`) must manually scope queries to the authenticated user (e.g., `where('teacher_id', auth()->id())`) to prevent IDOR (Insecure Direct Object Reference).

> [!WARNING]
> Since traditional Laravel Policies are not extensively used, developers MUST be extremely careful to scope database queries properly within Repositories and Services.
